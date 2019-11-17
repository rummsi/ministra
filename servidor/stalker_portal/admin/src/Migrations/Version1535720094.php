<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720094 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('added')) {
            $this->addSql('ALTER TABLE `itv` ADD COLUMN `added` DATETIME DEFAULT NULL;');
        }
        $this->addSql(<<<EOL
--

CREATE TABLE `deleted_channels` (
  `id`      INT         NOT NULL AUTO_INCREMENT,
  `ch_id`   INT NOT NULL DEFAULT 0,
  `deleted` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP COLUMN `added`;
DROP TABLE `deleted_channels`;
--
EOL
);
    }
}
