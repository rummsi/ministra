<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720139 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('stream_zones')->hasColumn('type')) {
            $this->addSql("ALTER TABLE `stream_zones` ADD COLUMN `type` ENUM ('country', 'ip') DEFAULT 'country';");
        }
        $this->addSql(<<<EOL
--

CREATE TABLE `ips_in_zone` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ip` VARCHAR(128) NOT NULL DEFAULT '',
  `zone_id` INT NOT NULL DEFAULT 0,
  KEY `zone_id` (`zone_id`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
ALTER TABLE `countries_in_zone` DROP KEY `country_id`;
ALTER TABLE `countries_in_zone` ADD KEY `zone_id` (`zone_id`);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `stream_zones` DROP COLUMN `type`;
DROP TABLE `ips_in_zone`;
ALTER TABLE `countries_in_zone` ADD KEY `country_id` (`country_id`);
ALTER TABLE `countries_in_zone` DROP KEY `zone_id`;
--
EOL
);
    }
}
