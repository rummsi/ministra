<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720118 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('ch_links')->hasColumn('edgecast_auth_support')) {
            $this->addSql('ALTER TABLE `ch_links` ADD COLUMN `edgecast_auth_support` TINYINT DEFAULT 0;');
        }
        $this->addSql(<<<EOL
--

ALTER TABLE `video_series_files` CHANGE COLUMN `tmp_link_type` `tmp_link_type` ENUM('flussonic','nginx','wowza', 'edgecast_auth') NULL DEFAULT NULL ;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `ch_links` DROP COLUMN `edgecast_auth_support`;
ALTER TABLE `video_series_files` CHANGE COLUMN `tmp_link_type` `tmp_link_type` ENUM('flussonic','nginx','wowza') NULL DEFAULT NULL ;
--
EOL
);
    }
}
