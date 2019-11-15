<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191115164604 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE professeurs (id INT AUTO_INCREMENT NOT NULL, nom_professeur VARCHAR(64) NOT NULL, prenom_professeur VARCHAR(64) NOT NULL, mail_academique VARCHAR(64) NOT NULL, login VARCHAR(64) NOT NULL, date_naissance DATE NOT NULL, password VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professeurs_classes (professeurs_id INT NOT NULL, classes_id INT NOT NULL, INDEX IDX_46D5655B3E1D55D7 (professeurs_id), INDEX IDX_46D5655B9E225B24 (classes_id), PRIMARY KEY(professeurs_id, classes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE professeurs_classes ADD CONSTRAINT FK_46D5655B3E1D55D7 FOREIGN KEY (professeurs_id) REFERENCES professeurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professeurs_classes ADD CONSTRAINT FK_46D5655B9E225B24 FOREIGN KEY (classes_id) REFERENCES classes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classes ADD professeur_responsable_id INT DEFAULT NULL, CHANGE nom_classe nom_classe VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC543F56ED4 FOREIGN KEY (professeur_responsable_id) REFERENCES professeurs (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2ED7EC543F56ED4 ON classes (professeur_responsable_id)');
        $this->addSql('ALTER TABLE etudiants CHANGE nom_etudiant nom_etudiant VARCHAR(64) NOT NULL, CHANGE prenom_etudiant prenom_etudiant VARCHAR(64) NOT NULL, CHANGE mail_academique mail_academique VARCHAR(64) NOT NULL, CHANGE mail mail VARCHAR(64) NOT NULL, CHANGE password password VARCHAR(64) NOT NULL, CHANGE login login VARCHAR(64) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC543F56ED4');
        $this->addSql('ALTER TABLE professeurs_classes DROP FOREIGN KEY FK_46D5655B3E1D55D7');
        $this->addSql('DROP TABLE professeurs');
        $this->addSql('DROP TABLE professeurs_classes');
        $this->addSql('DROP INDEX UNIQ_2ED7EC543F56ED4 ON classes');
        $this->addSql('ALTER TABLE classes DROP professeur_responsable_id, CHANGE nom_classe nom_classe VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE etudiants CHANGE nom_etudiant nom_etudiant VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE prenom_etudiant prenom_etudiant VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE mail_academique mail_academique VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE mail mail VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE password password VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE login login VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
