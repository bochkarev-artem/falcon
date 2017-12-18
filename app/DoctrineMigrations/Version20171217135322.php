<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171217135322 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book DROP lang');
        $this->addSql('ALTER TABLE ads DROP lang');
        $this->addSql('ALTER TABLE genre CHANGE description_ru description LONGTEXT DEFAULT NULL, CHANGE title_ru title VARCHAR(255) DEFAULT NULL, CHANGE slug_ru slug VARCHAR(255) DEFAULT NULL, DROP title_en, DROP description_en, DROP slug_en');
        $this->addSql('ALTER TABLE author DROP lang');
        $this->addSql('ALTER TABLE sequence DROP lang');
    }

    public function down(Schema $schema)
    {
    }
}
