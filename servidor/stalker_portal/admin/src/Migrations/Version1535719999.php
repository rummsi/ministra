<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719999 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('video_clock')) {
            $this->addSql("ALTER TABLE `users` ADD `video_clock` varchar(9) NOT NULL default 'Off';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `video_clock`;
--
EOL
);
    }
}
