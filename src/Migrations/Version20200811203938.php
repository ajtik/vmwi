<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200811203938 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, is_active TINYINT(1) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_x_sshkeys (user_id INT NOT NULL, sshkey_id INT NOT NULL, INDEX IDX_2BB7FC4DA76ED395 (user_id), UNIQUE INDEX UNIQ_2BB7FC4D9778BDF (sshkey_id), PRIMARY KEY(user_id, sshkey_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE virtual_machine (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, os VARCHAR(255) NOT NULL, vcpus SMALLINT NOT NULL, ram VARCHAR(6) NOT NULL, hdd VARCHAR(6) NOT NULL, status VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, ip_v4address VARCHAR(255) NOT NULL, ip_v6address VARCHAR(255) NOT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_23720C4BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sshkey (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, public_key LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE do_virtual_machine (id INT AUTO_INCREMENT NOT NULL, virtual_machine_id INT DEFAULT NULL, do_id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8C0D1432830F82E (virtual_machine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dovirtualmachines_x_dosshkeys (dovirtualmachine_id INT NOT NULL, dosshkey_id INT NOT NULL, INDEX IDX_E5B2613F68244797 (dovirtualmachine_id), INDEX IDX_E5B2613F441894DA (dosshkey_id), PRIMARY KEY(dovirtualmachine_id, dosshkey_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE do_sshkey (id INT AUTO_INCREMENT NOT NULL, sshkey_id INT DEFAULT NULL, do_id INT NOT NULL, UNIQUE INDEX UNIQ_8357A7689778BDF (sshkey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_x_sshkeys ADD CONSTRAINT FK_2BB7FC4DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE users_x_sshkeys ADD CONSTRAINT FK_2BB7FC4D9778BDF FOREIGN KEY (sshkey_id) REFERENCES sshkey (id)');
        $this->addSql('ALTER TABLE virtual_machine ADD CONSTRAINT FK_23720C4BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE do_virtual_machine ADD CONSTRAINT FK_8C0D1432830F82E FOREIGN KEY (virtual_machine_id) REFERENCES virtual_machine (id)');
        $this->addSql('ALTER TABLE dovirtualmachines_x_dosshkeys ADD CONSTRAINT FK_E5B2613F68244797 FOREIGN KEY (dovirtualmachine_id) REFERENCES do_virtual_machine (id)');
        $this->addSql('ALTER TABLE dovirtualmachines_x_dosshkeys ADD CONSTRAINT FK_E5B2613F441894DA FOREIGN KEY (dosshkey_id) REFERENCES do_sshkey (id)');
        $this->addSql('ALTER TABLE do_sshkey ADD CONSTRAINT FK_8357A7689778BDF FOREIGN KEY (sshkey_id) REFERENCES sshkey (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_x_sshkeys DROP FOREIGN KEY FK_2BB7FC4DA76ED395');
        $this->addSql('ALTER TABLE virtual_machine DROP FOREIGN KEY FK_23720C4BA76ED395');
        $this->addSql('ALTER TABLE do_virtual_machine DROP FOREIGN KEY FK_8C0D1432830F82E');
        $this->addSql('ALTER TABLE users_x_sshkeys DROP FOREIGN KEY FK_2BB7FC4D9778BDF');
        $this->addSql('ALTER TABLE do_sshkey DROP FOREIGN KEY FK_8357A7689778BDF');
        $this->addSql('ALTER TABLE dovirtualmachines_x_dosshkeys DROP FOREIGN KEY FK_E5B2613F68244797');
        $this->addSql('ALTER TABLE dovirtualmachines_x_dosshkeys DROP FOREIGN KEY FK_E5B2613F441894DA');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE users_x_sshkeys');
        $this->addSql('DROP TABLE virtual_machine');
        $this->addSql('DROP TABLE sshkey');
        $this->addSql('DROP TABLE do_virtual_machine');
        $this->addSql('DROP TABLE dovirtualmachines_x_dosshkeys');
        $this->addSql('DROP TABLE do_sshkey');
    }
}
