<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191129190438 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, date DATE NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_classes (articles_id INT NOT NULL, classes_id INT NOT NULL, INDEX IDX_47881A641EBAF6CC (articles_id), INDEX IDX_47881A649E225B24 (classes_id), PRIMARY KEY(articles_id, classes_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(64) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours_classes (cours_id INT NOT NULL, classes_id INT NOT NULL, INDEX IDX_41EB17BE7ECF78B0 (cours_id), INDEX IDX_41EB17BE9E225B24 (classes_id), PRIMARY KEY(cours_id, classes_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichiers (id INT AUTO_INCREMENT NOT NULL, cours_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, emplacement VARCHAR(255) NOT NULL, visible TINYINT(1) NOT NULL, INDEX IDX_969DB4AB7ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles_classes ADD CONSTRAINT FK_47881A641EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_classes ADD CONSTRAINT FK_47881A649E225B24 FOREIGN KEY (classes_id) REFERENCES classes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_classes ADD CONSTRAINT FK_41EB17BE7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_classes ADD CONSTRAINT FK_41EB17BE9E225B24 FOREIGN KEY (classes_id) REFERENCES classes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT FK_969DB4AB7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE etudiants CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE professeurs CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE secretaire CHANGE password password VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE articles_classes DROP FOREIGN KEY FK_47881A641EBAF6CC');
        $this->addSql('ALTER TABLE cours_classes DROP FOREIGN KEY FK_41EB17BE7ECF78B0');
        $this->addSql('ALTER TABLE fichiers DROP FOREIGN KEY FK_969DB4AB7ECF78B0');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE articles_classes');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE cours_classes');
        $this->addSql('DROP TABLE fichiers');
        $this->addSql('ALTER TABLE etudiants CHANGE password password VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE professeurs CHANGE password password VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE secretaire CHANGE password password VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci');
    }
}
