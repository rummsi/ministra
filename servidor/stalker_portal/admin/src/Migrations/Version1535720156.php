<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720156 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('ch_links')->hasColumn('wowza_securetoken')) {
            $this->addSql('ALTER TABLE `ch_links` ADD `wowza_securetoken` tinyint default 0;');
        }
        $this->addSql(<<<EOL
--

ALTER TABLE `video_series_files` MODIFY COLUMN `tmp_link_type` ENUM('flussonic','nginx','wowza', 'edgecast_auth', 'nimble', 'akamai_auth', 'wowza_securetoken') NULL DEFAULT NULL;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `ch_links` DROP `wowza_securetoken`;
ALTER TABLE `video_series_files` MODIFY COLUMN `tmp_link_type` ENUM('flussonic','nginx','wowza', 'edgecast_auth', 'nimble', 'akamai_auth') NULL DEFAULT NULL;
--
EOL
);
    }
}
