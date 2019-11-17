<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720164 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
update adm_grp_action_access set blocked = 1 where controller_name = 'new-video-club' and action_name in('add-video-ads', 'add-video-moderators');
update adm_grp_action_access set is_ajax = 1, action_access = (view_access or edit_access), view_access = 0, edit_access = 0 where controller_name = 'new-video-club' and action_name in('edit-video-ads', 'edit-video-moderators');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
update adm_grp_action_access set blocked = 0 where controller_name = 'new-video-club' and action_name in('add-video-ads', 'add-video-moderators');
update adm_grp_action_access set is_ajax = 0, view_access = action_access, edit_access = action_access, action_access = 0 where controller_name = 'new-video-club' and action_name in('edit-video-ads', 'edit-video-moderators');
--
EOL
);
    }
}
