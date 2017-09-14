#!/bin/bash

# Export a database
mysqldump -u <user> -p <database_name> > <file_path>/<file>

# Import a database
mysql -u <user> -p <database_name> < <file_path>/<file>
