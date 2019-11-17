<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720014 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `epg` MODIFY `time` timestamp null default null;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
