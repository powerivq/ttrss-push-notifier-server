<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/Html2Text.php');
require_once(__DIR__ . '/gcm/Constants.php');
require_once(__DIR__ . '/gcm/InvalidRequestException.php');
require_once(__DIR__ . '/gcm/Message.php');
require_once(__DIR__ . '/gcm/MulticastResult.php');
require_once(__DIR__ . '/gcm/Result.php');
require_once(__DIR__ . '/gcm/Sender.php');

class zzz_ttrss_push_notifier extends Plugin {

	private $host;
	private $dbh;

	function about() {
		return array(1.0,
			"Send push notifications to Chrome",
			"PowerIVQ");
	}

	function init($host) {
		$this->host = $host;
		$this->dbh = $host->get_dbh();
		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}

	private function remove_spaces($content) {
		return preg_replace(array('/\s+/', '/\s([?.!])/'), array(" ", "$1"), $content);
	}

	private function check_published($id_str) {
		$id = $this->dbh->escape_string($id_str);
		$result = $this->dbh->query("SELECT COUNT(*) FROM ttrss_entries WHERE link='$id' AND plugin_data LIKE '%ttrss_push_notifier%';");
		$ret = $this->dbh->fetch_assoc($result)["COUNT(*)"];
		return $ret;
	}

	private function process_article($content) {
		$html2text = new \Html2Text\Html2Text($content, array('do_links'=>'none'));
		$content = $html2text->getText();
		$content = $this->remove_spaces($content);
		return substr($content, 0, min(300, strlen($content)));
	}

	private function get_icon($content, $base_url) {
		if (preg_match('/src[ \n]*=[ \n]*["\']([^"\']*?\.jpg)["\']/', $content, $match))
			$path = $match[1];
		else if (preg_match('/src[ \n]*=[ \n]*["\']([^"\']*?\.png)["\']/', $content, $match))
			$path = $match[1];
		else return "";
		return $this->get_absolute_path($path, $base_url);
	}

	private function get_absolute_path($path, $base_url) {
		if (preg_match('/^https?:\/\//', $path, $match))
			return $path;
		if ($path[0] == '/') {
			if (preg_match('/^(https?:\/\/.*?)\//', $base_url . '/', $match))
				return $match[1] . $path;
		} else {
			if (preg_match('/^https?:\/\/.*\//', $base_url, $match))
				return $match[0] . $path;
		}
		return "";
	}

	function hook_article_filter($article) {
		if (strpos($article["plugin_data"], "Fetch Failure") !== FALSE || $this->check_published($article["link"]) > 0)
			return $article;

		$data = array(
			'title' => $this->remove_spaces($article["title"]),
			'url' => $article["link"],
			'detail' => $this->process_article($article["content"]),
			'iconUrl' => $this->get_icon($article["content"], $article["link"]),
			'source' => $article["author"]
		);
		global $reg_ids;
		$gcm = new \PHP_GCM\Sender(api_key);
		$msg = new \PHP_GCM\Message('', $data);
		try {
			$result = $gcm->sendNoRetryMulti($msg, $reg_ids);
		} catch (Exception $e) {}
		$article["plugin_data"] .= ",ttrss_push_notifier";
		return $article;
	}

	public function api_version() {
		return 2;
	}
}
?>
