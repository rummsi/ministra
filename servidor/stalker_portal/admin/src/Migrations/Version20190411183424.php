<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
class Version20190411183424 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('smac_codes');
        $table->addColumn('end_cleared_date', \Doctrine\DBAL\Types\Type::BIGINT)->setLength(20)->setDefault(-1);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('smac_codes');
        $table->dropColumn('end_cleared_date');
    }
}
