<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125104006 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fichiers (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE informations_classes (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, description LONGTEXT NOT NULL, chemin_plaquette VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_95757C7D8F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE informations_globales (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE secretaire (id INT AUTO_INCREMENT NOT NULL, nom_secretaire VARCHAR(64) NOT NULL, prenom_secretaire VARCHAR(64) NOT NULL, mail_academique VARCHAR(64) NOT NULL, login VARCHAR(64) NOT NULL, password VARCHAR(64) NOT NULL, password_requested_at DATETIME DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE informations_classes ADD CONSTRAINT FK_95757C7D8F5EA509 FOREIGN KEY (classe_id) REFERENCES classes (id)');
        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC543F56ED4');
        $this->addSql('ALTER TABLE classes ADD nom_complet VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC543F56ED4 FOREIGN KEY (professeur_responsable_id) REFERENCES professeurs (id)');
        $this->addSql('ALTER TABLE etudiants ADD password_requested_at DATETIME DEFAULT NULL, ADD token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE professeurs ADD password_requested_at DATETIME DEFAULT NULL, ADD token VARCHAR(255) DEFAULT NULL, DROP date_naissance');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE fichiers');
        $this->addSql('DROP TABLE informations_classes');
        $this->addSql('DROP TABLE informations_globales');
        $this->addSql('DROP TABLE secretaire');
        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC543F56ED4');
        $this->addSql('ALTER TABLE classes DROP nom_complet');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC543F56ED4 FOREIGN KEY (professeur_responsable_id) REFERENCES professeurs (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE etudiants DROP password_requested_at, DROP token');
        $this->addSql('ALTER TABLE professeurs ADD date_naissance DATE NOT NULL, DROP password_requested_at, DROP token');
    }
}
