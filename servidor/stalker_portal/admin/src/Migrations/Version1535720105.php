<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720105 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
          (`controller_name`,                          `action_name`, `is_ajax`, `description`)
VALUES    ('application-catalog',  'smart-application-update',                1, 'Application for SmartLauncher. Updating applications'),
          ('application-catalog',  'smart-application-check-update',          1, 'Application for SmartLauncher. Checking for updates for applications');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
