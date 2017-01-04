<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170103134724 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE author ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sequence ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tag ADD slug VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE author DROP slug');
        $this->addSql('ALTER TABLE book DROP slug');
        $this->addSql('ALTER TABLE sequence DROP slug');
        $this->addSql('ALTER TABLE tag DROP slug');
    }
}
