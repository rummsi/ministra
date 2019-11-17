<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720084 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('ch_links')->hasColumn('xtream_codes_support')) {
            $this->addSql('ALTER TABLE `ch_links` ADD COLUMN `xtream_codes_support` TINYINT DEFAULT 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `ch_links` DROP COLUMN `xtream_codes_support`;
--
EOL
);
    }
}
