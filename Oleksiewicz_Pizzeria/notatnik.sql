DROP DATABASE IF EXISTS Oleksiewicz_Pizzeria;
CREATE DATABASE Oleksiewicz_Pizzeria;

USE Oleksiewicz_Pizzeria;
CREATE TABLE userdata (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username varchar(50) NOT NULL,
	password varchar(50) NOT NULL,
	email varchar(50) NOT NULL,
	admin BOOLEAN NOT NULL DEFAULT '0'
);

CREATE TABLE pizza_history (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id int NOT NULL,
	pizza_id int NOT NULL,
	price int NOT NULL,
	order_time int NOT NULL,
	force_status BOOLEAN NOT NULL DEFAULT '0'
);

CREATE TABLE pizza (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	description TEXT NOT NULL,
	price tinyint NOT NULL
);

ALTER TABLE pizza_history ADD CONSTRAINT user_pizza FOREIGN KEY (user_id) REFERENCES userdata(id);
ALTER TABLE pizza_history ADD CONSTRAINT pizza_hisotry FOREIGN KEY (pizza_id) REFERENCES pizza(id);


INSERT INTO userdata (id, username, password, email, admin) VALUES (NULL, 'admin', 'g033h22dh348dhe5660if2140dhf35850f4gd997', 'admin@wp.pl', 1);
INSERT INTO pizza (id, description, price) VALUES (NULL, 'Margherrita', 30);
INSERT INTO pizza (id, description, price) VALUES (NULL, 'Romana', 40);
INSERT INTO pizza (id, description, price) VALUES (NULL, 'Hawajska', 34);
INSERT INTO pizza (id, description, price) VALUES (NULL, 'Capicciosa', 32);

