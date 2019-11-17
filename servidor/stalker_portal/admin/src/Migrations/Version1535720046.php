<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720046 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('events')->hasColumn('post_function')) {
            $this->addSql('ALTER TABLE `events` ADD COLUMN `post_function` VARCHAR(255) NULL;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `events` DROP COLUMN `post_function`;
--
EOL
);
    }
}
