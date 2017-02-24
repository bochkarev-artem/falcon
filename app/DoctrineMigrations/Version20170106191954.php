<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170106191954 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE author (author_id INT AUTO_INCREMENT NOT NULL, litres_hub_id INT DEFAULT NULL, document_id VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, level INT DEFAULT NULL, review_count INT DEFAULT NULL, arts_count INT DEFAULT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, photo_path VARCHAR(255) DEFAULT NULL, photo_name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX author_ids (document_id), PRIMARY KEY(author_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (book_id INT AUTO_INCREMENT NOT NULL, sequence_id INT DEFAULT NULL, litres_hub_id INT NOT NULL, price VARCHAR(255) DEFAULT NULL, cover VARCHAR(255) DEFAULT NULL, cover_name VARCHAR(255) DEFAULT NULL, cover_path VARCHAR(255) DEFAULT NULL, has_trial TINYINT(1) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, annotation LONGTEXT DEFAULT NULL, featured_home TINYINT(1) DEFAULT NULL, featured_menu TINYINT(1) DEFAULT NULL, date DATE DEFAULT NULL, lang VARCHAR(255) DEFAULT NULL, sequence_number INT DEFAULT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, document_id VARCHAR(255) DEFAULT NULL, main_author_slug VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, city_published VARCHAR(255) DEFAULT NULL, year_published VARCHAR(4) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, review_count INT DEFAULT NULL, INDEX IDX_CBE5A33198FB19AE (sequence_id), PRIMARY KEY(book_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_genre (book_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_8D92268116A2B381 (book_id), INDEX IDX_8D9226814296D31F (genre_id), PRIMARY KEY(book_id, genre_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_author (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_9478D34516A2B381 (book_id), INDEX IDX_9478D345F675F31B (author_id), PRIMARY KEY(book_id, author_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_tag (book_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F2F4CE1516A2B381 (book_id), INDEX IDX_F2F4CE15BAD26311 (tag_id), PRIMARY KEY(book_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (genre_id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, litres_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, INDEX IDX_835033F8727ACA70 (parent_id), UNIQUE INDEX genre_ids (token), PRIMARY KEY(genre_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sequence (sequence_id INT AUTO_INCREMENT NOT NULL, litres_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX sequence_ids (litres_id), PRIMARY KEY(sequence_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (tag_id INT AUTO_INCREMENT NOT NULL, litres_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX tag_ids (litres_id), PRIMARY KEY(tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33198FB19AE FOREIGN KEY (sequence_id) REFERENCES sequence (sequence_id)');
        $this->addSql('ALTER TABLE book_genre ADD CONSTRAINT FK_8D92268116A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id)');
        $this->addSql('ALTER TABLE book_genre ADD CONSTRAINT FK_8D9226814296D31F FOREIGN KEY (genre_id) REFERENCES genre (genre_id)');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id)');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (author_id)');
        $this->addSql('ALTER TABLE book_tag ADD CONSTRAINT FK_F2F4CE1516A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id)');
        $this->addSql('ALTER TABLE book_tag ADD CONSTRAINT FK_F2F4CE15BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (tag_id)');
        $this->addSql('ALTER TABLE genre ADD CONSTRAINT FK_835033F8727ACA70 FOREIGN KEY (parent_id) REFERENCES genre (genre_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY FK_9478D345F675F31B');
        $this->addSql('ALTER TABLE book_genre DROP FOREIGN KEY FK_8D92268116A2B381');
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY FK_9478D34516A2B381');
        $this->addSql('ALTER TABLE book_tag DROP FOREIGN KEY FK_F2F4CE1516A2B381');
        $this->addSql('ALTER TABLE book_genre DROP FOREIGN KEY FK_8D9226814296D31F');
        $this->addSql('ALTER TABLE genre DROP FOREIGN KEY FK_835033F8727ACA70');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33198FB19AE');
        $this->addSql('ALTER TABLE book_tag DROP FOREIGN KEY FK_F2F4CE15BAD26311');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_genre');
        $this->addSql('DROP TABLE book_author');
        $this->addSql('DROP TABLE book_tag');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE sequence');
        $this->addSql('DROP TABLE tag');
    }
}
