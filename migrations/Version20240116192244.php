<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116192244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE talibe ADD dahra_id INT NOT NULL');
        $this->addSql('ALTER TABLE talibe ADD CONSTRAINT FK_D9C6946F616D84A5 FOREIGN KEY (dahra_id) REFERENCES dahra (id)');
        $this->addSql('CREATE INDEX IDX_D9C6946F616D84A5 ON talibe (dahra_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE talibe DROP FOREIGN KEY FK_D9C6946F616D84A5');
        $this->addSql('DROP INDEX IDX_D9C6946F616D84A5 ON talibe');
        $this->addSql('ALTER TABLE talibe DROP dahra_id');
    }
}
