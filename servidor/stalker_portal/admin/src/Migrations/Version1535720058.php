<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720058 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `users_activity` CHANGE COLUMN `users_online` `users_online` VARCHAR(512) NOT NULL DEFAULT '{}' ;
UPDATE `users_activity` SET `users_online` = CONCAT("{'total':", `users_online`, "}"), `time` = `time`;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users_activity` CHANGE COLUMN `users_online` `users_online` INT(11) NOT NULL DEFAULT '0' ;
--
EOL
);
    }
}
