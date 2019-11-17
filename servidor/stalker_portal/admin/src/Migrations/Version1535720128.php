<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720128 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('ext_adv_campaigns_position')->hasColumn('blocks')) {
            $this->addSql('ALTER TABLE `ext_adv_campaigns_position` ADD COLUMN `blocks` TINYINT NOT NULL DEFAULT 1;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `ext_adv_campaigns_position` DROP COLUMN `blocks`;
--
EOL
);
    }
}
