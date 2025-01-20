# Here we create the database and import the database with the content, the content will be deleted.
cd /
mysql --defaults-extra-file=/var/www/api_/config/mysq.cnf   </var/www/api_/Tests/Scripts/sql/createDatabases.sql
mysql --defaults-extra-file=/var/www/api_/config/mysq.cnf test_db_name </var/www/database/test_db_name.sql
