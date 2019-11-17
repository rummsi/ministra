<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719996 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('played_itv')->hasColumn('user_locale')) {
            $this->addSql("ALTER TABLE `played_itv` ADD `user_locale` varchar(64) NOT NULL default '';");
        }
        $this->addSql(<<<EOL
--

UPDATE `played_itv`, `users` SET user_locale = locale WHERE played_itv.uid = users.id;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `played_itv` DROP `user_locale`;
--
EOL
);
    }
}
