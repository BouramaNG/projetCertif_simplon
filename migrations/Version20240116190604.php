<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116190604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dahra ADD adresse VARCHAR(255) NOT NULL, ADD numero_telephone_ouztas VARCHAR(255) NOT NULL, DROP nombre_talibe');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dahra ADD nombre_talibe INT NOT NULL, DROP adresse, DROP numero_telephone_ouztas');
    }
}
