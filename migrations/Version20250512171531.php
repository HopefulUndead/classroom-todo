<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512171531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom CHANGE id_teacher TEACHER_ID INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom ADD CONSTRAINT FK_497D309D725225C2 FOREIGN KEY (TEACHER_ID) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_497D309D725225C2 ON classroom (TEACHER_ID)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom DROP FOREIGN KEY FK_497D309D725225C2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_497D309D725225C2 ON classroom
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom CHANGE TEACHER_ID id_teacher INT NOT NULL
        SQL);
    }
}
