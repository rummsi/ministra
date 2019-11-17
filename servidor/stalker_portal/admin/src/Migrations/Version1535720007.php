<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720007 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('sec_subtitle_lang')) {
            $this->addSql("ALTER TABLE `users` ADD `sec_subtitle_lang` varchar(4) NOT NULL default '';");
        }
        if (!$schema->getTable('users')->hasColumn('pri_subtitle_lang')) {
            $this->addSql("ALTER TABLE `users` ADD `pri_subtitle_lang` varchar(4) NOT NULL default '';");
        }
        if (!$schema->getTable('users')->hasColumn('sec_audio_lang')) {
            $this->addSql("ALTER TABLE `users` ADD `sec_audio_lang` varchar(4) NOT NULL default '';");
        }
        if (!$schema->getTable('users')->hasColumn('pri_audio_lang')) {
            $this->addSql("ALTER TABLE `users` ADD `pri_audio_lang` varchar(4) NOT NULL default '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `pri_audio_lang`;
ALTER TABLE `users` DROP `sec_audio_lang`;
ALTER TABLE `users` DROP `pri_subtitle_lang`;
ALTER TABLE `users` DROP `sec_subtitle_lang`;
--
EOL
);
    }
}
