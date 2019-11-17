<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720030 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `fav_radio` (
  `id`        INT          NOT NULL AUTO_INCREMENT,
  `uid`       INT UNSIGNED NOT NULL,
  `fav_radio` TEXT         NULL,
  `addtime`   DATETIME     NULL,
  `edittime`  TIMESTAMP    NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC)
) DEFAULT CHARSET = utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE IF EXISTS `fav_radio`;
--
EOL
);
    }
}
