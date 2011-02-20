# add abbr column to difficulty table
alter table difficulty add column abbr tinytext;

update difficulty set abbr = "ve" where id = 1;
update difficulty set abbr = "vh" where id = 5;
