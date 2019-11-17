<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719979 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('vclub_ad')->hasColumn('status')) {
            $this->addSql('ALTER TABLE `vclub_ad` ADD `status` int NOT NULL DEFAULT 1;');
        }
        $this->addSql(<<<EOL
--

CREATE TABLE IF NOT EXISTS `vclub_ad_deny_category`(
  `id` int NOT NULL auto_increment,
  `ad_id` int NOT NULL default 0,
  `category_id` int NOT NULL default 0,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `vclub_ad` DROP `status`;
DROP TABLE `vclub_ad_deny_category`;
--
EOL
);
    }
}
