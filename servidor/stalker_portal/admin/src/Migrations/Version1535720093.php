<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720093 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('adm_grp_action_access')->hasColumn('blocked')) {
            $this->addSql('ALTER TABLE `adm_grp_action_access` ADD COLUMN `blocked` TINYINT NOT NULL DEFAULT 0;');
        }
        $this->addSql(<<<EOL
--

UPDATE `adm_grp_action_access` SET `blocked` = 1 WHERE `controller_name` = 'video-club';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `adm_grp_action_access` DROP COLUMN `blocked`;
--
EOL
);
    }
}
