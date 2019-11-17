<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720095 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `playback_sessions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `session_id` CHAR(40) NOT NULL DEFAULT '',
  `user_id` INT NOT NULL DEFAULT 0,
  `type` ENUM('tv-channel', 'video', 'karaoke', 'tv-archive', 'audio', 'pvr') NOT NULL,
  `media_id` INT NOT NULL DEFAULT 0,
  `title` VARCHAR(128) NOT NULL DEFAULT '',
  `streamer_id` INT NOT NULL DEFAULT 0,
  `storage_id` INT NOT NULL DEFAULT 0,
  `started` timestamp null default null,
  PRIMARY KEY (`id`),
  INDEX `session_id` (`session_id`),
  INDEX `user_id` (`user_id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `playback_sessions`;
--
EOL
);
    }
}
