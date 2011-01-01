# make craggy_climb.success_id mandatory

alter table craggy_climb drop foreign key craggy_climb_ibfk_4;
alter table craggy_climb change column success_id success_id int(11) not null;
alter table craggy_climb add constraint foreign key (success_id) references craggy_success(id) on delete cascade;
