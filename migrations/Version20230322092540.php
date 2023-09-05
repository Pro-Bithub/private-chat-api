<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322092540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('ALTER TABLE accounts CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE clickable_links CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE clickable_links_users CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE contact_custom_fields DROP FOREIGN KEY FK_6BD0740BE7A1254A');
        $this->addSql('ALTER TABLE contact_custom_fields DROP FOREIGN KEY FK_6BD0740BF50D82F4');
        $this->addSql('DROP INDEX IDX_6BD0740BE7A1254A ON contact_custom_fields');
        $this->addSql('DROP INDEX IDX_6BD0740BF50D82F4 ON contact_custom_fields');
        $this->addSql('ALTER TABLE contact_custom_fields CHANGE contact_id contact_id VARCHAR(255) NOT NULL, CHANGE form_field_id form_field_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE contact_form_fields CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE contact_forms CHANGE form_type form_type VARCHAR(255) NOT NULL, CHANGE waiting_time waiting_time VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE contacts CHANGE gender gender VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE custom_fields CHANGE field_type field_type VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE landing_page_fields CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE landing_pages CHANGE comment comment VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE messages CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE plan_discount_users CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE plan_discounts CHANGE discount_type discount_type VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL, CHANGE discount_value discount_value VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE plan_users CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE plans CHANGE billing_type billing_type VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE predefind_texts CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE predefined_text_users CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE registrations DROP FOREIGN KEY FK_53DE51E79B6B5FBA');
        $this->addSql('DROP INDEX IDX_53DE51E79B6B5FBA ON registrations');
        $this->addSql('ALTER TABLE registrations CHANGE account_id account_id VARCHAR(128) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sales CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL, CHANGE roles roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE password password VARCHAR(255) NOT NULL, CHANGE account_id account_id VARCHAR(255) NOT NULL, CHANGE notification_mail notification_mail VARCHAR(255) DEFAULT NULL, CHANGE notification_audio notification_audio VARCHAR(255) DEFAULT NULL, CHANGE notification_browser notification_browser VARCHAR(255) DEFAULT NULL, CHANGE shortcut shortcut VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE date_start date_start DATE NOT NULL, CHANGE gender gender VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_logs CHANGE element element VARCHAR(255) NOT NULL, CHANGE source source VARCHAR(255) NOT NULL, CHANGE browser browser VARCHAR(84) DEFAULT NULL, CHANGE location location VARCHAR(84) DEFAULT NULL, CHANGE device device VARCHAR(84) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_notifications CHANGE email_notifications email_notifications VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_permissions CHANGE pre_defined_messages pre_defined_messages VARCHAR(255) NOT NULL, CHANGE planning_management planning_management VARCHAR(255) NOT NULL, CHANGE package_creation package_creation VARCHAR(255) NOT NULL, CHANGE package_visibility package_visibility VARCHAR(255) NOT NULL, CHANGE visitors_rating visitors_rating VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_planning CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_presentations CHANGE gender gender VARCHAR(255) NOT NULL, CHANGE role role VARCHAR(255) NOT NULL, CHANGE atrological_sign atrological_sign VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_rights CHANGE contact_gender contact_gender VARCHAR(255) NOT NULL, CHANGE contact_firstname contact_firstname VARCHAR(255) NOT NULL, CHANGE contact_lastname contact_lastname VARCHAR(255) NOT NULL, CHANGE contact_name contact_name VARCHAR(255) NOT NULL, CHANGE contact_phone contact_phone VARCHAR(255) NOT NULL, CHANGE contact_country contact_country VARCHAR(255) NOT NULL, CHANGE contact_address contact_address VARCHAR(255) NOT NULL, CHANGE contact_ipaddress contact_ipaddress VARCHAR(255) NOT NULL, CHANGE contact_request_category contact_request_category VARCHAR(255) NOT NULL, CHANGE contact_request contact_request VARCHAR(255) NOT NULL, CHANGE contact_origin contact_origin VARCHAR(255) NOT NULL, CHANGE contact_date_of_birth contact_date_of_birth VARCHAR(255) NOT NULL, CHANGE contact_company_name contact_company_name VARCHAR(255) NOT NULL, CHANGE contact_custom_fields contact_custom_fields VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL, CHANGE date_end date_end DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, username VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, valid DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE custom_fields CHANGE field_type field_type SMALLINT DEFAULT NULL, CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE clickable_links CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE predefind_texts CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE plan_discounts CHANGE discount_type discount_type SMALLINT NOT NULL, CHANGE discount_value discount_value VARCHAR(255) DEFAULT NULL, CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE user_rights CHANGE contact_gender contact_gender SMALLINT NOT NULL, CHANGE contact_firstname contact_firstname SMALLINT NOT NULL, CHANGE contact_lastname contact_lastname SMALLINT NOT NULL, CHANGE contact_name contact_name SMALLINT NOT NULL, CHANGE contact_phone contact_phone SMALLINT NOT NULL, CHANGE contact_country contact_country SMALLINT NOT NULL, CHANGE contact_address contact_address SMALLINT NOT NULL, CHANGE contact_ipaddress contact_ipaddress SMALLINT NOT NULL, CHANGE contact_request_category contact_request_category SMALLINT NOT NULL, CHANGE contact_request contact_request SMALLINT NOT NULL, CHANGE contact_origin contact_origin SMALLINT NOT NULL, CHANGE contact_date_of_birth contact_date_of_birth SMALLINT NOT NULL, CHANGE contact_company_name contact_company_name SMALLINT NOT NULL, CHANGE contact_custom_fields contact_custom_fields SMALLINT NOT NULL, CHANGE status status SMALLINT NOT NULL, CHANGE date_end date_end DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE landing_page_fields CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE landing_pages CHANGE comment comment VARCHAR(500) DEFAULT NULL, CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE plan_users CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE plan_discount_users CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE registrations CHANGE account_id account_id INT DEFAULT NULL, CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE registrations ADD CONSTRAINT FK_53DE51E79B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id)');
        $this->addSql('CREATE INDEX IDX_53DE51E79B6B5FBA ON registrations (account_id)');
        $this->addSql('ALTER TABLE predefined_text_users CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE sales CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE user_notifications CHANGE email_notifications email_notifications VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_permissions CHANGE pre_defined_messages pre_defined_messages SMALLINT NOT NULL, CHANGE planning_management planning_management SMALLINT NOT NULL, CHANGE package_creation package_creation SMALLINT NOT NULL, CHANGE package_visibility package_visibility VARCHAR(5) NOT NULL, CHANGE visitors_rating visitors_rating SMALLINT NOT NULL, CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE `user` CHANGE email email VARCHAR(180) DEFAULT NULL, CHANGE roles roles LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL, CHANGE notification_mail notification_mail SMALLINT DEFAULT NULL, CHANGE notification_audio notification_audio SMALLINT DEFAULT NULL, CHANGE notification_browser notification_browser SMALLINT DEFAULT NULL, CHANGE shortcut shortcut SMALLINT DEFAULT NULL, CHANGE status status SMALLINT DEFAULT NULL, CHANGE date_start date_start DATE DEFAULT NULL, CHANGE gender gender SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE messages CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE plans CHANGE billing_type billing_type SMALLINT NOT NULL, CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE clickable_links_users CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE accounts CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE user_planning CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE contact_forms CHANGE form_type form_type SMALLINT NOT NULL, CHANGE waiting_time waiting_time SMALLINT DEFAULT NULL, CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE contact_custom_fields CHANGE contact_id contact_id INT DEFAULT NULL, CHANGE form_field_id form_field_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_custom_fields ADD CONSTRAINT FK_6BD0740BE7A1254A FOREIGN KEY (contact_id) REFERENCES contacts (id)');
        $this->addSql('ALTER TABLE contact_custom_fields ADD CONSTRAINT FK_6BD0740BF50D82F4 FOREIGN KEY (form_field_id) REFERENCES contact_form_fields (id)');
        $this->addSql('CREATE INDEX IDX_6BD0740BE7A1254A ON contact_custom_fields (contact_id)');
        $this->addSql('CREATE INDEX IDX_6BD0740BF50D82F4 ON contact_custom_fields (form_field_id)');
        $this->addSql('ALTER TABLE user_presentations CHANGE gender gender VARCHAR(1) NOT NULL, CHANGE role role VARCHAR(1) NOT NULL, CHANGE atrological_sign atrological_sign VARCHAR(1) DEFAULT NULL, CHANGE status status VARCHAR(1) NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_form_fields CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE user_logs CHANGE browser browser VARCHAR(100) DEFAULT NULL, CHANGE device device VARCHAR(255) DEFAULT NULL, CHANGE location location VARCHAR(100) DEFAULT NULL, CHANGE element element VARCHAR(2) NOT NULL COMMENT \'1: account, 2: user, 3: plan,4: plan_users,5: plan_discounts,6: plan_discount_users, 7: clickable_links, 8: clickable_link_users, 9: custom_fields, 10: landing_pages, 11:landing_page_fields, 12: predefined_texts, 13: predefined_text_users, 14: sales, 15: user_notifications, 16: user_permissions, 17: user_planning, 18: user_presentations, 19: user_rights, 20: contacts, 21: contact_forms, 22: contact_form_fields, 23: contact_custom_fields, 24: contact_balances, 25: login, 26: registrations
        \', CHANGE source source VARCHAR(1) NOT NULL');
        $this->addSql('ALTER TABLE contacts CHANGE gender gender SMALLINT NOT NULL, CHANGE status status SMALLINT NOT NULL');
    }
}
