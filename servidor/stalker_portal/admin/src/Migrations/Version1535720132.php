<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720132 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `ext_adv_positions` (`platform`, `position_code`, `label`)
VALUES  ('stb', 104, 'Before starting the TV channel'),
  ('stb', 204, 'Before starting the TV channel'),
  ('stb', 205, 'During a TV channel playback');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DELETE FROM `ext_adv_positions` WHERE `position_code` = 104;
DELETE FROM `ext_adv_positions` WHERE `position_code` = 204;
DELETE FROM `ext_adv_positions` WHERE `position_code` = 205;
--
EOL
);
    }
}
