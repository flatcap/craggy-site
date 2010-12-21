create trigger general_comments_modified
before insert or update on general_comments
for each row
begin
 :new.modified_date := sysdate;
end;
/
show errors


mysql> desc general_log;
+--------------+------------------+------+-----+-------------------+-----------------------------+
| Field        | Type             | Null | Key | Default           | Extra                       |
+--------------+------------------+------+-----+-------------------+-----------------------------+
| event_time   | timestamp        | NO   |     | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
| user_host    | mediumtext       | NO   |     | NULL              |                             |
| thread_id    | int(11)          | NO   |     | NULL              |                             |
| server_id    | int(10) unsigned | NO   |     | NULL              |                             |
| command_type | varchar(64)      | NO   |     | NULL              |                             |
| argument     | mediumtext       | NO   |     | NULL              |                             |
+--------------+------------------+------+-----+-------------------+-----------------------------+

