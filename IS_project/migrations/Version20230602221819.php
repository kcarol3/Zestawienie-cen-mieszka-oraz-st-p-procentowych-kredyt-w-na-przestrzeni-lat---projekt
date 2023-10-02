<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602221819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE house_size (id INT AUTO_INCREMENT NOT NULL, size VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE houses ADD house_size_id INT NOT NULL');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBDD5A2E3A FOREIGN KEY (house_size_id) REFERENCES house_size (id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CBDD5A2E3A ON houses (house_size_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBDD5A2E3A');
        $this->addSql('DROP TABLE house_size');
        $this->addSql('DROP INDEX IDX_95D7F5CBDD5A2E3A ON houses');
        $this->addSql('ALTER TABLE houses DROP house_size_id');
    }
}
