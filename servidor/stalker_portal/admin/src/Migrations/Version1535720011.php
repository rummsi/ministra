<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720011 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('tv_reminder')->hasColumn('tv_program_name')) {
            $this->addSql('ALTER TABLE `tv_reminder` ADD `tv_program_name` text;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `tv_reminder` DROP `tv_program_name`;
--
EOL
);
    }
}
