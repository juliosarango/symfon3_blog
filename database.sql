/**
 * Author:  jsarangoq
 * Created: 31/03/2016
 */

create database if not exists blog;

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

create table categories(
id serial not null,
name varchar(255),
description text,
constraint pk_categories primary key(id)

);

create table tags(
id serial not null,
name varchar(255),
description text,
constraint pk_tags primary key(id)
);

create table entries(
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
references categories(id)
);

create table entry_tag(
id serial not null,
entry_id integer not null,
tag_id integer not null
constraint pk_entry_tag(id),
constraint fk_entry_tag_entries foreign key (entry_id)
references entries(id),
constraint fk_entry_tag_tags foreign key (tag_id)
references tags(id),
);

select * from users;

