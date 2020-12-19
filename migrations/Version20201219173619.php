<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201219173619 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE departments (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, name_department VARCHAR(255) NOT NULL, code_department VARCHAR(255) NOT NULL, INDEX IDX_16AEB8D498260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE likes (id INT AUTO_INCREMENT NOT NULL, shop_like_id INT DEFAULT NULL, user_like_id INT DEFAULT NULL, INDEX IDX_49CA4E7D86A8BA18 (shop_like_id), INDEX IDX_49CA4E7DDD96E438 (user_like_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordered (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, status TINYINT(1) NOT NULL, order_ready TINYINT(1) NOT NULL, INDEX IDX_C3121F994D16C4DD (shop_id), INDEX IDX_C3121F99A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordered_products (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, ordered_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_39EA29254584665A (product_id), INDEX IDX_39EA2925AA60395A (ordered_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, sub_category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, stock INT NOT NULL, designation LONGTEXT DEFAULT NULL, picture VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_B3BA5A5A4D16C4DD (shop_id), INDEX IDX_B3BA5A5AF7BFE87C (sub_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, name_region VARCHAR(255) NOT NULL, code_region VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop_categories (id INT AUTO_INCREMENT NOT NULL, name_category VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop_sub_categories (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, name_sub_category VARCHAR(255) NOT NULL, INDEX IDX_8699D4A34D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shops (id INT AUTO_INCREMENT NOT NULL, trader_id INT DEFAULT NULL, category_id INT NOT NULL, town_id INT DEFAULT NULL, name_shop VARCHAR(255) NOT NULL, picture VARCHAR(255) NOT NULL, presentation LONGTEXT NOT NULL, street_number VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_237A67831273968F (trader_id), INDEX IDX_237A678312469DE2 (category_id), INDEX IDX_237A678375E23604 (town_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE towns (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, name_town VARCHAR(255) NOT NULL, zip_code VARCHAR(5) NOT NULL, INDEX IDX_CAF94E3DAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, town_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, street_number VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64975E23604 (town_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE departments ADD CONSTRAINT FK_16AEB8D498260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D86A8BA18 FOREIGN KEY (shop_like_id) REFERENCES shops (id)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DDD96E438 FOREIGN KEY (user_like_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_C3121F994D16C4DD FOREIGN KEY (shop_id) REFERENCES shops (id)');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_C3121F99A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ordered_products ADD CONSTRAINT FK_39EA29254584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE ordered_products ADD CONSTRAINT FK_39EA2925AA60395A FOREIGN KEY (ordered_id) REFERENCES ordered (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A4D16C4DD FOREIGN KEY (shop_id) REFERENCES shops (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AF7BFE87C FOREIGN KEY (sub_category_id) REFERENCES shop_sub_categories (id)');
        $this->addSql('ALTER TABLE shop_sub_categories ADD CONSTRAINT FK_8699D4A34D16C4DD FOREIGN KEY (shop_id) REFERENCES shops (id)');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A67831273968F FOREIGN KEY (trader_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A678312469DE2 FOREIGN KEY (category_id) REFERENCES shop_categories (id)');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A678375E23604 FOREIGN KEY (town_id) REFERENCES towns (id)');
        $this->addSql('ALTER TABLE towns ADD CONSTRAINT FK_CAF94E3DAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64975E23604 FOREIGN KEY (town_id) REFERENCES towns (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE towns DROP FOREIGN KEY FK_CAF94E3DAE80F5DF');
        $this->addSql('ALTER TABLE ordered_products DROP FOREIGN KEY FK_39EA2925AA60395A');
        $this->addSql('ALTER TABLE ordered_products DROP FOREIGN KEY FK_39EA29254584665A');
        $this->addSql('ALTER TABLE departments DROP FOREIGN KEY FK_16AEB8D498260155');
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A678312469DE2');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AF7BFE87C');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D86A8BA18');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F994D16C4DD');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A4D16C4DD');
        $this->addSql('ALTER TABLE shop_sub_categories DROP FOREIGN KEY FK_8699D4A34D16C4DD');
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A678375E23604');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64975E23604');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DDD96E438');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F99A76ED395');
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A67831273968F');
        $this->addSql('DROP TABLE departments');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE ordered');
        $this->addSql('DROP TABLE ordered_products');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE shop_categories');
        $this->addSql('DROP TABLE shop_sub_categories');
        $this->addSql('DROP TABLE shops');
        $this->addSql('DROP TABLE towns');
        $this->addSql('DROP TABLE user');
    }
}
