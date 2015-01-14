<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/config.php');

global $reg_ids;
if ($_POST['test_regid']) {
	echo in_array($_POST['test_regid'], $reg_ids) ? 'ok' : 'nok';
	return;
}
if (!$_POST['reg_id'] || !in_array($_POST['reg_id'], $reg_ids)) return;
$conn = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());
mysql_select_db(DB_NAME, $conn) or die(mysqL_error());
$user = mysql_real_escape_string($_POST['user'], $conn);
$url = mysql_real_escape_string($_POST['url'], $conn);
mysql_query("UPDATE ttrss_user_entries JOIN ttrss_entries ON link='$url' AND ttrss_entries.id=ttrss_user_entries.ref_id " .
            "JOIN ttrss_users ON login='$user' AND ttrss_users.id=ttrss_user_entries.owner_uid SET unread=0;", $conn);
echo mysql_affected_rows($conn);
?>
