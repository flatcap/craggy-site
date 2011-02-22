# a few tidy ups

alter table colour change column abbr abbr text null;
update colour set abbr = null where abbr = "";

alter table route add column notes2 varchar(255) null;
update route set notes2 = notes;
alter table route drop column notes;
alter table route change column notes2 notes varchar(255) null;

alter table rating add column notes2 varchar(255) null;
update rating set notes2 = notes;
alter table rating drop column notes;
alter table rating change column notes2 notes varchar(255) null;

