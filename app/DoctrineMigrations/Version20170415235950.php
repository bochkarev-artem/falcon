<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170415235950 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE book_card (book_card_id INT AUTO_INCREMENT NOT NULL, book_id INT DEFAULT NULL, user_id INT DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, INDEX IDX_F89D3DB16A2B381 (book_id), INDEX IDX_F89D3DBA76ED395 (user_id), PRIMARY KEY(book_card_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_review (book_review_id INT AUTO_INCREMENT NOT NULL, book_card_id INT DEFAULT NULL, text LONGTEXT NOT NULL, approved TINYINT(1) NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_50948A4BD9ECB2B6 (book_card_id), PRIMARY KEY(book_review_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_card ADD CONSTRAINT FK_F89D3DB16A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id)');
        $this->addSql('ALTER TABLE book_card ADD CONSTRAINT FK_F89D3DBA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4BD9ECB2B6 FOREIGN KEY (book_card_id) REFERENCES book_card (book_card_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647992FC23A8 ON fos_user (username_canonical)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_review DROP FOREIGN KEY FK_50948A4BD9ECB2B6');
        $this->addSql('DROP TABLE book_card');
        $this->addSql('DROP TABLE book_review');
        $this->addSql('DROP INDEX UNIQ_957A647992FC23A8 ON fos_user');
    }
}
