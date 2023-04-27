DROP DATABASE IF EXISTS Oleksiewicz_Pizzeria;
CREATE DATABASE Oleksiewicz_Pizzeria;

USE Oleksiewicz_Pizzeria;
CREATE TABLE userdata (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username varchar(50) NOT NULL,
	password varchar(50) NOT NULL,
	email varchar(50) NOT NULL,
	role_id tinyint NOT NULL DEFAULT '1'
);

CREATE TABLE roles (
    id tinyint NOT NULL PRIMARY KEY,
	role_name varchar(20) NOT NULL
);

CREATE TABLE pizza_order (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id int NOT NULL,
	pizza_id int NOT NULL,
	price int NOT NULL
);

CREATE TABLE pizza (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	description TEXT NOT NULL,
	price tinyint NOT NULL,
	deleted boolean NOT NULL DEFAULT '0'
);

CREATE TABLE pizza_history (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id int NOT NULL,
	pizza_preparation varchar(20) DEFAULT '-',
	pizza_delivery varchar(20) DEFAULT '-',
	pizza_order varchar(20) DEFAULT '-'
);

CREATE TABLE messages (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	sender_user_id int NOT NULL,
	addressee_user_id int,
	sender_text text NOT NULL,
	addressee_text text
);

ALTER TABLE pizza_order ADD CONSTRAINT user_pizza FOREIGN KEY (user_id) REFERENCES userdata(id);
ALTER TABLE pizza_order ADD CONSTRAINT pizza_order FOREIGN KEY (pizza_id) REFERENCES pizza(id);
ALTER TABLE userdata ADD CONSTRAINT user_role FOREIGN KEY (role_id) REFERENCES roles(id);

INSERT INTO roles (id, role_name) VALUES (1, 'user');
INSERT INTO roles (id, role_name) VALUES (2, 'admin');
INSERT INTO roles (id, role_name) VALUES (3, 'deliverer');

INSERT INTO userdata (id, username, password, email, role_id) VALUES (NULL, 'admin', 'g033h22dh348dhe5660if2140dhf35850f4gd997', 'admin@wp.pl', 2);
INSERT INTO userdata (id, username, password, email, role_id) VALUES (NULL, 'deliverer', '855d1ddid4fd6g8i5ded6ed09173482301g2ih87', 'deliverer@wp.pl', 3);
INSERT INTO pizza (id, description, price) VALUES (NULL, 'Margherrita', 30);
INSERT INTO pizza (id, description, price) VALUES (NULL, 'Romana', 40);
INSERT INTO pizza (id, description, price) VALUES (NULL, 'Hawajska', 34);
INSERT INTO pizza (id, description, price) VALUES (NULL, 'Capicciosa', 32);