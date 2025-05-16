<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516071422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE classroom (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, TEACHER_ID INT NOT NULL, INDEX IDX_497D309D725225C2 (TEACHER_ID), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE classroom_user (classroom_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7499B21D6278D5A8 (classroom_id), INDEX IDX_7499B21DA76ED395 (user_id), PRIMARY KEY(classroom_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, completed TINYINT(1) NOT NULL, date DATE NOT NULL, USER_ID INT NOT NULL, CLASSROOM_ID INT NOT NULL, INDEX IDX_527EDB25A0666B6F (USER_ID), INDEX IDX_527EDB25CF07DA58 (CLASSROOM_ID), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(10) NOT NULL, first_name VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom ADD CONSTRAINT FK_497D309D725225C2 FOREIGN KEY (TEACHER_ID) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom_user ADD CONSTRAINT FK_7499B21D6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom_user ADD CONSTRAINT FK_7499B21DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD CONSTRAINT FK_527EDB25A0666B6F FOREIGN KEY (USER_ID) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD CONSTRAINT FK_527EDB25CF07DA58 FOREIGN KEY (CLASSROOM_ID) REFERENCES classroom (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom DROP FOREIGN KEY FK_497D309D725225C2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom_user DROP FOREIGN KEY FK_7499B21D6278D5A8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE classroom_user DROP FOREIGN KEY FK_7499B21DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP FOREIGN KEY FK_527EDB25A0666B6F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP FOREIGN KEY FK_527EDB25CF07DA58
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE classroom
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE classroom_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE task
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
