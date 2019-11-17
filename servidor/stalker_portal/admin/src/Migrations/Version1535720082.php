<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720082 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `access_tokens` MODIFY `time_delta` VARCHAR(8) NOT NULL DEFAULT '300';
UPDATE `access_tokens` SET time_delta='300';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `access_tokens` MODIFY `time_delta` VARCHAR(128) NOT NULL DEFAULT '' ;
--
EOL
);
    }
}
