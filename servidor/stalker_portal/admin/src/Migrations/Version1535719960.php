<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719960 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('tariff_plan')->hasColumn('user_default')) {
            $this->addSql('ALTER TABLE `tariff_plan` ADD `user_default` tinyint default 0;');
        }
        $this->addSql(<<<EOL
--
ALTER TABLE `users` MODIFY `ls` VARCHAR(64) NOT NULL DEFAULT '';

EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `tariff_plan` DROP `user_default`;
--
EOL
);
    }
}
