<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720113 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `played_video` ADD INDEX `uid_index` (`uid` ASC);
ALTER TABLE `played_itv` ADD INDEX `uid_index` (`uid` ASC);
ALTER TABLE `played_tv_archive` ADD INDEX `uid_index` (`uid` ASC);
ALTER TABLE `media_claims_log` ADD INDEX `uid_index` (`uid` ASC);
ALTER TABLE `played_timeshift` ADD INDEX `uid_index` (`uid` ASC);
ALTER TABLE `readed_anec` ADD INDEX `mac_index` (`mac` ASC);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `played_video` DROP INDEX `uid_index`;
ALTER TABLE `played_itv` DROP INDEX `uid_index`;
ALTER TABLE `played_tv_archive` DROP INDEX `uid_index`;
ALTER TABLE `media_claims_log` DROP INDEX `uid_index`;
ALTER TABLE `played_timeshift` DROP INDEX `uid_index`;
ALTER TABLE `readed_anec` DROP INDEX `mac_index`;
--
EOL
);
    }
}
