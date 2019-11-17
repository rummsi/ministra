<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720079 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
          (`controller_name`, `action_name`,                        `is_ajax`, `description`)
VALUES    ('users',    'tariff-and-service-control',                        0, 'Setting the tariff plans and additional services for users on edit page'),
          ('users',    'billing-date-control',                              0, 'Managing of billing date for users on edit page'),
          ('users',    'user-reseller-control',                             0, 'Managing of users reseller on edit page');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
