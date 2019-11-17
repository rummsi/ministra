<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720115 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('administrators')->hasColumn('opinion_form_flag')) {
            $this->addSql("ALTER TABLE `administrators` ADD COLUMN `opinion_form_flag` ENUM('fill','remind', 'no') DEFAULT NULL;");
        }
        $this->addSql(<<<EOL
--

INSERT INTO `adm_grp_action_access`
        (`controller_name`,             `action_name`,    `is_ajax`,  `description`,                           `hidden`, `only_top_admin`)
VALUES  ('index',                     'opinion-check',            1, 'Checking state of flag of opinion form',        1,               1),
        ('index',                       'opinion-set',            1, 'Setting state of flag of opinion form',         1,               1);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `administrators` DROP COLUMN `opinion_form_flag`;
--
EOL
);
    }
}
