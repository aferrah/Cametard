CREATE TABLE Camions(
   immat VARCHAR(10),
   type_camion VARCHAR(20),
   poids_transport DECIMAL(15,2),
   PRIMARY KEY(immat)
);

CREATE TABLE Chauffeurs(
   numero_permis INT,
   nom VARCHAR(50),
   prenom VARCHAR(50),
   PRIMARY KEY(numero_permis)
);

CREATE TABLE Cargaisons(
   id_cargaison INT,
   date_transport DATE,
   ville_depart VARCHAR(50),
   ville_arrivee VARCHAR(50),
   immat VARCHAR(10) NOT NULL,
   numero_permis INT NOT NULL,
   PRIMARY KEY(id_cargaison),
   FOREIGN KEY(immat) REFERENCES Camions(immat),
   FOREIGN KEY(numero_permis) REFERENCES Chauffeurs(numero_permis)
);

CREATE TABLE Marchandises(
   id_marchandise INT,
   nom VARCHAR(50),
   type_requis VARCHAR(50),
   poids DECIMAL(15,2),
   id_cargaison INT NOT NULL,
   PRIMARY KEY(id_marchandise),
   FOREIGN KEY(id_cargaison) REFERENCES Cargaisons(id_cargaison)
);
