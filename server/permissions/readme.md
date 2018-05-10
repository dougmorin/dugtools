# Server website docroot permissioning automation

## TO-DO

	- Create a flat html site permissioning tool.
	- Create a react/node permissioning tool.
	- Roll each one of these tools into one file with variable triggers.

## Notes:

The following files have been taken from https://www.drupal.org/node/244924 and modified to fit my needs.  I converted it from being a "drupal only" permissioning tool to being for wordpress OR drupal.

### Setup instructions

If you have sufficient privileges on your server

    1. Place the file in /usr/local/bin 
    2. `sudo chown root:root /usr/local/bin/fixpermissions-{wordpress/drupal}`
    3. `sudo vi /etc/sudoers.d/fix-permissions` and enter the following line in the file
    4. `user1, user2 ALL = (root) NOPASSWD: /usr/local/bin/fixpermissions-{wordpress/drupal}`
    5. Save the file and then `sudo chmod 0440 /etc/sudoers.d/fix-permissions`

Note: Substitute your desired comma separated list of users where you see user1, user2 above. Alternatively, you could enter an ALIAS for a user list. Run man sudoers for more information on formatting the line.

What the /etc/sudoers.d/fix-permissions accomplishes is making the script available to a set of users via the sudo command without having to enter a password.

### My `/etc/sudoers.d/fix-permissions` file

The user has been sanitized, of course.  I put both of my files in the same folder.

```
user1 ALL = (root) NOPASSWD: /usr/local/bin/fixpermissions-drupal
user1 ALL = (root) NOPASSWD: /usr/local/bin/fixpermissions-wordpress
```

### Call the scripts

Once this is all setup, you can run the following commands from the user you setup to run in the sudoer file.

#### For Wordpress
```
bash fixpermissions-wordpress \
--wordpress_root=/home/site/public_html \
--wordpress_user=site \
--httpd_group=apache
```

#### For Drupal
```
bash fixpermissions-drupal \
--drupal_root=/home/site/public_html \
--drupal_user=site \
--httpd_group=apache
```
