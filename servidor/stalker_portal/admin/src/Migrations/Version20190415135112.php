<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
class Version20190415135112 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('smac_codes');
        $column = $table->getColumn('end_cleared_date', \Doctrine\DBAL\Types\Type::BIGINT);
        $column->setLength(20)->setNotnull(false)->setDefault(-1);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('smac_codes');
        $column = $table->getColumn('end_cleared_date', \Doctrine\DBAL\Types\Type::BIGINT);
        $column->setLength(20)->setNotnull(true)->setDefault(-1);
    }
    public function getDescription()
    {
        return 'Add nullable field for expire_date';
    }
}
