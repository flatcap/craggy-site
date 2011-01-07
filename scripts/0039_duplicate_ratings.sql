# get rid of duplicate ratings

update craggy_rating set difficulty_id=3 where id=297;
update craggy_rating set difficulty_id=3 where id=295;
update craggy_rating set difficulty_id=4 where id=270;
update craggy_rating set difficulty_id=4 where id=271;
update craggy_rating set difficulty_id=3 where id=272;
update craggy_rating set difficulty_id=4 where id=268;
update craggy_rating set difficulty_id=4 where id=269;
update craggy_rating set difficulty_id=4 where id=267;
update craggy_rating set difficulty_id=4 where id=264;
update craggy_rating set difficulty_id=4 where id=265;
update craggy_rating set difficulty_id=2 where id=266;
update craggy_rating set difficulty_id=3 where id=259;
update craggy_rating set difficulty_id=3 where id=260;
update craggy_rating set difficulty_id=3 where id=262;
update craggy_rating set difficulty_id=3 where id=263;
update craggy_rating set difficulty_id=4 where id=315;
update craggy_rating set difficulty_id=4 where id=283;
update craggy_rating set difficulty_id=4 where id=250;
update craggy_rating set difficulty_id=4 where id=251;
update craggy_rating set difficulty_id=4 where id=252;
update craggy_rating set difficulty_id=4 where id=229;
update craggy_rating set difficulty_id=4 where id=208;
update craggy_rating set difficulty_id=4 where id=230;
update craggy_rating set difficulty_id=4 where id=231;
update craggy_rating set difficulty_id=4 where id=232;
update craggy_rating set difficulty_id=4 where id=253;
update craggy_rating set difficulty_id=3 where id=234;
update craggy_rating set difficulty_id=4 where id=254;
update craggy_rating set difficulty_id=3 where id=255;
update craggy_rating set difficulty_id=3 where id=256;
update craggy_rating set difficulty_id=4 where id=258;
update craggy_rating set difficulty_id=4 where id=257;

delete from craggy_rating where id in (296,294,73,74,75,79,80,81,94,95,96,140,141,143,144,246,169,204,205,206,207,203,209,210,211,212,213,214,216,215,217,218,226,227,228,233,235,236,237);

