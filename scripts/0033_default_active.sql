# craggy_rating.active should default to 1

alter table craggy_rating change column active active tinyint(1) default 1;
