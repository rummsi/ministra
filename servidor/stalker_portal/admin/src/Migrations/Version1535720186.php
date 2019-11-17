<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720186 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('notification_feed')->hasColumn('deleted')) {
            $this->addSql('ALTER TABLE `notification_feed` ADD COLUMN `deleted` tinyint NOT NULL default 0 AFTER `read`;');
        }
        $this->addSql(<<<EOL
--

update `notification_feed` set `deleted` = 0 where `read` = 0;
update `notification_feed` set `deleted` = 1 where `read` = 1;
ALTER TABLE `notification_feed` ADD INDEX `deleted` (`deleted`);
INSERT INTO `adm_grp_action_access`
        (`controller_name`,            `action_name`, `is_ajax`, `description`,                                 `hidden`,`only_top_admin`)
VALUES  ('index',           'note-list-mark-deleted',         1, 'Mark notification as deleted, set "deleted" flag',     1,               1);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `notification_feed` DROP INDEX `deleted`;
ALTER TABLE `notification_feed` DROP COLUMN `deleted`;
--
EOL
);
    }
}
