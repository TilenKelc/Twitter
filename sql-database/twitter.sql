/*
Created		4. 09. 2019
Modified		10. 09. 2019
Project		
Model		
Company		
Author		
Version		
Database		mySQL 5 
*/


Create table users (
	id Serial NOT NULL,
	username Varchar(50) NOT NULL,
	joined Date NOT NULL,
	bio Text,
	location Varchar(100),
	born Date,
	avatar Varchar(255),
	password Varchar(60) NOT NULL,
	email Varchar(100) NOT NULL,
	type_id Bigint UNSIGNED NOT NULL,
 Primary Key (id),
 Foreign Key (type_id) references types (id) on delete  restrict on update  restrict
) ENGINE = MyISAM;

Create table tweets (
	id Serial NOT NULL,
	user_id Bigint UNSIGNED NOT NULL,
	picture Varchar(255),
	text Text NOT NULL,
	time Timestamp NOT NULL,
	likes Int NOT NULL DEFAULT 0,
	like_id Int,
 Primary Key (id),
 Foreign Key (user_id) references users (id) on delete  restrict on update  restrict
) ENGINE = MyISAM;

Create table replies (
	id Serial NOT NULL,
	user_id Bigint UNSIGNED NOT NULL,
	reply Text NOT NULL,
	date Timestamp NOT NULL,
	tweet_id Bigint UNSIGNED NOT NULL,
 Primary Key (id),
 Foreign Key (tweet_id) references tweets (id) on delete  restrict on update  restrict,
 Foreign Key (user_id) references users (id) on delete  restrict on update  restrict
) ENGINE = MyISAM;

Create table types (
	id Serial NOT NULL,
	user_type Varchar(50) NOT NULL,
 Primary Key (id)
) ENGINE = MyISAM;

Create table friends (
	id Serial NOT NULL,
	user_id Bigint UNSIGNED NOT NULL,
	state Varchar(50) NOT NULL,
	friend_id Int NOT NULL,
 Primary Key (id),
 Foreign Key (user_id) references users (id) on delete  restrict on update  restrict
) ENGINE = MyISAM;


