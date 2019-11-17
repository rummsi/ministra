<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720051 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('apps')->hasColumn('name')) {
            $this->addSql("ALTER TABLE `apps` ADD COLUMN `name` VARCHAR(64) NOT NULL DEFAULT '';");
        }
        $this->addSql(<<<EOL
--

CREATE TABLE `github_api_cache` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `etag` varchar(128) NOT NULL DEFAULT '',
  `data` TEXT,
  `updated` timestamp null default null,
  UNIQUE INDEX `url` (`url`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `apps` DROP COLUMN `name`;
DROP TABLE `github_api_cache`;
--
EOL
);
    }
}
