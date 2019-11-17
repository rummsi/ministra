<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720024 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('storages')->hasColumn('flussonic_server')) {
            $this->addSql('ALTER TABLE `storages` ADD `flussonic_server` tinyint default 0;');
        }
        if (!$schema->getTable('itv')->hasColumn('flussonic_dvr')) {
            $this->addSql('ALTER TABLE `itv` ADD `flussonic_dvr` tinyint default 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP `flussonic_dvr`;
ALTER TABLE `storages` DROP `flussonic_server`;
--
EOL
);
    }
}
