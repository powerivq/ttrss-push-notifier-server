# TTRSS Push Notifier Server
This is the server part of the TTRSS Push Notifier.

## How to install:

1. Install the Chrome extension <a target="_blank" href="https://chrome.google.com/webstore/detail/ttrss-push-notifier/clpkfcceimiehegmkehfildimbkagmic">here</a>.

2. Click on the extension button. Fill all blanks and click apply.

3. Copy the registration id at the bottom of the extension popup page. (Repeat step 1-3 if you have more than one computer)

2. Download <a target="_blank" href="https://github.com/powerivq/ttrss-push-notifier-server/archive/master.zip">this</a> zip archive to your ttrss server.

3. Unzip it to /your_ttrss_directory/plugins/.

4. Open /your_ttrss_directory/plugins/zzz_ttrss_push_notifier/config.php on a text editor.

5. If you have only one computer, replace the first string in $reg_ids with your registration id obtained on step 3. If you have more, insert all registration ids afterwards as an array.

6. You are done! You can confirm all setting are right by clicking the extension button and see the status line.
