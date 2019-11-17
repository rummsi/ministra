<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190909093657 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
UPDATE `adm_grp_action_access` SET `action_name`='check-package-name' 
where `controller_name`='tariffs' and `action_name`='check-package-name ';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
    public function getDescription()
    {
        return 'Update action names for tariffs/check-package-name';
    }
}
