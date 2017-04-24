#!/bin/bash

# This snippet of code will search through whatever directory you're located
# at in the terminal and convert the files and folders to the appropriate rwx.

find . -type d -exec chmod u=rwx,g=rx,o=rx '{}' \;
find . -type f -exec chmod u=rw,g=r,o=r '{}' \;

exit 0
