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

        $this->addSql('CREATE SEQUENCE author_author_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE book_book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE genre_genre_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sequence_sequence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE author (author_id INT NOT NULL, litres_hub_id INT NOT NULL, document_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) NOT NULL, level INT NOT NULL, recenses_count INT NOT NULL, arts_count INT NOT NULL, photo VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(author_id))');
        $this->addSql('CREATE TABLE book (book_id INT NOT NULL, sequence_id INT DEFAULT NULL, litres_hub_id INT NOT NULL, price VARCHAR(255) NOT NULL, cover VARCHAR(255) NOT NULL, cover_preview VARCHAR(255) NOT NULL, filename INT NOT NULL, type INT NOT NULL, has_trial BOOLEAN NOT NULL, reader VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, annotation TEXT NOT NULL, date VARCHAR(255) NOT NULL, lang VARCHAR(255) NOT NULL, document_url VARCHAR(255) NOT NULL, document_id INT NOT NULL, publisher VARCHAR(255) NOT NULL, city_published VARCHAR(255) NOT NULL, year_published VARCHAR(4) NOT NULL, isbn VARCHAR(255) NOT NULL, rating DOUBLE PRECISION NOT NULL, recenses_count INT NOT NULL, PRIMARY KEY(book_id))');
        $this->addSql('CREATE INDEX IDX_CBE5A33198FB19AE ON book (sequence_id)');
        $this->addSql('CREATE TABLE book_genres (book_id INT NOT NULL, genre_id INT NOT NULL, PRIMARY KEY(book_id, genre_id))');
        $this->addSql('CREATE INDEX IDX_813CEE9B16A2B381 ON book_genres (book_id)');
        $this->addSql('CREATE INDEX IDX_813CEE9B4296D31F ON book_genres (genre_id)');
        $this->addSql('CREATE TABLE book_authors (book_id INT NOT NULL, author_id INT NOT NULL, PRIMARY KEY(book_id, author_id))');
        $this->addSql('CREATE INDEX IDX_1D2C02C716A2B381 ON book_authors (book_id)');
        $this->addSql('CREATE INDEX IDX_1D2C02C7F675F31B ON book_authors (author_id)');
        $this->addSql('CREATE TABLE book_tags (book_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(book_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_7621DF2E16A2B381 ON book_tags (book_id)');
        $this->addSql('CREATE INDEX IDX_7621DF2EBAD26311 ON book_tags (tag_id)');
        $this->addSql('CREATE TABLE genre (genre_id INT NOT NULL, litres_id INT NOT NULL, title VARCHAR(255) NOT NULL, token VARCHAR(255) DEFAULT NULL, type INT NOT NULL, PRIMARY KEY(genre_id))');
        $this->addSql('CREATE TABLE sequence (sequence_id INT NOT NULL, litres_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(sequence_id))');
        $this->addSql('CREATE TABLE tag (tag_id INT NOT NULL, litres_id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(tag_id))');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33198FB19AE FOREIGN KEY (sequence_id) REFERENCES sequence (sequence_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_genres ADD CONSTRAINT FK_813CEE9B16A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_genres ADD CONSTRAINT FK_813CEE9B4296D31F FOREIGN KEY (genre_id) REFERENCES genre (genre_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_authors ADD CONSTRAINT FK_1D2C02C716A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_authors ADD CONSTRAINT FK_1D2C02C7F675F31B FOREIGN KEY (author_id) REFERENCES author (author_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_tags ADD CONSTRAINT FK_7621DF2E16A2B381 FOREIGN KEY (book_id) REFERENCES book (book_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_tags ADD CONSTRAINT FK_7621DF2EBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (tag_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE book_authors DROP CONSTRAINT FK_1D2C02C7F675F31B');
        $this->addSql('ALTER TABLE book_genres DROP CONSTRAINT FK_813CEE9B16A2B381');
        $this->addSql('ALTER TABLE book_authors DROP CONSTRAINT FK_1D2C02C716A2B381');
        $this->addSql('ALTER TABLE book_tags DROP CONSTRAINT FK_7621DF2E16A2B381');
        $this->addSql('ALTER TABLE book_genres DROP CONSTRAINT FK_813CEE9B4296D31F');
        $this->addSql('ALTER TABLE book DROP CONSTRAINT FK_CBE5A33198FB19AE');
        $this->addSql('ALTER TABLE book_tags DROP CONSTRAINT FK_7621DF2EBAD26311');
        $this->addSql('DROP SEQUENCE author_author_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE book_book_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE genre_genre_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sequence_sequence_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_tag_id_seq CASCADE');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_genres');
        $this->addSql('DROP TABLE book_authors');
        $this->addSql('DROP TABLE book_tags');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE sequence');
        $this->addSql('DROP TABLE tag');
    }
}
