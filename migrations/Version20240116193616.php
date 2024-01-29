<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116193616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faire_don ADD dahra_id INT NOT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE faire_don ADD CONSTRAINT FK_48F8565B616D84A5 FOREIGN KEY (dahra_id) REFERENCES dahra (id)');
        $this->addSql('ALTER TABLE faire_don ADD CONSTRAINT FK_48F8565BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_48F8565B616D84A5 ON faire_don (dahra_id)');
        $this->addSql('CREATE INDEX IDX_48F8565BA76ED395 ON faire_don (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faire_don DROP FOREIGN KEY FK_48F8565B616D84A5');
        $this->addSql('ALTER TABLE faire_don DROP FOREIGN KEY FK_48F8565BA76ED395');
        $this->addSql('DROP INDEX IDX_48F8565B616D84A5 ON faire_don');
        $this->addSql('DROP INDEX IDX_48F8565BA76ED395 ON faire_don');
        $this->addSql('ALTER TABLE faire_don DROP dahra_id, DROP user_id');
    }
}
