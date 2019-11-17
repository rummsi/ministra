<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720173 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('clock_format')) {
            $this->addSql(<<<EOL
--
ALTER TABLE users ADD COLUMN clock_format ENUM('12h', '24h') NULL DEFAULT NULL;
EOL
);
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE users DROP COLUMN clock_format;
--
EOL
);
    }
}
