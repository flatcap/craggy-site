
show create table craggy_route \G

*************************** 1. row ***************************
       Table: craggy_route
Create Table: CREATE TABLE `craggy_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `panel_id` int(11) NOT NULL,
  `colour_id` int(11) NOT NULL,
  `grade_id` int(11) NOT NULL,
  `notes` text,
  `setter_id` int(11) NOT NULL,
  `date_set` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grade_id` (`grade_id`),
  KEY `panel_id` (`panel_id`),
  KEY `setter_id` (`setter_id`),
  KEY `colour_id` (`colour_id`),
  CONSTRAINT `craggy_route_ibfk_1` FOREIGN KEY (`grade_id`) REFERENCES `craggy_grade` (`id`),
  CONSTRAINT `craggy_route_ibfk_2` FOREIGN KEY (`panel_id`) REFERENCES `craggy_panel` (`id`),
  CONSTRAINT `craggy_route_ibfk_3` FOREIGN KEY (`setter_id`) REFERENCES `craggy_setter` (`id`),
  CONSTRAINT `craggy_route_ibfk_4` FOREIGN KEY (`colour_id`) REFERENCES `craggy_colour` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=utf8

