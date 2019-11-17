<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720185 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('karaoke')->hasColumn('protocol_old')) {
            $this->addSql("ALTER TABLE `karaoke` ADD `protocol_old` VARCHAR(64) DEFAULT '';");
        }
        $this->addSql(<<<EOL
--

UPDATE `karaoke` SET  `protocol_old` = `protocol`;
UPDATE `karaoke` SET  `protocol` = IF(`protocol` = 'nfs', 'http', `protocol`);
UPDATE `karaoke` SET  `rtsp_url` = IF(`protocol` = 'http', '', `rtsp_url`);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
UPDATE `karaoke` SET  `protocol` = `protocol_old`;
ALTER TABLE `karaoke` DROP `protocol_old`;
--
EOL
);
    }
}
