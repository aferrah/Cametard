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

/*exemple d'insertion qui déclenche une violation de la contrainte*/
/* INSERT INTO Camions values ('khkjh','camion',-50,); */