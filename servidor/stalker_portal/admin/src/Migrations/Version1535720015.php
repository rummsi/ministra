<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720015 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('ch_links')->hasColumn('flussonic_tmp_link')) {
            $this->addSql('ALTER TABLE `ch_links` ADD `flussonic_tmp_link` tinyint default 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `ch_links` DROP `flussonic_tmp_link`;
--
EOL
);
    }
}
