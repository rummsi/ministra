<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190422133134 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $query = $this->connection->createQueryBuilder()->update('smac_codes')->set('status', '"' . \SMACCode::STATUS_RESERVED . '"')->where('status = "' . \SMACCode::STATUS_BLOCKED . '"');
        $this->addSql($query->getSQL());
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $query = $this->connection->createQueryBuilder()->update('smac_codes')->set('status', '"' . \SMACCode::STATUS_BLOCKED . '"')->where('status = "' . \SMACCode::STATUS_RESERVED . '"');
        $this->addSql($query->getSQL());
    }
    public function getDescription()
    {
        return 'Change column values for smac_certificates';
    }
}
