<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720179 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `media_claims` ADD INDEX `media_type_id` (`media_type` ASC, `media_id` ASC);
ALTER TABLE `video_series_files` ADD INDEX `video_id_idx` (`video_id` ASC);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `media_claims` DROP INDEX `media_type_id`;
ALTER TABLE `video_series_files` DROP INDEX `video_id_idx`;
--
EOL
);
    }
}
