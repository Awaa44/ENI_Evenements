<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223141100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lieux ADD villes_id INT NOT NULL');
        $this->addSql('ALTER TABLE lieux ADD CONSTRAINT FK_9E44A8AE286C17BC FOREIGN KEY (villes_id) REFERENCES villes (id)');
        $this->addSql('CREATE INDEX IDX_9E44A8AE286C17BC ON lieux (villes_id)');
        $this->addSql('ALTER TABLE participants ADD sites_id INT NOT NULL');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_716970927838E496 FOREIGN KEY (sites_id) REFERENCES sites (id)');
        $this->addSql('CREATE INDEX IDX_716970927838E496 ON participants (sites_id)');
        $this->addSql('ALTER TABLE sorties ADD etat_sortie INT DEFAULT NULL, ADD organisateur_id INT NOT NULL, ADD etats_id INT NOT NULL, ADD lieux_id INT NOT NULL');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8D936B2FA FOREIGN KEY (organisateur_id) REFERENCES participants (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8CA7E0C2E FOREIGN KEY (etats_id) REFERENCES etats (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8A2C806AC FOREIGN KEY (lieux_id) REFERENCES lieux (id)');
        $this->addSql('CREATE INDEX IDX_488163E8D936B2FA ON sorties (organisateur_id)');
        $this->addSql('CREATE INDEX IDX_488163E8CA7E0C2E ON sorties (etats_id)');
        $this->addSql('CREATE INDEX IDX_488163E8A2C806AC ON sorties (lieux_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lieux DROP FOREIGN KEY FK_9E44A8AE286C17BC');
        $this->addSql('DROP INDEX IDX_9E44A8AE286C17BC ON lieux');
        $this->addSql('ALTER TABLE lieux DROP villes_id');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_716970927838E496');
        $this->addSql('DROP INDEX IDX_716970927838E496 ON participants');
        $this->addSql('ALTER TABLE participants DROP sites_id');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8D936B2FA');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8CA7E0C2E');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8A2C806AC');
        $this->addSql('DROP INDEX IDX_488163E8D936B2FA ON sorties');
        $this->addSql('DROP INDEX IDX_488163E8CA7E0C2E ON sorties');
        $this->addSql('DROP INDEX IDX_488163E8A2C806AC ON sorties');
        $this->addSql('ALTER TABLE sorties DROP etat_sortie, DROP organisateur_id, DROP etats_id, DROP lieux_id');
    }
}
