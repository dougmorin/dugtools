#!/bin/bash

# Help menu
print_help() {
cat <<-HELP
This script is used to fix permissions of a Wordpress installation
you need to provide the following arguments:

  1) Path to your Wordpress installation.
  2) Username of the user that you want to give uploads/upgrade/cache ownership.
  3) HTTPD group name (defaults to www-data for Apache).

Usage: (sudo) bash ${0##*/} --wordpress_path=PATH --wordpress_user=USER --httpd_group=GROUP
Example: (sudo) bash ${0##*/} --wordpress_path=/usr/local/apache2/htdocs --wordpress_user=john --httpd_group=www-data
HELP
exit 0
}

if [ $(id -u) != 0 ]; then
  printf "**************************************\n"
  printf "* Error: You must run this with or root*\n"
  printf "**************************************\n"
  print_help
  exit 1
fi

wordpress_path=${1%/}
wordpress_user=${2}
httpd_group="${3:-www-data}"

# Parse Command Line Arguments
while [ "$#" -gt 0 ]; do
  case "$1" in
    --wordpress_path=*)
        wordpress_path="${1#*=}"
        ;;
    --wordpress_user=*)
        wordpress_user="${1#*=}"
        ;;
    --httpd_group=*)
        httpd_group="${1#*=}"
        ;;
    --help) print_help;;
    *)
      printf "***********************************************************\n"
      printf "* Error: Invalid argument, run --help for valid arguments. *\n"
      printf "***********************************************************\n"
      exit 1
  esac
  shift
done

if [ -z "${wordpress_path}" ] || [ ! -d "${wordpress_path}/wp-admin" ] || [ ! -f "${wordpress_path}/wp-load.php" ] ; then
  printf "*********************************************\n"
  printf "* Error: Please provide a valid Wordpress path. *\n"
  printf "*********************************************\n"
  print_help
  exit 1
fi

if [ -z "${wordpress_user}" ] || [[ $(id -un "${wordpress_user}" 2> /dev/null) != "${wordpress_user}" ]]; then
  printf "*************************************\n"
  printf "* Error: Please provide a valid user. *\n"
  printf "*************************************\n"
  print_help
  exit 1
fi

cd $wordpress_path
printf "Changing ownership of all contents of "${wordpress_path}":\n user => "${wordpress_user}" \t group => "${httpd_group}"\n"
chown -R ${wordpress_user}:${httpd_group} .

printf "Changing permissions of all directories inside "${wordpress_path}" to "rwxr-x---"...\n"
find . -type d -exec chmod u=rwx,g=rx,o= '{}' \;

printf "Changing permissions of all files inside "${wordpress_path}" to "rw-r-----"...\n"
find . -type f -exec chmod u=rw,g=r,o= '{}' \;

printf "Changing permissions of "uploads/upgrade/cache" directories in "${wordpress_path}/wp-content" to "rwxrwx---"...\n"
cd wp-content
find . -type d -name uploads -exec chmod ug=rwx,o= '{}' \;
find . -type d -name upgrade -exec chmod ug=rwx,o= '{}' \;
find . -type d -name cache -exec chmod ug=rwx,o= '{}' \;

printf "Changing permissions of all files inside all "uploads/upgrade/cache" directories in "${wordpress_path}/wp-content/uploads" to "rw-rw----"...\n"
printf "Changing permissions of all directories inside all "uploads/upgrade/cache" directories in "${wordpress_path}/wp-content/uploads" to "rwxrwx---"...\n"
for x in ./uploads; do
  find ${x} -type d -exec chmod ug=rwx,o= '{}' \;
  find ${x} -type f -exec chmod ug=rw,o= '{}' \;
done

for x in ./upgrade; do
  find ${x} -type d -exec chmod ug=rwx,o= '{}' \;
  find ${x} -type f -exec chmod ug=rw,o= '{}' \;
done

for x in ./cache; do
  find ${x} -type d -exec chmod ug=rwx,o= '{}' \;
  find ${x} -type f -exec chmod ug=rw,o= '{}' \;
done
echo "Done setting proper permissions on files and directories"