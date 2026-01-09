<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109081031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, responsable_id INT NOT NULL, INDEX IDX_3BAE0AA753C59D72 (responsable_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA753C59D72 FOREIGN KEY (responsable_id) REFERENCES `user` (id)');
        $this->addSql('DROP INDEX IDX_794381C6A76ED395 ON review');
        $this->addSql('ALTER TABLE review ADD content LONGTEXT NOT NULL, ADD is_validated TINYINT NOT NULL, ADD author_id INT NOT NULL, ADD event_id INT NOT NULL, CHANGE user_id rating INT NOT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C671F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_794381C6F675F31B ON review (author_id)');
        $this->addSql('CREATE INDEX IDX_794381C671F7E88B ON review (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA753C59D72');
        $this->addSql('DROP TABLE event');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6F675F31B');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C671F7E88B');
        $this->addSql('DROP INDEX IDX_794381C6F675F31B ON review');
        $this->addSql('DROP INDEX IDX_794381C671F7E88B ON review');
        $this->addSql('ALTER TABLE review ADD user_id INT NOT NULL, DROP content, DROP rating, DROP is_validated, DROP author_id, DROP event_id');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
    }
}
