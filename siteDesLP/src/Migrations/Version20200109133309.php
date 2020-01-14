<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200109133309 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE candidats (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(64) NOT NULL, prenom VARCHAR(64) NOT NULL, mail VARCHAR(64) NOT NULL, password VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotions (id INT AUTO_INCREMENT NOT NULL, annee VARCHAR(9) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classes ADD promotions_id INT NOT NULL');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC510007789 FOREIGN KEY (promotions_id) REFERENCES promotions (id)');
        $this->addSql('CREATE INDEX IDX_2ED7EC510007789 ON classes (promotions_id)');
        $this->addSql('ALTER TABLE etudiants ADD id INT AUTO_INCREMENT NOT NULL, ADD nom VARCHAR(64) NOT NULL, ADD prenom VARCHAR(64) NOT NULL, DROP num_etudiant, DROP nom_etudiant, DROP prenom_etudiant, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC510007789');
        $this->addSql('DROP TABLE candidats');
        $this->addSql('DROP TABLE promotions');
        $this->addSql('DROP INDEX IDX_2ED7EC510007789 ON classes');
        $this->addSql('ALTER TABLE classes DROP promotions_id');
        $this->addSql('ALTER TABLE etudiants MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE etudiants DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE etudiants ADD num_etudiant INT NOT NULL, ADD nom_etudiant VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, ADD prenom_etudiant VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, DROP id, DROP nom, DROP prenom');
    }
}
