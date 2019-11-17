<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720107 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('access_tokens')->hasColumn('last_refresh')) {
            $this->addSql('ALTER TABLE `access_tokens` ADD COLUMN `last_refresh` timestamp null default null;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `access_tokens` DROP COLUMN `last_refresh`;
--
EOL
);
    }
}
