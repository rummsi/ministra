<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720090 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('video')->hasColumn('autocomplete_provider')) {
            $this->addSql("ALTER TABLE `video` ADD COLUMN `autocomplete_provider` ENUM('kinopoisk', 'tmdb');");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `video` DROP COLUMN `autocomplete_provider`;
--
EOL
);
    }
}
