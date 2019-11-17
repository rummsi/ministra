<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719955 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('video')->hasColumn('rating_last_update')) {
            $this->addSql('ALTER TABLE `video` ADD `rating_last_update` timestamp null default null;');
        }
        if (!$schema->getTable('video')->hasColumn('rating_count_imdb')) {
            $this->addSql("ALTER TABLE `video` ADD `rating_count_imdb` varchar(64) not null default '';");
        }
        if (!$schema->getTable('video')->hasColumn('rating_imdb')) {
            $this->addSql("ALTER TABLE `video` ADD `rating_imdb` varchar(64) not null default '';");
        }
        if (!$schema->getTable('video')->hasColumn('rating_count_kinopoisk')) {
            $this->addSql("ALTER TABLE `video` ADD `rating_count_kinopoisk` varchar(64) not null default '';");
        }
        if (!$schema->getTable('video')->hasColumn('rating_kinopoisk')) {
            $this->addSql("ALTER TABLE `video` ADD `rating_kinopoisk` varchar(64) not null default '';");
        }
        if (!$schema->getTable('video')->hasColumn('kinopoisk_id')) {
            $this->addSql("ALTER TABLE `video` ADD `kinopoisk_id` varchar(64) not null default '';");
        }
        if (!$schema->getTable('storages')->hasColumn('fake_tv_archive')) {
            $this->addSql('ALTER TABLE `storages` ADD `fake_tv_archive` tinyint default 0;');
        }
        $this->addSql(<<<EOL
--

CREATE TEMPORARY TABLE `tmp_itv_subscription` AS SELECT * FROM `itv_subscription` GROUP BY `uid`;
TRUNCATE `itv_subscription`;
ALTER TABLE `itv_subscription` DROP INDEX `uid`;
ALTER TABLE `itv_subscription` ADD UNIQUE INDEX (`uid`);
INSERT INTO `itv_subscription` SELECT * FROM `tmp_itv_subscription`;
DROP TABLE `tmp_itv_subscription`;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `storages` DROP `fake_tv_archive`;
ALTER TABLE `video` DROP `kinopoisk_id`;
ALTER TABLE `video` DROP `rating_kinopoisk`;
ALTER TABLE `video` DROP `rating_count_kinopoisk`;
ALTER TABLE `video` DROP `rating_imdb`;
ALTER TABLE `video` DROP `rating_count_imdb`;
ALTER TABLE `video` DROP `rating_last_update`;
--
EOL
);
    }
}
