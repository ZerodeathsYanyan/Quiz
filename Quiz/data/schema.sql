CREATE TABLE users(
	user_id int PRIMARY KEY AUTO_INCREMENT,
    userType varchar(10) not null,
    username varchar(30) not null UNIQUE,
    password varchar(255) not null
);

ALTER TABLE users AUTO_INCREMENT = 1000;
