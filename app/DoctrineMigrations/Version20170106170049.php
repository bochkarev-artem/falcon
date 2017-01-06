<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170106170049 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE author CHANGE recenses_count review_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD sequence_number INT DEFAULT NULL, CHANGE recenses_count review_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sequence DROP number');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE author CHANGE review_count recenses_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book CHANGE review_count recenses_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book DROP sequence_number');
        $this->addSql('ALTER TABLE sequence ADD number INT DEFAULT NULL');
    }
}
