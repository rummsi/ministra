<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190904165014 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $values = ['controller_name' => ':controller_name', 'action_name' => ':action_name', 'is_ajax' => ':is_ajax', 'description' => ':description'];
        $this->connection->createQueryBuilder()->insert('adm_grp_action_access')->values($values)->setParameters(['controller_name' => 'statistics', 'action_name' => 'stat-users-devices', 'is_ajax' => 0, 'description' => 'Display statistics by user\'s devices'])->execute();
        $this->connection->createQueryBuilder()->insert('adm_grp_action_access')->values($values)->setParameters(['controller_name' => 'statistics', 'action_name' => 'stat-users-devices-json', 'is_ajax' => 1, 'description' => 'Display statistics by user\'s devices by page + filters'])->execute();
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->connection->createQueryBuilder()->delete('adm_grp_action_access')->where('action_name = :action_name')->orWhere('action_name = :action_name_2')->andWhere('controller_name = :controller_name')->setParameters(['controller_name' => 'statistics', 'action_name' => 'stat-users-devices', 'action_name_2' => 'stat-users-devices-json'])->execute();
    }
    public function getDescription()
    {
        return 'Add actions';
    }
}
