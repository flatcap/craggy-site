# null for empty dates

update craggy_route set date_set = null where date_set = '0000-00-00';
