<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191121122024 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC543F56ED4');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC543F56ED4 FOREIGN KEY (professeur_responsable_id) REFERENCES professeurs (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE professeurs DROP date_naissance');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC543F56ED4');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC543F56ED4 FOREIGN KEY (professeur_responsable_id) REFERENCES professeurs (id)');
        $this->addSql('ALTER TABLE professeurs ADD date_naissance DATE NOT NULL');
    }
}
