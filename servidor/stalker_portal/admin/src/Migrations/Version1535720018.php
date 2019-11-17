<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720018 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `user_log` MODIFY `param` varchar(255) NOT NULL DEFAULT '';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
