<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720147 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
        (`controller_name`,         `action_name`,    `is_ajax`, `description`                  )
VALUES  ('tv-channels',    'change-channel-genre',            1, 'Change genre for one or grope tv-channels'),
        ('tv-channels',    'change-channel-language',         1, 'Change language for one or grope tv-channels');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
