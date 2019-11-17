<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720150 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `smac_certificates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fingerprint` VARCHAR(64) NOT NULL DEFAULT '',
  `body` BLOB,
  `added` TIMESTAMP NULL DEFAULT NULL,
  `updated` TIMESTAMP NULL DEFAULT NULL,
  INDEX `fingerprint` (`fingerprint`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `smac_certificates`;
--
EOL
);
    }
}
