<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720169 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
        (`controller_name`, `action_name`,              `is_ajax`, `description`                  )
VALUES  ('settings',        'themes-edit',                      0, 'Page for editing theme'),
        ('settings',        'upload-theme-img',                 1, 'Uploading theme images'),
        ('settings',        'themes-reset-to-default',          1, 'Resetting launcher themes to default settings');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
