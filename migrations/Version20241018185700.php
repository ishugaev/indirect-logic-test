<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018185700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, text VARCHAR(255) NOT NULL, is_correct BOOLEAN NOT NULL, question_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DADD4A251E27F6BF ON answer (question_id)');
        $this->addSql('CREATE TABLE question (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE test_result (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_answer (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, is_correct BOOLEAN NOT NULL, test_result_id INT NOT NULL, question_id INT NOT NULL, answer_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF8F5118853A2189 ON user_answer (test_result_id)');
        $this->addSql('CREATE INDEX IDX_BF8F51181E27F6BF ON user_answer (question_id)');
        $this->addSql('CREATE INDEX IDX_BF8F5118AA334807 ON user_answer (answer_id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118853A2189 FOREIGN KEY (test_result_id) REFERENCES test_result (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F51181E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP CONSTRAINT FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE user_answer DROP CONSTRAINT FK_BF8F5118853A2189');
        $this->addSql('ALTER TABLE user_answer DROP CONSTRAINT FK_BF8F51181E27F6BF');
        $this->addSql('ALTER TABLE user_answer DROP CONSTRAINT FK_BF8F5118AA334807');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE test_result');
        $this->addSql('DROP TABLE user_answer');
    }
}
