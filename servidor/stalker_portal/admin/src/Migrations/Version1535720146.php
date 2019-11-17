<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720146 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('languages')) {
            $this->addSql('ALTER TABLE `itv` ADD COLUMN `languages` TEXT NOT NULL;');
        }
        $this->addSql(<<<EOL
--

INSERT INTO `adm_grp_action_access`
        (`controller_name`,          `action_name`,                `is_ajax`, `description`)
VALUES  ('tv-channels',      'cmd-autodetect-lang',                        1, 'Autodetect language for channel links');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP COLUMN `languages`;
--
EOL
);
    }
}
