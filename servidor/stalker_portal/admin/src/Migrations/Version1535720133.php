<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720133 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `watched_settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `enable_not_ended` TINYINT NOT NULL DEFAULT 1,
  `enable_watched` TINYINT NOT NULL DEFAULT 1,
  `not_ended_history_size` INT NOT NULL DEFAULT 30,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
INSERT INTO `watched_settings` VALUES (null, 1, 1, 30);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `watched_settings`;
--
EOL
);
    }
}
