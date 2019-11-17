<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720074 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
          (`controller_name`, `action_name`,      `is_ajax`, `description`)
VALUES    ('tv-channels',     'itv-reset-claims',         1, 'Reset media-claims');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
