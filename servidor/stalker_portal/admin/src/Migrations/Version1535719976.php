<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719976 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('ch_link_on_streamer')->hasColumn('monitoring_status')) {
            $this->addSql('ALTER TABLE `ch_link_on_streamer` ADD `monitoring_status` tinyint default 1;');
        }
        if (!$schema->getTable('ch_links')->hasColumn('enable_balancer_monitoring')) {
            $this->addSql('ALTER TABLE `ch_links` ADD `enable_balancer_monitoring` tinyint default 0;');
        }
        $this->addSql(<<<EOL
--
DELETE FROM `ch_links` WHERE `ch_id` not in (SELECT `id` FROM `itv`);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `ch_links` DROP `enable_balancer_monitoring`;
ALTER TABLE `ch_link_on_streamer` DROP `monitoring_status`;
--
EOL
);
    }
}
