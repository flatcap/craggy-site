
CREATE TABLE part_tab
     (  c1 int default NULL,
	c2 varchar(30) default NULL,
	c3 date default NULL
     ) engine=myisam
     PARTITION BY RANGE (year(c3)) (PARTITION p0 VALUES LESS THAN (1995),
     PARTITION p1 VALUES LESS THAN (1996) , PARTITION p2 VALUES LESS THAN (1997) ,
     PARTITION p3 VALUES LESS THAN (1998) , PARTITION p4 VALUES LESS THAN (1999) ,
     PARTITION p5 VALUES LESS THAN (2000) , PARTITION p6 VALUES LESS THAN (2001) ,
     PARTITION p7 VALUES LESS THAN (2002) , PARTITION p8 VALUES LESS THAN (2003) ,
     PARTITION p9 VALUES LESS THAN (2004) , PARTITION p10 VALUES LESS THAN (2010),
     PARTITION p11 VALUES LESS THAN MAXVALUE );

CREATE TABLE no_part_tab
(c1 int(11) default NULL,
 c2 varchar(30) default NULL,
 c3 date default NULL) engine=myisam;

delimiter //
CREATE PROCEDURE load_part_tab()
begin
 declare v int default 0;
	 while v < 8000000
 do
 insert into part_tab
 values (v,'testing partitions',adddate('1995-01-01',(rand(v)*36520) mod 3652));
 set v = v + 1;
 end while;
end
//

delimiter ;
call load_part_tab();

insert into no_part_tab select * from part_tab;


select count(*) from no_part_tab where c3 > date '1995-01-01' and c3 < date '1995-12-31';
select count(*) from part_tab where c3 > date '1995-01-01' and c3 < date '1995-12-31';


