<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720188 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `resellers_ips_ranges` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `reseller_id` INT NOT NULL DEFAULT 0,
  `ip_range` VARCHAR(32) NOT NULL DEFAULT '',
  `calculated_range_begin` INT UNSIGNED NOT NULL DEFAULT 0,
  `calculated_range_end` INT UNSIGNED NOT NULL DEFAULT 0,
  INDEX `idx_reseller_id` (`reseller_id`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
ALTER TABLE users ADD COLUMN last_change_ip VARCHAR(64) NOT NULL DEFAULT '{}';
ALTER TABLE reseller ADD COLUMN use_ip_ranges TINYINT NOT NULL DEFAULT 0;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `resellers_ips_ranges`;
ALTER TABLE users DROP COLUMN last_change_ip;
ALTER TABLE reseller DROP COLUMN use_ip_ranges;
--
EOL
);
    }
}
