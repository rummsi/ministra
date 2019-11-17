<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
class Version20190402191636 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('smac_codes');
        $table->addColumn('expire_date', \Doctrine\DBAL\Types\Type::BIGINT)->setLength(20)->setDefault(-1);
        $table->addColumn('count_cleared', \Doctrine\DBAL\Types\Type::INTEGER)->setDefault(-1);
        $table->addColumn('count_clearing', \Doctrine\DBAL\Types\Type::INTEGER)->setDefault(-1);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('smac_codes');
        $table->dropColumn('expire_date');
        $table->dropColumn('count_cleared');
        $table->dropColumn('count_clearing');
    }
}
