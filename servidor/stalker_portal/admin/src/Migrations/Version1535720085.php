<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720085 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('tv_genre')->hasColumn('censored')) {
            $this->addSql('ALTER TABLE `tv_genre` ADD COLUMN `censored` TINYINT NOT NULL  DEFAULT 0;');
        }
        $this->addSql(<<<EOL
--

ALTER TABLE `media_category` ADD COLUMN `censored` TINYINT NOT NULL DEFAULT 0;
UPDATE `tv_genre` SET `censored` = 1 WHERE `title` = 'for adults';
UPDATE `media_category` SET `censored` = 1 WHERE `category_name` = 'adult';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `tv_genre` DROP COLUMN `censored`;
ALTER TABLE `media_category` DROP COLUMN `censored`;
--
EOL
);
    }
}
