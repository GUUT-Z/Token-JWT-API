CREATE DATABASE IF NOT EXISTS api CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE api;
CREATE TABLE categorie (
num INT AUTO_INCREMENT PRIMARY KEY,
nomcat VARCHAR(100) NOT NULL
);
CREATE TABLE produit (
id INT AUTO_INCREMENT PRIMARY KEY,
nom VARCHAR(100) NOT NULL,
description VARCHAR(255),
prix DECIMAL(10,2) NOT NULL,
numCategorie INT NOT NULL,
dateCreation DATE NOT NULL,
dateModification DATE NOT NULL,
FOREIGN KEY (numCategorie) REFERENCES categorie(num)
);
INSERT INTO categorie (num, nomcat) VALUES
(1, 'papeterie'),
(2, 'ordinateur'),
(3, 'voiture');
INSERT INTO produit (id, nom, description, prix, numCategorie, dateCreation,
dateModification) VALUES
(1, 'Stylo', 'Stylo bleu', 1.23, 1, '2021-07-04', '2021-09-03'),
(2, 'PC', 'Asus', 300.99, 2, '2019-01-01', '2019-05-14'),
(3, 'voiture', 'Mercedes benz', 985470.47, 3, '2019-02-06', '2019-06-23');