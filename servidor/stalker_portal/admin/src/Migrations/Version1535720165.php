<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720165 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `smac_codes` MODIFY `status` ENUM("Not Activated", "Activated", "Blocked", "Manually entered") DEFAULT "Not Activated";
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `smac_codes` MODIFY `status` ENUM("Not Activated", "Activated", "Blocked") DEFAULT "Not Activated";
--
EOL
);
    }
}
