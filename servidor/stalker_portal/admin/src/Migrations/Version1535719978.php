<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719978 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('moderators')->hasColumn('disable_vclub_ad')) {
            $this->addSql('ALTER TABLE `moderators` ADD `disable_vclub_ad` tinyint NOT NULL DEFAULT 0;');
        }
        if (!$schema->getTable('vclub_ad')->hasColumn('weight')) {
            $this->addSql('ALTER TABLE `vclub_ad` ADD `weight` int NOT NULL DEFAULT 1;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `vclub_ad` DROP `weight`;
ALTER TABLE `moderators` DROP `disable_vclub_ad`;
--
EOL
);
    }
}
