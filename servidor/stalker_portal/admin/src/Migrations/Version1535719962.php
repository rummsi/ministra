<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719962 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('last_id')->hasColumn('uid')) {
            $this->addSql('ALTER TABLE `last_id` ADD `uid` int not null default 0;');
        }
        $this->addSql(<<<EOL
--

UPDATE `last_id` SET `uid`=(SELECT id FROM `users` where mac=ident limit 1);
ALTER TABLE `last_id` DROP KEY `ident`;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `last_id` DROP `uid`;
ALTER TABLE `last_id` ADD UNIQUE KEY (`ident`);
--
EOL
);
    }
}
