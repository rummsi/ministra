<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720049 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE `adm_grp_action_access` SET `action_name`= 'users-filter-list' WHERE `action_name`= 'users-filters-list';
UPDATE `adm_grp_action_access` SET `action_name`= 'users-filter-list-json' WHERE `action_name`= 'users-filters-list-json';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
