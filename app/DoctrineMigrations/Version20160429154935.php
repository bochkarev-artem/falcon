<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160429154935 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE genre_genre_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE author_author_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sequence_sequence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE book_book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE genre (genre_id INT NOT NULL, litres_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(genre_id))');
        $this->addSql('CREATE UNIQUE INDEX genre_ids ON genre (token)');
        $this->addSql('CREATE TABLE tag (tag_id INT NOT NULL, litres_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(tag_id))');
        $this->addSql('CREATE UNIQUE INDEX tag_ids ON tag (litres_id)');
        $this->addSql('CREATE TABLE author (author_id INT NOT NULL, litres_hub_id INT DEFAULT NULL, document_id VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, level INT DEFAULT NULL, recenses_count INT DEFAULT NULL, arts_count INT DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(author_id))');
        $this->addSql('CREATE UNIQUE INDEX author_ids ON author (litres_hub_id, document_id)');
        $this->addSql('CREATE TABLE sequence (sequence_id INT NOT NULL, litres_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, number INT DEFAULT NULL, PRIMARY KEY(sequence_id))');
        $this->addSql('CREATE UNIQUE INDEX sequence_ids ON sequence (litres_id)');
        $this->addSql('CREATE TABLE book (book_id INT NOT NULL, litres_hub_id INT NOT NULL, price VARCHAR(255) DEFAULT NULL, cover VARCHAR(255) DEFAULT NULL, cover_preview VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, type INT DEFAULT NULL, has_trial BOOLEAN DEFAULT NULL, reader VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, annotation TEXT DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, lang VARCHAR(255) DEFAULT NULL, document_url VARCHAR(255) DEFAULT NULL, document_id VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, city_published VARCHAR(255) DEFAULT NULL, year_published VARCHAR(4) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, recenses_count INT DEFAULT NULL, PRIMARY KEY(book_id))');
        $this->addSql('CREATE UNIQUE INDEX book_ids ON book (litres_hub_id)');
        $this->addSql('CREATE TABLE book_genre (book_id INT NOT NULL, genre_id INT NOT NULL, PRIMARY KEY(book_id, genre_id))');
        $this->addSql('CREATE INDEX IDX_8D92268116A2B381 ON book_genre (book_id)');
        $this->addSql('CREATE INDEX IDX_8D9226814296D31F ON book_genre (genre_id)');
        $this->addSql('CREATE TABLE book_author (book_id INT NOT NULL, author_id INT NOT NULL, PRIMARY KEY(book_id, author_id))');
        $this->addSql('CREATE INDEX IDX_9478D34516A2B381 ON book_author (book_id)');
        $this->addSql('CREATE INDEX IDX_9478D345F675F31B ON book_author (author_id)');
        $this->addSql('CREATE TABLE book_tag (book_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(book_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_F2F4CE1516A2B381 ON book_tag (book_id)');
        $this->addSql('CREATE INDEX IDX_F2F4CE15BAD26311 ON book_tag (tag_id)');
        $this->addSql('CREATE TABLE book_sequence (book_id INT NOT NULL, sequence_id INT NOT NULL, PRIMARY KEY(book_id, sequence_id))');
        $this->addSql('CREATE INDEX IDX_4B79C17216A2B381 ON book_sequence (book_id)');
        $this->addSql('CREATE INDEX IDX_4B79C17298FB19AE ON book_sequence (sequence_id)');
        $this->addSql('ALTER TABLE book_genre ADD CONSTRAINT FK_8D92268116A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_genre ADD CONSTRAINT FK_8D9226814296D31F FOREIGN KEY (genre_id) REFERENCES genre (genre_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (author_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_tag ADD CONSTRAINT FK_F2F4CE1516A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_tag ADD CONSTRAINT FK_F2F4CE15BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (tag_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_sequence ADD CONSTRAINT FK_4B79C17216A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_sequence ADD CONSTRAINT FK_4B79C17298FB19AE FOREIGN KEY (sequence_id) REFERENCES sequence (sequence_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE book_genre DROP CONSTRAINT FK_8D9226814296D31F');
        $this->addSql('ALTER TABLE book_tag DROP CONSTRAINT FK_F2F4CE15BAD26311');
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D345F675F31B');
        $this->addSql('ALTER TABLE book_sequence DROP CONSTRAINT FK_4B79C17298FB19AE');
        $this->addSql('ALTER TABLE book_genre DROP CONSTRAINT FK_8D92268116A2B381');
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D34516A2B381');
        $this->addSql('ALTER TABLE book_tag DROP CONSTRAINT FK_F2F4CE1516A2B381');
        $this->addSql('ALTER TABLE book_sequence DROP CONSTRAINT FK_4B79C17216A2B381');
        $this->addSql('DROP SEQUENCE genre_genre_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE author_author_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sequence_sequence_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE book_book_id_seq CASCADE');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE sequence');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_genre');
        $this->addSql('DROP TABLE book_author');
        $this->addSql('DROP TABLE book_tag');
        $this->addSql('DROP TABLE book_sequence');
    }
}
