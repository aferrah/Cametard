CREATE TABLE Camions(
   immat VARCHAR(10),
   type_camion VARCHAR(20) CHECK(type_camion IN ('frigo', 'citerne', 'palette', 'plateau')),
   poids_transport DECIMAL(15,2) CHECK(poids_transport > 0),
   CONSTRAINT PK_Camions PRIMARY KEY(immat)
);

CREATE TABLE Chauffeurs(
   numero_permis VARCHAR(20),
   nom VARCHAR(50),
   prenom VARCHAR(50),
   CONSTRAINT PK_Chauffeurs PRIMARY KEY(numero_permis)
);

CREATE TABLE Cargaisons(
   id_cargaison INT AUTO_INCREMENT,
   date_transport DATE,
   ville_depart VARCHAR(50),
   ville_arrivee VARCHAR(50),
   immat VARCHAR(10) NOT NULL,
   numero_permis VARCHAR(20) NOT NULL,
   CONSTRAINT PK_Cargaisons PRIMARY KEY(id_cargaison),
   CONSTRAINT FK_Cargaisons_Camions FOREIGN KEY(immat) REFERENCES Camions(immat) ON DELETE CASCADE,
   CONSTRAINT FK_Cargaisons_Chauffeurs FOREIGN KEY(numero_permis) REFERENCES Chauffeurs(numero_permis) ON DELETE CASCADE
);

CREATE TABLE Marchandises(
   id_marchandise INT AUTO_INCREMENT,
   nom VARCHAR(50),
   type_requis VARCHAR(20) CHECK(type_requis IN ('frigo', 'citerne', 'palette', 'plateau')),
   poids DECIMAL(15,2) CHECK(poids > 0),
   id_cargaison INT NOT NULL,
   CONSTRAINT PK_Marchandises PRIMARY KEY(id_marchandise),
   CONSTRAINT FK_Marchandises_Cargaisons FOREIGN KEY(id_cargaison) REFERENCES Cargaisons(id_cargaison) ON DELETE CASCADE
);

CREATE TABLE Logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) CHECK (role IN ('chauffeur', 'logisticien', 'admin'))
);


INSERT INTO Camions (immat, type_camion, poids_transport) VALUES
('AB-123-CD', 'frigo', 18000.00),
('XY-456-ZZ', 'citerne', 25000.00),
('EF-789-GH', 'palette', 12000.00),
('LM-345-NO', 'plateau', 15000.00);

INSERT INTO Chauffeurs (numero_permis, nom, prenom) VALUES
('PERM123456', 'Durand', 'Jean'),
('PERM654321', 'Martin', 'Sophie'),
('PERM111222', 'Leroy', 'Thomas'),
('PERM333444', 'Dupont', 'Alice');

INSERT INTO Cargaisons (date_transport, ville_depart, ville_arrivee, immat, numero_permis) VALUES
('2024-03-10', 'Paris', 'Lyon', 'AB-123-CD', 'PERM123456'),
('2024-03-10', 'Marseille', 'Bordeaux', 'XY-456-ZZ', 'PERM654321'),
('2024-03-11', 'Lille', 'Nantes', 'EF-789-GH', 'PERM111222'),
('2024-03-11', 'Nice', 'Toulouse', 'LM-345-NO', 'PERM333444');

INSERT INTO Marchandises (nom, type_requis, poids, id_cargaison) VALUES
('Pommes', 'frigo', 16000.00, 1),
('Carburant', 'citerne', 24000.00, 2),
('Meubles', 'palette', 10000.00, 3),
('Béton', 'plateau', 14000.00, 4),
('Poissons', 'frigo', 2000.00, 1);



--Essai d’insertion d’un camion avec un poids invalide (doit échouer)

INSERT INTO Camions (immat, type_camion, poids_transport) VALUES
('ZZ-999-AA', 'frigo', -5000.00);  -- Échoue car poids_transport < 0

--Essai d’insertion d’une cargaison avec un camion inexistant (doit échouer)

INSERT INTO Cargaisons (date_transport, ville_depart, ville_arrivee, immat, numero_permis) VALUES
('2024-03-12', 'Paris', 'Lyon', 'XX-000-YY', 'PERM123456');  -- Échoue car XX-000-YY n'existe pas dans Camions

--Essai d’insertion d’une marchandise avec un type de camion invalide (doit échouer)

INSERT INTO Marchandises (nom, type_requis, poids, id_cargaison) VALUES
('Téléviseurs', 'camion-benne', 5000.00, 1);  -- Échoue car 'camion-benne' n'est pas un type valide



--Liste des marchandises triées par ville de départ et par poids

SELECT nom, type_requis, poids, ville_depart
FROM Marchandises
NATURAL JOIN Cargaisons
ORDER BY ville_depart ASC, poids DESC;

--Liste des camions de type frigo avec un poids total de marchandises > 15 tonnes

SELECT immat, type_camion, SUM(poids) AS poids_total
FROM Camions
NATURAL JOIN Cargaisons
NATURAL JOIN Marchandises 
WHERE type_camion = 'frigo'
GROUP BY immat, type_camion
HAVING SUM(poids) > 15000;

--Liste des chauffeurs non affectés à un camion le 1/4/2021

SELECT numero_permis, nom, prenom
FROM Chauffeurs
WHERE numero_permis NOT IN (
    SELECT numero_permis
    FROM Cargaison
    WHERE date_transport = '2021-04-01'
);

--Nombre de jours d’utilisation par camion

SELECT immat, COUNT(DISTINCT date_transport) AS jours_utilisation
FROM Camions
NATURAL JOIN Cargaisons
GROUP BY immat;

--La ville qui a le plus de livraisons de cargaisons

SELECT ville_arrivee, COUNT(*) AS nombre_livraisons
FROM Cargaisons
GROUP BY ville_arrivee
ORDER BY nombre_livraisons DESC
LIMIT 1;



