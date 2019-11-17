<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720064 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE adm_grp_action_access SET hidden = 1 where controller_name = 'information';
UPDATE adm_grp_action_access SET description = "Logs on/off of user's packages" where controller_name = 'tariffs' AND action_name = 'subscribe-log';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
