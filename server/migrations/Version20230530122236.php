<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230530122236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE promotion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE promotion (id INT NOT NULL, briefcase_id INT DEFAULT NULL, ticker VARCHAR(50) NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C11D7DD1660C962C ON promotion (briefcase_id)');
        $this->addSql('ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD1660C962C FOREIGN KEY (briefcase_id) REFERENCES briefcase (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE promotion_id_seq CASCADE');
        $this->addSql('ALTER TABLE promotion DROP CONSTRAINT FK_C11D7DD1660C962C');
        $this->addSql('DROP TABLE promotion');
    }
}
