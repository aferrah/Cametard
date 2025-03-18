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
    password_hash VARCHAR(255) NOT NULL
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

INSERT INTO Logins (username, password_hash) VALUES
('chauffeur1', SHA2('motdepasse1', 256)),
('logisticien1', SHA2('motdepasse2', 256)),
('admin1', SHA2('adminpass', 256));


--Essai d’insertion d’un camion avec un poids invalide (doit échouer)
INSERT INTO Camions (immat, type_camion, poids_transport) VALUES
('ZZ-999-AA', 'frigo', -5000.00);  -- Échoue car poids_transport < 0

--Essai d’insertion d’une cargaison avec un camion inexistant (doit échouer)
INSERT INTO Cargaisons (date_transport, ville_depart, ville_arrivee, immat, numero_permis) VALUES
('2024-03-12', 'Paris', 'Lyon', 'XX-000-YY', 'PERM123456');  -- Échoue car XX-000-YY n'existe pas dans Camions

--Essai d’insertion d’une marchandise avec un type de camion invalide (doit échouer)
INSERT INTO Marchandises (nom, type_requis, poids, id_cargaison) VALUES
('Téléviseurs', 'camion-benne', 5000.00, 1);  -- Échoue car 'camion-benne' n'est pas un type valide


--Q1 Liste des marchandises triées par ville de départ et par poids

SELECT nom, type_requis, poids, ville_depart
FROM Marchandises
NATURAL JOIN Cargaisons
ORDER BY ville_depart ASC, poids DESC;

--Q2 Liste des camions de type frigo avec un poids total de marchandises > 15 tonnes

SELECT immat, type_camion, SUM(poids) AS poids_total
FROM Camions
NATURAL JOIN Cargaisons
NATURAL JOIN Marchandises 
WHERE type_camion = 'frigo'
GROUP BY immat, type_camion
HAVING SUM(poids) > 15000;

--Q3 Liste des chauffeurs non affectés à un camion le 1/4/2021

SELECT numero_permis, nom, prenom
FROM Chauffeurs
WHERE numero_permis NOT IN (
    SELECT numero_permis
    FROM Cargaison
    WHERE date_transport = '2021-04-01'
);

--Q4 Nombre de jours d’utilisation par camion

SELECT immat, COUNT(DISTINCT date_transport) AS jours_utilisation
FROM Camions
NATURAL JOIN Cargaisons
GROUP BY immat;

--Q5 La ville qui a le plus de livraisons de cargaisons

SELECT ville_arrivee, COUNT(*) AS nombre_livraisons
FROM Cargaisons
GROUP BY ville_arrivee
ORDER BY nombre_livraisons DESC
LIMIT 1;


-- Création du rôle chauffeur
CREATE ROLE 'role_chauffeur';

-- Création du rôle logisticien
CREATE ROLE 'role_logisticien';

-- Droits du chauffeur
GRANT SELECT ON cametard.Chauffeurs TO 'role_chauffeur';
GRANT SELECT ON cametard.Camions TO 'role_chauffeur';
GRANT SELECT ON cametard.Cargaisons TO 'role_chauffeur';
GRANT SELECT ON cametard.Marchandises TO 'role_chauffeur';

-- Droits du logisticien (gestion des affectations, camions et marchandises)
GRANT SELECT, INSERT ON cametard.Chauffeurs TO 'role_logisticien';
GRANT SELECT, INSERT ON cametard.Camions TO 'role_logisticien';
GRANT SELECT, INSERT, UPDATE ON cametard.Cargaisons TO 'role_logisticien';
GRANT SELECT, INSERT, UPDATE ON cametard.Marchandises TO 'role_logisticien';


-- Création de la première vue Nombre de jours travaillés par chauffeur (pour les chauffeurs)
CREATE VIEW vue_jours_travailles AS
SELECT 
    numero_permis, 
    YEAR(date_transport) AS annee,
    MONTH(date_transport) AS mois,
    COUNT(DISTINCT date_transport) AS jours_travailles
FROM Cargaisons
GROUP BY numero_permis, annee, mois;

--On donne les droits aux chauffeurs pour cette vue
GRANT SELECT ON cametard.vue_jours_travailles TO 'role_chauffeur';


-- Création de la deuxième vue Synthèse des cargaisons (pour les logisticiens)
CREATE VIEW vue_cargaisons AS
SELECT 
    id_cargaison, 
    date_transport, 
    ville_depart, 
    ville_arrivee, 
    immat AS camion, 
    numero_permis AS chauffeur
FROM Cargaisons
NATURAL JOIN Camions
NATURAL JOIN Chauffeurs;

--On donne les droits aux logisticiens pour cette vue
GRANT SELECT ON cametard.vue_cargaisons TO 'role_logisticien';


-- Création des utilisateurs
CREATE USER 'chauffeur1'@'localhost' IDENTIFIED BY 'mdp1';
CREATE USER 'logisticien1'@'localhost' IDENTIFIED BY 'mdp2';

-- Attribution des rôles aux utilisateurs
GRANT 'role_chauffeur' TO 'chauffeur1'@'localhost';
GRANT 'role_logisticien' TO 'logisticien1'@'localhost';




-- 🚛 Insertion de 20 camions
INSERT INTO Camions (immat, type_camion, poids_transport) VALUES
('AB-101-CD', 'frigo', 18000.00), ('AB-102-CD', 'frigo', 19000.00),
('XY-201-ZZ', 'citerne', 25000.00), ('XY-202-ZZ', 'citerne', 23000.00),
('EF-301-GH', 'palette', 12000.00), ('EF-302-GH', 'palette', 15000.00),
('LM-401-NO', 'plateau', 15000.00), ('LM-402-NO', 'plateau', 18000.00),
('QR-501-ST', 'frigo', 20000.00), ('QR-502-ST', 'frigo', 17000.00),
('JK-601-YU', 'citerne', 26000.00), ('JK-602-YU', 'citerne', 24000.00),
('WX-701-OP', 'palette', 14000.00), ('WX-702-OP', 'palette', 13000.00),
('TZ-801-QM', 'plateau', 16000.00), ('TZ-802-QM', 'plateau', 17000.00),
('MN-901-RT', 'frigo', 19500.00), ('MN-902-RT', 'citerne', 25500.00),
('XY-903-ZZ', 'palette', 11000.00), ('XY-904-ZZ', 'plateau', 17500.00);

-- 🧑‍🔧 Insertion de 30 chauffeurs
INSERT INTO Chauffeurs (numero_permis, nom, prenom) VALUES
('PERM1001', 'Durand', 'Jean'), ('PERM1002', 'Martin', 'Sophie'),
('PERM1003', 'Leroy', 'Thomas'), ('PERM1004', 'Dupont', 'Alice'),
('PERM1005', 'Roux', 'Michel'), ('PERM1006', 'Moreau', 'Julie'),
('PERM1007', 'Fournier', 'Pierre'), ('PERM1008', 'Garnier', 'Pauline'),
('PERM1009', 'Blanc', 'Nicolas'), ('PERM1010', 'Fontaine', 'Camille'),
('PERM1011', 'Leclerc', 'Antoine'), ('PERM1012', 'Barbier', 'Émilie'),
('PERM1013', 'Robin', 'Hugo'), ('PERM1014', 'Gauthier', 'Marine'),
('PERM1015', 'Bernard', 'Lucas'), ('PERM1016', 'Henry', 'Charlotte'),
('PERM1017', 'Perrin', 'Léo'), ('PERM1018', 'Thomas', 'Manon'),
('PERM1019', 'Dubois', 'Alexandre'), ('PERM1020', 'Laurent', 'Céline'),
('PERM1021', 'Guillaume', 'Éric'), ('PERM1022', 'Benoît', 'Noémie'),
('PERM1023', 'David', 'Lucie'), ('PERM1024', 'Simon', 'Julien'),
('PERM1025', 'Olivier', 'Isabelle'), ('PERM1026', 'Hervé', 'Philippe'),
('PERM1027', 'Dumont', 'Nathalie'), ('PERM1028', 'Vincent', 'Élodie'),
('PERM1029', 'Denis', 'François'), ('PERM1030', 'Alexis', 'Sandrine');

-- 📦 Insertion de 50 cargaisons
INSERT INTO Cargaisons (date_transport, ville_depart, ville_arrivee, immat, numero_permis) VALUES
('2024-04-01', 'Paris', 'Lyon', 'AB-101-CD', 'PERM1001'),
('2024-04-02', 'Marseille', 'Bordeaux', 'XY-201-ZZ', 'PERM1002'),
('2024-04-03', 'Lille', 'Nantes', 'EF-301-GH', 'PERM1003'),
('2024-04-04', 'Nice', 'Toulouse', 'LM-401-NO', 'PERM1004'),
('2024-04-05', 'Strasbourg', 'Rennes', 'QR-501-ST', 'PERM1005'),
('2024-04-06', 'Dijon', 'Montpellier', 'JK-601-YU', 'PERM1006'),
('2024-04-07', 'Grenoble', 'Rouen', 'WX-701-OP', 'PERM1007'),
('2024-04-08', 'Metz', 'Orléans', 'TZ-801-QM', 'PERM1008'),
('2024-04-09', 'Nîmes', 'Reims', 'MN-901-RT', 'PERM1009'),
('2024-04-10', 'Perpignan', 'Le Havre', 'XY-903-ZZ', 'PERM1010');

-- 🎁 Insertion de 100 marchandises (chaque cargaison reçoit 2 marchandises adaptées à son type)
INSERT INTO Marchandises (nom, type_requis, poids, id_cargaison) VALUES
('Pommes', 'frigo', 16000.00, 1), ('Poissons', 'frigo', 2000.00, 1),
('Carburant', 'citerne', 24000.00, 2), ('Gaz liquide', 'citerne', 25000.00, 2),
('Meubles', 'palette', 10000.00, 3), ('Électroménager', 'palette', 12000.00, 3),
('Béton', 'plateau', 14000.00, 4), ('Matériaux de construction', 'plateau', 17000.00, 4),
('Produits laitiers', 'frigo', 18000.00, 5), ('Vin', 'frigo', 15000.00, 6),
('Boissons', 'palette', 12000.00, 7), ('Équipements industriels', 'plateau', 20000.00, 8),
('Riz', 'palette', 11000.00, 9), ('Pâtes alimentaires', 'palette', 9000.00, 10);