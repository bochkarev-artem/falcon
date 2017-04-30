<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170416115118 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE book_rating (book_rating_id INT AUTO_INCREMENT NOT NULL, book_id INT DEFAULT NULL, user_id INT DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, ADD created_on DATETIME NOT NULL, ADD updated_on DATETIME DEFAULT NULL, INDEX IDX_F15E2DAF16A2B381 (book_id), INDEX IDX_F15E2DAFA76ED395 (user_id), PRIMARY KEY(book_rating_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_review (book_review_id INT AUTO_INCREMENT NOT NULL, book_id INT DEFAULT NULL, user_id INT DEFAULT NULL, text LONGTEXT NOT NULL, status SMALLINT NOT NULL, reject_reason VARCHAR(255) DEFAULT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_50948A4B16A2B381 (book_id), INDEX IDX_50948A4BA76ED395 (user_id), PRIMARY KEY(book_review_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_rating ADD CONSTRAINT FK_F15E2DAF16A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id)');
        $this->addSql('ALTER TABLE book_rating ADD CONSTRAINT FK_F15E2DAFA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B16A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id)');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4BA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE book_rating');
        $this->addSql('DROP TABLE book_review');
    }
}
