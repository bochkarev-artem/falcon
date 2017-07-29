<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170720211941 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genre ADD title_en VARCHAR(255) DEFAULT NULL, ADD description_en LONGTEXT DEFAULT NULL, ADD slug_en VARCHAR(255) DEFAULT NULL, CHANGE description description_ru LONGTEXT DEFAULT NULL, CHANGE title title_ru VARCHAR(255) DEFAULT NULL, CHANGE slug slug_ru VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE author DROP level, DROP litres_hub_id, DROP review_count, DROP arts_count, DROP photo, DROP photo_path, DROP description');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genre CHANGE title_ru title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE description_ru description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE slug_ru slug VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP title_en, DROP description_en, DROP slug_en');
        $this->addSql('ALTER TABLE author ADD level INT DEFAULT NULL, ADD litres_hub_id INT DEFAULT NULL, ADD review_count INT DEFAULT NULL, ADD arts_count INT DEFAULT NULL, ADD photo VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD photo_path VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
