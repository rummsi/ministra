<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720136 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('user_played_movies')->hasColumn('not_ended')) {
            $this->addSql('ALTER TABLE `user_played_movies` ADD COLUMN `not_ended` TINYINT NOT NULL DEFAULT 0 AFTER `watched`;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `user_played_movies` DROP COLUMN `not_ended`;
--
EOL
);
    }
}
