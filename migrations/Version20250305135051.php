<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305135051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Camions (immat VARCHAR(10) NOT NULL, type_camion VARCHAR(20) NOT NULL, poids_transport NUMERIC(15, 2) NOT NULL, PRIMARY KEY(immat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Cargaisons (id_cargaison INT AUTO_INCREMENT NOT NULL, immat VARCHAR(10) NOT NULL, numero_permis VARCHAR(20) NOT NULL, date_transport DATE NOT NULL, ville_depart VARCHAR(50) NOT NULL, ville_arrivee VARCHAR(50) NOT NULL, INDEX IDX_228289B7F0CD7A4F (immat), INDEX IDX_228289B74FFF8769 (numero_permis), PRIMARY KEY(id_cargaison)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Chauffeurs (numero_permis VARCHAR(20) NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, PRIMARY KEY(numero_permis)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Marchandises (id_marchandise INT AUTO_INCREMENT NOT NULL, id_cargaison INT NOT NULL, nom VARCHAR(50) NOT NULL, type_requis VARCHAR(20) NOT NULL, poids NUMERIC(15, 2) NOT NULL, INDEX IDX_628D43518D314067 (id_cargaison), PRIMARY KEY(id_marchandise)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Cargaisons ADD CONSTRAINT FK_228289B7F0CD7A4F FOREIGN KEY (immat) REFERENCES Camions (immat) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Cargaisons ADD CONSTRAINT FK_228289B74FFF8769 FOREIGN KEY (numero_permis) REFERENCES Chauffeurs (numero_permis) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Marchandises ADD CONSTRAINT FK_628D43518D314067 FOREIGN KEY (id_cargaison) REFERENCES Cargaisons (id_cargaison) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Cargaisons DROP FOREIGN KEY FK_228289B7F0CD7A4F');
        $this->addSql('ALTER TABLE Cargaisons DROP FOREIGN KEY FK_228289B74FFF8769');
        $this->addSql('ALTER TABLE Marchandises DROP FOREIGN KEY FK_628D43518D314067');
        $this->addSql('DROP TABLE Camions');
        $this->addSql('DROP TABLE Cargaisons');
        $this->addSql('DROP TABLE Chauffeurs');
        $this->addSql('DROP TABLE Marchandises');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
