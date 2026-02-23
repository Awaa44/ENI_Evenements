<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223132958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY `FK_BB662DEC15DFCFB2`');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY `FK_BB662DEC838709D5`');
        $this->addSql('DROP INDEX IDX_74E0281C15DFCFB2 ON inscriptions');
        $this->addSql('DROP INDEX IDX_74E0281C838709D5 ON inscriptions');
        $this->addSql('ALTER TABLE inscriptions ADD id INT AUTO_INCREMENT NOT NULL, ADD participant_id INT NOT NULL, ADD sortie_id INT NOT NULL, DROP sorties_id, DROP participants_id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C9D1C3019 FOREIGN KEY (participant_id) REFERENCES participants (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281CCC72D953 FOREIGN KEY (sortie_id) REFERENCES sorties (id)');
        $this->addSql('CREATE INDEX IDX_74E0281C9D1C3019 ON inscriptions (participant_id)');
        $this->addSql('CREATE INDEX IDX_74E0281CCC72D953 ON inscriptions (sortie_id)');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY `FK_488163E8D936B2FA`');
        $this->addSql('DROP INDEX IDX_488163E8D936B2FA ON sorties');
        $this->addSql('ALTER TABLE sorties DROP organisateur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C9D1C3019');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281CCC72D953');
        $this->addSql('DROP INDEX IDX_74E0281C9D1C3019 ON inscriptions');
        $this->addSql('DROP INDEX IDX_74E0281CCC72D953 ON inscriptions');
        $this->addSql('ALTER TABLE inscriptions MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE inscriptions ADD sorties_id INT NOT NULL, ADD participants_id INT NOT NULL, DROP id, DROP participant_id, DROP sortie_id, DROP PRIMARY KEY, ADD PRIMARY KEY (sorties_id, participants_id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT `FK_BB662DEC15DFCFB2` FOREIGN KEY (sorties_id) REFERENCES sorties (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT `FK_BB662DEC838709D5` FOREIGN KEY (participants_id) REFERENCES participants (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_74E0281C15DFCFB2 ON inscriptions (sorties_id)');
        $this->addSql('CREATE INDEX IDX_74E0281C838709D5 ON inscriptions (participants_id)');
        $this->addSql('ALTER TABLE sorties ADD organisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT `FK_488163E8D936B2FA` FOREIGN KEY (organisateur_id) REFERENCES participants (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_488163E8D936B2FA ON sorties (organisateur_id)');
    }
}
