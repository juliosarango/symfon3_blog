/**
 * Author:  jsarangoq
 * Created: 31/03/2016
 */

/*create database if not exists blog;*/

create table users(
id serial not null,
role varchar(20) not null,
name varchar(255),
surname varchar(255),
email varchar(255),
password varchar(255),
imagen varchar(255),
constraint pk_users primary key(id)
);

create table category(
id serial not null,
name varchar(255),
description text,
constraint pk_category primary key(id)

);

create table tag(
id serial not null,
name varchar(255),
description text,
constraint pk_tag primary key(id)
);

create table entry(
id serial not null,
user_id integer not null,
category_id integer not null,
title varchar(255),
content text,
status varchar(20),
image varchar(255),
constraint pk_entries primary key(id),
constraint fk_entries_users foreign key (user_id)
references users(id),
constraint fk_entries_categories foreign key (category_id)
references category(id)
);

create table entry_tag(
id serial not null,
entry_id integer not null,
tag_id integer not null,
constraint pk_entry_tag primary key(id),
constraint fk_entry_tag_entries foreign key (entry_id)
references entry(id),
constraint fk_entry_tag_tags foreign key (tag_id)
references tag(id)
);


insert into category(name,description) values ('Desarrollo Web','Categoria de Desarrollo Web');
insert into category(name,description) values ('Desarrollo Android','Categoria de Desarrollo Android');

insert into tag(name,description) values ('php','php tag');
insert into tag(name,description) values ('symfony','symfony tag');
insert into tag(name,description) values ('html','html tag');
insert into tag(name,description) values ('zend framework 2','apuntate al curso de zend framework 2');

insert into users(role, name, surname, email, password, imagen)values('admin','victor','robles','victor@victor.com','123456','')
insert into users(role, name, surname, email, password, imagen)values('user','juan','lopez','juan@juan.com','123456','')

insert into entry (user_id,category_id, title, content,status )values (1,1, 'entrada de desarrollo con php','Duis lorem semper, eu lacinia orci convallis. Vestibulum finibus massa a lacus ultricies faucibus? Donec neque libero, mattis in mollis sit amet, pulvinar vel tortor. Integer vel eros sit amet mi lacinia commodo id id tortor. Donec augue quam, convallis vitae mauris non, hendrerit volutpat ligula. Maecenas sed viverra arcu! Maecenas quis ligula luctus, aliquam enim suscipit, ultricies risus.','public');
insert into entry (user_id,category_id, title, content,status )values (2,2, 'entrada de desarrollo con android','Duis lorem semper, eu lacinia orci convallis. Vestibulum finibus massa a lacus ultricies faucibus? Donec neque libero, mattis in mollis sit amet, pulvinar vel tortor. Integer vel eros sit amet mi lacinia commodo id id tortor. Donec augue quam, convallis vitae mauris non, hendrerit volutpat ligula. Maecenas sed viverra arcu! Maecenas quis ligula luctus, aliquam enim suscipit, ultricies risus.','public');
insert into entry (user_id,category_id, title, content,status )values (1,1, 'Desarrollo de Webapps con symfony3','Duis lorem semper, eu lacinia orci convallis. Vestibulum finibus massa a lacus ultricies faucibus? Donec neque libero, mattis in mollis sit amet, pulvinar vel tortor. Integer vel eros sit amet mi lacinia commodo id id tortor. Donec augue quam, convallis vitae mauris non, hendrerit volutpat ligula. Maecenas sed viverra arcu! Maecenas quis ligula luctus, aliquam enim suscipit, ultricies risus.','public');
insert into entry (user_id,category_id, title, content,status )values (1,1, 'Desarrollo de apis rest con slim','Duis lorem semper, eu lacinia orci convallis. Vestibulum finibus massa a lacus ultricies faucibus? Donec neque libero, mattis in mollis sit amet, pulvinar vel tortor. Integer vel eros sit amet mi lacinia commodo id id tortor. Donec augue quam, convallis vitae mauris non, hendrerit volutpat ligula. Maecenas sed viverra arcu! Maecenas quis ligula luctus, aliquam enim suscipit, ultricies risus.','public');

insert into entry_tag (entry_id, tag_id) values (1,2);
insert into entry_tag (entry_id, tag_id) values (1,1);
insert into entry_tag (entry_id, tag_id) values (2,1);
insert into entry_tag (entry_id, tag_id) values (2,3);
insert into entry_tag (entry_id, tag_id) values (3,2);
insert into entry_tag (entry_id, tag_id) values (1,4);


