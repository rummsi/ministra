<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720027 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('storages')->hasColumn('wowza_dvr')) {
            $this->addSql('ALTER TABLE `storages` ADD `wowza_dvr` tinyint default 0;');
        }
        $this->addSql(<<<EOL
--

ALTER TABLE `storages` CHANGE `flussonic_server` `flussonic_dvr` tinyint default 0;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `storages` DROP `wowza_dvr`;
ALTER TABLE `storages` CHANGE `flussonic_dvr` `flussonic_server` tinyint default 0;
--
EOL
);
    }
}
