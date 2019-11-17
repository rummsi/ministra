<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190415124322 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `smac_codes` MODIFY `status` ENUM("Not Activated", "Activated", "Blocked", "Manually entered", "Reserved")
 DEFAULT "Not Activated";
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
    public function getDescription()
    {
        return 'Update license keys statuses';
    }
}
