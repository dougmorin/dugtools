
# AWS Setup Notes for a Centos 7 LAMP Server

Note: For this instance, I usually use RDS for my database instances so we will NOT be installing the database portion of the LAMP stack.

1. Setup the security group in AWS
    - First, configure ssh to only be allowed to your IP address.
    - Allow HTTP for all.
    - Allow HTTPS for all (if you're going to use https)

2. Update the Server

    ```
    sudo yum update -y
    ```

3. Install the basic projects not included with the build

    ```
    sudo yum install wget htop nano
    ```

4. Shut off SELinux

    ```
    sudo nano /etc/selinux/config
    ```

    Change the following value

    ```
    SELINUX=enforcing
    # to
    SELINUX=disabled
    ```

5. Configure the timezones.  For us, we're in the EST so we use America/New_York.

    ```
    sudo timedatectl set-timezone America/New_York
    sudo yum install ntp -y
    sudo systemctl start ntpd
    sudo systemctl enable ntpd
    ```

6. Install the EPEL Repo through the fedora project

    Taken from https://www.cyberciti.biz/faq/installing-rhel-epel-repo-on-centos-redhat-7-x

    ```
    cd /tmp
    sudo wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
    sudo yum -y install epel-release-latest-7.noarch.rpm
    ```

7. If you want PHP 5.6, use the following instructions.  Currently, by default Centos 7 (for aws) uses PHP 5.4, so this overrides that.

    Taken from https://www.digitalocean.com/community/questions/how-to-install-php-5-6-on-centos-7-0-x64

    ```
    cd /tmp
    wget http://rpms.famillecollet.com/enterprise/remi-release-7.rpm
    sudo rpm -Uvh remi-release-7*.rpm
    ```

    Now we need to make sure that the appropriate repo is enabled within REMI.

    ```
    sudo nano /etc/yum.repos.d/remi.repo
    ```

    In the first section that says `[remi]`, change `enabled=0` to `enabled=1`.  Now, look for the section that is headed with `[remi-php56]`.  In this section, change `enabled=0` to `enabled=1`. Save and exit.

8. Install Apache

    ```
    sudo yum install httpd -y
    sudo systemctl start httpd
    sudo systemctl enable httpd
    ```

9. Install PHP

    ```
    sudo yum install php php-mysql php-gd php-mcrypt php-mbstring mod_ssl -y
    ```

    Modify the php.ini file

    ```
    sudo nano /etc/php.ini
    ```

    Change the following values

    ```
    memory_limit = 512M
    display_errors = Off
    cgi.fix_pathinfo=0
    ```

10. Reboot and make sure everything is functioning properly at this time

    ```
    sudo shutdown -r now
    ```

11. Configure Apache to be a VHOST server

    Add this to the bottom of your http.conf file.  Currently, mine is located at `/etc/httpd/conf/httpd.conf`.

    _Just to note, there are other variables you should probably modify in your httpd config file, but i'll leave that up to you._

    ```
    sudo nano /etc/httpd/conf/httpd.conf
    ```
    ```
    Include /etc/httpd/vhost.d/*.conf
    ```

    Make the new directory, vhost.d.  This will contain all config files for your new virtual sites.

    ```
    sudo mkdir /etc/httpd/vhost.d
    ```

    Now, create your new virtual host file in the new vhost.d folder. For a naming convention, typically you would use the full domain name followed by a .conf extension.  For example, the domain doug.com would become `doug.com.conf`.

    For this example, we'll continue with the site name doug.com.

    Let's not create a new conf file for the domain you're trying to setup on this virtual host server.

    ```
    sudo nano /etc/httpd/vhost.d/doug.com.conf
    ```

    Place the following text inside of the newly created conf file.

    ```
    <VirtualHost *:80>
      ServerName doug.com
      ServerAlias www.doug.com
      DocumentRoot /var/www/vhosts/doug.com/site/public_html
      <Directory /var/www/vhosts/doug.com/site/public_html>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
      </Directory>
      CustomLog /var/log/httpd/doug.com-access.log combined
      ErrorLog /var/log/httpd/doug.com-error.log
      # Possible values include: debug, info, notice, warn, error,
      # crit, alert, emerg.
      LogLevel warn
    </VirtualHost>
    ```

    Before we continue, now is going to be the point where we create the new user to hold the site.  Typically I name it after the site itself, but the name itself is not directly connected to anything specific.

    ```
    sudo adduser doug
    ```

    Next, we need to go ahead and provide a password to connect with.

    ```
    sudo passwd doug
    ```

    You will then be asked to provide a password.

    After you provide a password, you then need to add the new user to the apache group.

    ```
    sudo usermod -aG apache doug
    ```

    Now that we have the user configured, let's go ahead and log in with that user to configure the folder structure and make sure that the user works.

    ```
    su doug
    ```

    Now that you're logged in, go to the home directory for the user and create the two folders required for this project.

    ```
    cd ~/
    mkdir site
    mkdir site/public_html
    ```

    For testing purposes, you should create an html file with anything inside of it inside of the public_html directory.

    ```
    echo 'hello world' > site/public_html/index.html
    ```

    Now that you have created the user and the root web directory for the site, we can move on to the next step, but first you need to log out.

    ```
    exit
    ```

    At this point, you can modify the permission of the newly created user directory to allow the super user to be able to browse.

    ```
    sudo chmod go+rx /home/doug
    ```

    Now, let's create the folder that holds the symbolic site links, and then create the link for our new site.

    ```
    sudo mkdir /var/www/vhosts
    sudo ln -s /home/doug /var/www/vhosts/doug.com
    ```
