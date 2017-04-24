#!/bin/bash

# This script fixed an error I was having when MySQL workbench failed
# to run import scripts.

if [ ! -e /tmp/mysql.sock ]; then
  sudo ln -s /Applications/MAMP/tmp/mysql/mysql.sock /tmp/mysql.sock
  echo "... mysql link created"
fi
