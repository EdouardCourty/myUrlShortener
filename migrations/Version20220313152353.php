<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220313152353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE link_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE link (id INT NOT NULL, url TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER INDEX uniq_8d93d649f85e0677 RENAME TO UNIQ_88BDF3E9F85E0677');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE link_id_seq CASCADE');
        $this->addSql('DROP TABLE link');
        $this->addSql('ALTER INDEX uniq_88bdf3e9f85e0677 RENAME TO uniq_8d93d649f85e0677');
    }
}
