CREATE TABLE testing (
    name VARCHAR(5)
) COMMENT='this is testing';

ALTER TABLE tablename COMMENT = 'new updated comment';

SHOW TABLE STATUS [WHERE Name = '...']



“COMMENT” is just an option you can add for each column as well as at the end of the table when creating a table:

CREATE TABLE boo (
id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'The KEY obviously',
.
.

Updating comments is not so user friendly, in that it requires you to repeat the whole column description including the column name, as in

ALTER TABLE myTable CHANGE COLUMN myColumn
myColumn BIGINT NOT NULL COMMENT 'This is the most important primary key ever';

Just like for table comments, you can see the column comments if you show the CREATE TABLE:

SHOW CREATE TABLE myTable;


mysql> use information_schema;
mysql> show tables;
mysq> select column_name, column_comment from columns where table_name='your-table';

