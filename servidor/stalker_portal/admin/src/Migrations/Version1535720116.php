<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720116 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('audio_compositions')->hasColumn('duration')) {
            $this->addSql('ALTER TABLE `audio_compositions` ADD COLUMN `duration` INT DEFAULT 0;');
        }
        $this->addSql(<<<EOL
--

INSERT INTO `adm_grp_action_access`
          (`controller_name`,         `action_name`, `is_ajax`, `description`)
VALUES    ('audio-club',      'get-media-info-json',         1, 'Getting audio-info from source');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `audio_compositions` DROP COLUMN `duration`;
--
EOL
);
    }
}
