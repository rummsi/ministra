<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719957 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users_rec')->hasColumn('program_real_id')) {
            $this->addSql("ALTER TABLE `users_rec` ADD `program_real_id` varchar(64) not null default '';");
        }
        if (!$schema->getTable('tv_reminder')->hasColumn('tv_program_real_id')) {
            $this->addSql("ALTER TABLE `tv_reminder` ADD `tv_program_real_id` varchar(64) not null default '';");
        }
        if (!$schema->getTable('epg')->hasColumn('real_id')) {
            $this->addSql("ALTER TABLE `epg` ADD `real_id` varchar(64) not null default '';");
        }
        $this->addSql(<<<EOL
--

UPDATE `epg` SET `real_id`=CONCAT(ch_id, '_', UNIX_TIMESTAMP(`time`)), `time`=`time`;

UPDATE `tv_reminder` SET `tv_program_real_id` = (SELECT `real_id` FROM `epg` WHERE `id`=`tv_program_id`);
DELETE FROM `tv_reminder` WHERE `tv_program_real_id`='';

EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `epg` DROP `real_id`;
ALTER TABLE `tv_reminder` DROP `tv_program_real_id`;
ALTER TABLE `users_rec` DROP `program_real_id`;
--
EOL
);
    }
}
