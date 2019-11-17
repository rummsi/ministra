<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720072 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE `adm_grp_action_access` SET `description` = 'Publish or add to schedule' WHERE `action_name` = 'enable-video';
UPDATE `adm_grp_action_access` SET `description` = 'Unpublish video' WHERE `action_name` = 'disable-video';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
