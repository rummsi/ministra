<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720183 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('smac_certificates')->hasColumn('repaired_date')) {
            $this->addSql('ALTER TABLE `smac_certificates` ADD `repaired_date` INT DEFAULT 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `smac_certificates` DROP `repaired_date`;
--
EOL
);
    }
}
