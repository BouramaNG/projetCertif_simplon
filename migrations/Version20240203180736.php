<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240203180736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE black_listed_tocken (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(2000) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dahra (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, nom_ouztas VARCHAR(255) NOT NULL, numero_telephone_ouztas VARCHAR(255) NOT NULL, nombre_talibe INT NOT NULL, image_filename VARCHAR(255) DEFAULT NULL, INDEX IDX_487266D4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faire_don (id INT AUTO_INCREMENT NOT NULL, dahra_id INT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, type_don VARCHAR(255) DEFAULT NULL, adresse_provenance VARCHAR(255) NOT NULL, description_don LONGTEXT NOT NULL, disponibilite_don VARCHAR(255) NOT NULL, INDEX IDX_48F8565B616D84A5 (dahra_id), INDEX IDX_48F8565BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_7E8585C8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parrainage (id INT AUTO_INCREMENT NOT NULL, talibe_id INT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, type_parrainage VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_195BAFB542E0616F (talibe_id), INDEX IDX_195BAFB5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE talibe (id INT AUTO_INCREMENT NOT NULL, dahra_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, age INT NOT NULL, adresse VARCHAR(255) NOT NULL, situation VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, date_arrive_talibe DATE DEFAULT NULL, presence_talibe VARCHAR(255) NOT NULL, image_filename VARCHAR(255) DEFAULT NULL, INDEX IDX_D9C6946F616D84A5 (dahra_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, numero_telephone VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, reset_token VARCHAR(2000) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dahra ADD CONSTRAINT FK_487266D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE faire_don ADD CONSTRAINT FK_48F8565B616D84A5 FOREIGN KEY (dahra_id) REFERENCES dahra (id)');
        $this->addSql('ALTER TABLE faire_don ADD CONSTRAINT FK_48F8565BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE parrainage ADD CONSTRAINT FK_195BAFB542E0616F FOREIGN KEY (talibe_id) REFERENCES talibe (id)');
        $this->addSql('ALTER TABLE parrainage ADD CONSTRAINT FK_195BAFB5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE talibe ADD CONSTRAINT FK_D9C6946F616D84A5 FOREIGN KEY (dahra_id) REFERENCES dahra (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dahra DROP FOREIGN KEY FK_487266D4A76ED395');
        $this->addSql('ALTER TABLE faire_don DROP FOREIGN KEY FK_48F8565B616D84A5');
        $this->addSql('ALTER TABLE faire_don DROP FOREIGN KEY FK_48F8565BA76ED395');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8A76ED395');
        $this->addSql('ALTER TABLE parrainage DROP FOREIGN KEY FK_195BAFB542E0616F');
        $this->addSql('ALTER TABLE parrainage DROP FOREIGN KEY FK_195BAFB5A76ED395');
        $this->addSql('ALTER TABLE talibe DROP FOREIGN KEY FK_D9C6946F616D84A5');
        $this->addSql('DROP TABLE black_listed_tocken');
        $this->addSql('DROP TABLE dahra');
        $this->addSql('DROP TABLE faire_don');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE parrainage');
        $this->addSql('DROP TABLE talibe');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
