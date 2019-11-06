<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106143831 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE classes (id INT AUTO_INCREMENT NOT NULL, nom_classe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE etudiants ADD classe_etudiant_id INT NOT NULL');
        $this->addSql('ALTER TABLE etudiants ADD CONSTRAINT FK_227C02EB71F37302 FOREIGN KEY (classe_etudiant_id) REFERENCES classes (id)');
        $this->addSql('CREATE INDEX IDX_227C02EB71F37302 ON etudiants (classe_etudiant_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE etudiants DROP FOREIGN KEY FK_227C02EB71F37302');
        $this->addSql('DROP TABLE classes');
        $this->addSql('DROP INDEX IDX_227C02EB71F37302 ON etudiants');
        $this->addSql('ALTER TABLE etudiants DROP classe_etudiant_id');
    }
}
