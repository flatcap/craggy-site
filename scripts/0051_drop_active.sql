# drop the active column from the climb and rating tables

alter table climb  drop column active;
alter table rating drop column active;

