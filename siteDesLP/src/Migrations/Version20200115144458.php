<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200115144458 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE promotions_classes (promotions_id INT NOT NULL, classes_id INT NOT NULL, INDEX IDX_D4344A4B10007789 (promotions_id), INDEX IDX_D4344A4B9E225B24 (classes_id), PRIMARY KEY(promotions_id, classes_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offres (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, type_offre_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_C6AC3544A4AEAFEA (entreprise_id), INDEX IDX_C6AC3544813777A6 (type_offre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offres_classes (offres_id INT NOT NULL, classes_id INT NOT NULL, INDEX IDX_29A215B46C83CD9F (offres_id), INDEX IDX_29A215B49E225B24 (classes_id), PRIMARY KEY(offres_id, classes_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_offre (id INT AUTO_INCREMENT NOT NULL, nom_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promotions_classes ADD CONSTRAINT FK_D4344A4B10007789 FOREIGN KEY (promotions_id) REFERENCES promotions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promotions_classes ADD CONSTRAINT FK_D4344A4B9E225B24 FOREIGN KEY (classes_id) REFERENCES classes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT FK_C6AC3544A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprises (id)');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT FK_C6AC3544813777A6 FOREIGN KEY (type_offre_id) REFERENCES type_offre (id)');
        $this->addSql('ALTER TABLE offres_classes ADD CONSTRAINT FK_29A215B46C83CD9F FOREIGN KEY (offres_id) REFERENCES offres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offres_classes ADD CONSTRAINT FK_29A215B49E225B24 FOREIGN KEY (classes_id) REFERENCES classes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC510007789');
        $this->addSql('DROP INDEX IDX_2ED7EC510007789 ON classes');
        $this->addSql('ALTER TABLE classes DROP promotions_id');
        $this->addSql('ALTER TABLE contacts DROP FOREIGN KEY FK_334015734836BEA1');
        $this->addSql('DROP INDEX IDX_334015734836BEA1 ON contacts');
        $this->addSql('ALTER TABLE contacts DROP entreprise_contact_id');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_33401573BF396750 FOREIGN KEY (id) REFERENCES entreprises (id)');
        $this->addSql('ALTER TABLE cours ADD visible TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offres_classes DROP FOREIGN KEY FK_29A215B46C83CD9F');
        $this->addSql('ALTER TABLE offres DROP FOREIGN KEY FK_C6AC3544813777A6');
        $this->addSql('DROP TABLE promotions_classes');
        $this->addSql('DROP TABLE offres');
        $this->addSql('DROP TABLE offres_classes');
        $this->addSql('DROP TABLE type_offre');
        $this->addSql('ALTER TABLE classes ADD promotions_id INT NOT NULL');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC510007789 FOREIGN KEY (promotions_id) REFERENCES promotions (id)');
        $this->addSql('CREATE INDEX IDX_2ED7EC510007789 ON classes (promotions_id)');
        $this->addSql('ALTER TABLE contacts DROP FOREIGN KEY FK_33401573BF396750');
        $this->addSql('ALTER TABLE contacts ADD entreprise_contact_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_334015734836BEA1 FOREIGN KEY (entreprise_contact_id) REFERENCES entreprises (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_334015734836BEA1 ON contacts (entreprise_contact_id)');
        $this->addSql('ALTER TABLE cours DROP visible');
    }
}
