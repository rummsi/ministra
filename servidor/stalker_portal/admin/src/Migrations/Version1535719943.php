<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719943 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('monitoring_url')) {
            $this->addSql("ALTER TABLE `itv` ADD `monitoring_url` varchar(128) NOT NULL default '';");
        }
        if (!$schema->getTable('itv')->hasColumn('enable_monitoring')) {
            $this->addSql('ALTER TABLE `itv` ADD `enable_monitoring` tinyint default 0;');
        }
        if (!$schema->getTable('itv')->hasColumn('monitoring_status_updated')) {
            $this->addSql('ALTER TABLE `itv` ADD `monitoring_status_updated` datetime;');
        }
        if (!$schema->getTable('itv')->hasColumn('monitoring_status')) {
            $this->addSql('ALTER TABLE `itv` ADD `monitoring_status` tinyint default 0;');
        }
        if (!$schema->getTable('itv')->hasColumn('wowza_dvr')) {
            $this->addSql('ALTER TABLE `itv` ADD `wowza_dvr` tinyint default 0;');
        }
        if (!$schema->getTable('itv')->hasColumn('wowza_tmp_link')) {
            $this->addSql('ALTER TABLE `itv` ADD `wowza_tmp_link` tinyint default 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP `wowza_tmp_link`;
ALTER TABLE `itv` DROP `wowza_dvr`;
ALTER TABLE `itv` DROP `monitoring_status`;
ALTER TABLE `itv` DROP `monitoring_status_updated`;
ALTER TABLE `itv` DROP `enable_monitoring`;
ALTER TABLE `itv` DROP `monitoring_url`;
--
EOL
);
    }
}
