<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118152845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Notes (id SERIAL NOT NULL, student_entity_id INT NOT NULL, related_teacher_id INT NOT NULL, value INT NOT NULL, appreciation VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C0DA8988E64BBCDD ON Notes (student_entity_id)');
        $this->addSql('CREATE INDEX IDX_C0DA8988CB4D76AD ON Notes (related_teacher_id)');
        $this->addSql('COMMENT ON COLUMN Notes.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "account_validation" (id SERIAL NOT NULL, relateduservalidation_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, activation_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA37A9CA934ED376 ON "account_validation" (relateduservalidation_id)');
        $this->addSql('COMMENT ON COLUMN "account_validation".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "account_validation".activation_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE assignment_entity (id SERIAL NOT NULL, relatedstudent_id INT DEFAULT NULL, teacher_who_assign_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, deliverydate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_360964752EC9B21C ON assignment_entity (relatedstudent_id)');
        $this->addSql('CREATE INDEX IDX_360964755788B718 ON assignment_entity (teacher_who_assign_id)');
        $this->addSql('COMMENT ON COLUMN assignment_entity.deliverydate IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE class_entity (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, level VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE message_entity (id SERIAL NOT NULL, parent_handle_message_id INT NOT NULL, content VARCHAR(255) NOT NULL, send_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sender UUID NOT NULL, receiver UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_390FD96731657FFB ON message_entity (parent_handle_message_id)');
        $this->addSql('COMMENT ON COLUMN message_entity.sender IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message_entity.receiver IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE non_attendance_entity (id SERIAL NOT NULL, related_student_id INT NOT NULL, non_attendance_teacher_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, reason VARCHAR(255) DEFAULT NULL, justified BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_52348B45415947AA ON non_attendance_entity (related_student_id)');
        $this->addSql('CREATE INDEX IDX_52348B45D819801 ON non_attendance_entity (non_attendance_teacher_id)');
        $this->addSql('COMMENT ON COLUMN non_attendance_entity.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE subject_entity (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, name_of_class_id INT DEFAULT NULL, parentof_student_id INT DEFAULT NULL, student_class_name_id INT NOT NULL, username VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_active BOOLEAN DEFAULT NULL, discriminator VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE INDEX IDX_8D93D64955B5B127 ON "user" (name_of_class_id)');
        $this->addSql('CREATE INDEX IDX_8D93D64928F5B108 ON "user" (parentof_student_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649E1FE03B4 ON "user" (student_class_name_id)');
        $this->addSql('ALTER TABLE Notes ADD CONSTRAINT FK_C0DA8988E64BBCDD FOREIGN KEY (student_entity_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE Notes ADD CONSTRAINT FK_C0DA8988CB4D76AD FOREIGN KEY (related_teacher_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "account_validation" ADD CONSTRAINT FK_EA37A9CA934ED376 FOREIGN KEY (relateduservalidation_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assignment_entity ADD CONSTRAINT FK_360964752EC9B21C FOREIGN KEY (relatedstudent_id) REFERENCES subject_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assignment_entity ADD CONSTRAINT FK_360964755788B718 FOREIGN KEY (teacher_who_assign_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_entity ADD CONSTRAINT FK_390FD96731657FFB FOREIGN KEY (parent_handle_message_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE non_attendance_entity ADD CONSTRAINT FK_52348B45415947AA FOREIGN KEY (related_student_id) REFERENCES subject_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE non_attendance_entity ADD CONSTRAINT FK_52348B45D819801 FOREIGN KEY (non_attendance_teacher_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64955B5B127 FOREIGN KEY (name_of_class_id) REFERENCES class_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64928F5B108 FOREIGN KEY (parentof_student_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649E1FE03B4 FOREIGN KEY (student_class_name_id) REFERENCES class_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE Notes DROP CONSTRAINT FK_C0DA8988E64BBCDD');
        $this->addSql('ALTER TABLE Notes DROP CONSTRAINT FK_C0DA8988CB4D76AD');
        $this->addSql('ALTER TABLE "account_validation" DROP CONSTRAINT FK_EA37A9CA934ED376');
        $this->addSql('ALTER TABLE assignment_entity DROP CONSTRAINT FK_360964752EC9B21C');
        $this->addSql('ALTER TABLE assignment_entity DROP CONSTRAINT FK_360964755788B718');
        $this->addSql('ALTER TABLE message_entity DROP CONSTRAINT FK_390FD96731657FFB');
        $this->addSql('ALTER TABLE non_attendance_entity DROP CONSTRAINT FK_52348B45415947AA');
        $this->addSql('ALTER TABLE non_attendance_entity DROP CONSTRAINT FK_52348B45D819801');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64955B5B127');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64928F5B108');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649E1FE03B4');
        $this->addSql('DROP TABLE Notes');
        $this->addSql('DROP TABLE "account_validation"');
        $this->addSql('DROP TABLE assignment_entity');
        $this->addSql('DROP TABLE class_entity');
        $this->addSql('DROP TABLE message_entity');
        $this->addSql('DROP TABLE non_attendance_entity');
        $this->addSql('DROP TABLE subject_entity');
        $this->addSql('DROP TABLE "user"');
    }
}
