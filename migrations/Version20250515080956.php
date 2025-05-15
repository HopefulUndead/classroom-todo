<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515080956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_classroom (user_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_499DBD79A76ED395 (user_id), INDEX IDX_499DBD796278D5A8 (classroom_id), PRIMARY KEY(user_id, classroom_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_classroom ADD CONSTRAINT FK_499DBD79A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_classroom ADD CONSTRAINT FK_499DBD796278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD USER_ID INT NOT NULL, ADD CLASSROOM_ID INT NOT NULL, DROP id_user, DROP id_class
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD CONSTRAINT FK_527EDB25A0666B6F FOREIGN KEY (USER_ID) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD CONSTRAINT FK_527EDB25CF07DA58 FOREIGN KEY (CLASSROOM_ID) REFERENCES classroom (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB25A0666B6F ON task (USER_ID)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB25CF07DA58 ON task (CLASSROOM_ID)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_classroom DROP FOREIGN KEY FK_499DBD79A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_classroom DROP FOREIGN KEY FK_499DBD796278D5A8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_classroom
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP FOREIGN KEY FK_527EDB25A0666B6F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP FOREIGN KEY FK_527EDB25CF07DA58
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB25A0666B6F ON task
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB25CF07DA58 ON task
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD id_user INT NOT NULL, ADD id_class INT NOT NULL, DROP USER_ID, DROP CLASSROOM_ID
        SQL);
    }
}
