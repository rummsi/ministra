<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720177 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE `adm_grp_action_access` SET `controller_name`=replace(`controller_name`, 'activation-codes', 'license-keys');
UPDATE `adm_grp_action_access` SET `action_name`=replace(`action_name`, 'activation-codes', 'license-keys');
UPDATE `adm_grp_action_access` SET `action_name`=replace(`action_name`, '-code-', '-key-');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
