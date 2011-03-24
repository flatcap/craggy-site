# change the height field to a float
alter table panel add column height2 float after height;
update panel set height2 = height / 100;
alter table panel drop column height;
alter table panel change column height2 height float;
