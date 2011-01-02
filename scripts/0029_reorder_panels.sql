# reorder panel ids (for neatness)

set foreign_key_checks = 0;

update craggy_panel set id = 99 where id = 86;

update craggy_panel set id = 86 where id = 85;
update craggy_panel set id = 85 where id = 84;
update craggy_panel set id = 84 where id = 83;
update craggy_panel set id = 83 where id = 82;
update craggy_panel set id = 82 where id = 81;
update craggy_panel set id = 81 where id = 80;
update craggy_panel set id = 80 where id = 79;
update craggy_panel set id = 79 where id = 78;
update craggy_panel set id = 78 where id = 77;
update craggy_panel set id = 77 where id = 76;
update craggy_panel set id = 76 where id = 75;
update craggy_panel set id = 75 where id = 74;
update craggy_panel set id = 74 where id = 73;
update craggy_panel set id = 73 where id = 72;
update craggy_panel set id = 72 where id = 71;
update craggy_panel set id = 71 where id = 70;
update craggy_panel set id = 70 where id = 69;

update craggy_panel set id = 69 where id = 99;

-- -------------------------------------------------------

update craggy_route set panel_id = 99 where panel_id = 86;

update craggy_route set panel_id = 86 where panel_id = 85;
update craggy_route set panel_id = 85 where panel_id = 84;
update craggy_route set panel_id = 84 where panel_id = 83;
update craggy_route set panel_id = 83 where panel_id = 82;
update craggy_route set panel_id = 82 where panel_id = 81;
update craggy_route set panel_id = 81 where panel_id = 80;
update craggy_route set panel_id = 80 where panel_id = 79;
update craggy_route set panel_id = 79 where panel_id = 78;
update craggy_route set panel_id = 78 where panel_id = 77;
update craggy_route set panel_id = 77 where panel_id = 76;
update craggy_route set panel_id = 76 where panel_id = 75;
update craggy_route set panel_id = 75 where panel_id = 74;
update craggy_route set panel_id = 74 where panel_id = 73;
update craggy_route set panel_id = 73 where panel_id = 72;
update craggy_route set panel_id = 72 where panel_id = 71;
update craggy_route set panel_id = 71 where panel_id = 70;
update craggy_route set panel_id = 70 where panel_id = 69;

update craggy_route set panel_id = 69 where panel_id = 99;

set foreign_key_checks = 1;

