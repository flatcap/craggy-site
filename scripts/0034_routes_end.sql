# invalidate all old routes

# panels 57,58,59,60
update craggy_route set date_end = '2011-01-05' where id in (178,179,180,181,182,183,184,185,186,187,188);

# panels 53,54,55,56
update craggy_route set date_end = '2011-01-06' where id in (164,165,166,167,168,169,170,171,172,173,174,175,176,177);

# panels 50,51,52
update craggy_route set date_end = '2011-01-07' where id in (154,155,156,157,158,159,160,161,162,163);

