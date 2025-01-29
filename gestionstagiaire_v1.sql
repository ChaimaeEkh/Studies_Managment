CREATE DATABASE gestionstagiaire_v1;
USE gestionstagiaire_v1;

CREATE TABLE compteadministrateur (
    idAdmin INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    motDePasse VARCHAR(255) NOT NULL
);

CREATE TABLE filiere (
    idFiliere INT AUTO_INCREMENT PRIMARY KEY,
    intitule VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE stagiaire (
    idStagiaire INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    dateNaissance DATE NOT NULL,
    photoProfil VARCHAR(255) NULL,
    idFiliere INT NOT NULL,
    FOREIGN KEY (idFiliere) REFERENCES filiere(idFiliere) ON DELETE CASCADE
);
