<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720174 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE `filters` SET `title`= 'Status' WHERE `title` = 'State' AND `method` = 'getUsersByStatus';
UPDATE `filters` SET `title`= 'State'  WHERE `title` = 'Status'  AND `method` = 'getUsersByState';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
