<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720137 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('ext_adv_campaigns_position')->hasColumn('skip_after')) {
            $this->addSql('ALTER TABLE `ext_adv_campaigns_position` ADD COLUMN `skip_after` TINYINT NOT NULL DEFAULT 7;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `ext_adv_campaigns_position` DROP COLUMN `skip_after`;
--
EOL
);
    }
}
