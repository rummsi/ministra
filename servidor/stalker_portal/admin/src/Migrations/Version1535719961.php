<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719961 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('media_claims')->hasColumn('wrong_epg')) {
            $this->addSql('ALTER TABLE `media_claims` ADD `wrong_epg` int not null default 0;');
        }
        if (!$schema->getTable('media_claims')->hasColumn('no_epg')) {
            $this->addSql('ALTER TABLE `media_claims` ADD `no_epg` int not null default 0;');
        }
        $this->addSql(<<<EOL
--
ALTER TABLE `daily_media_claims` ADD `no_epg` int not null default 0;
ALTER TABLE `daily_media_claims` ADD `wrong_epg` int not null default 0;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `media_claims` DROP `no_epg`;
ALTER TABLE `media_claims` DROP `wrong_epg`;
ALTER TABLE `daily_media_claims` DROP `no_epg`;
ALTER TABLE `daily_media_claims` DROP `wrong_epg`;
--
EOL
);
    }
}
