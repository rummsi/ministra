<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190409113241 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $values = ['controller_name' => ':controller_name', 'action_name' => ':action_name', 'is_ajax' => ':is_ajax', 'description' => ':description'];
        $this->connection->createQueryBuilder()->insert('adm_grp_action_access')->values($values)->setParameter('controller_name', 'license-keys')->setParameter('action_name', 'clear-license-keys')->setParameter('is_ajax', 1)->setParameter('description', 'Clear license keys')->execute();
        $this->connection->createQueryBuilder()->insert('adm_grp_action_access')->values($values)->setParameter('controller_name', 'license-keys')->setParameter('action_name', 'check-license-key')->setParameter('is_ajax', 1)->setParameter('description', 'Check license key status')->execute();
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $query = $this->connection->createQueryBuilder()->delete('adm_grp_action_access')->where('controller_name = :controller_name')->andWhere($this->connection->createQueryBuilder()->expr()->orX($this->connection->createQueryBuilder()->expr()->eq('action_name', ':action_name_clear'), $this->connection->createQueryBuilder()->expr()->eq('action_name', ':action_name_clear_check')))->setParameter('controller_name', 'license-keys')->setParameter('action_name_clear', 'clear-license-keys')->setParameter('action_name_clear_check', 'check-license-key');
        $query->execute();
    }
    public function getDescription()
    {
        return 'Add routes for clear license keys metadata';
    }
}
