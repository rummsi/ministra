<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720195 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
INSERT INTO `adm_grp_action_access`
(    `controller_name`, `action_name`, `is_ajax`, `description`)
VALUES  ('tariffs', 'check-tariff-name',   1,     'Checking tariff name'),
        ('tariffs', 'check-package-name', 1,     'Checking package number');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DELETE FROM `adm_grp_action_access` WHERE controller_name='tariffs' AND action_name='check-package-name';
DELETE FROM `adm_grp_action_access` WHERE controller_name='tariffs' AND action_name='check-tariff-name';
--
EOL
);
    }
}
