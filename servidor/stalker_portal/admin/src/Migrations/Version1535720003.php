<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720003 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('verified')) {
            $this->addSql('ALTER TABLE `users` ADD `verified` tinyint NOT NULL;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `verified`;
--
EOL
);
    }
}
