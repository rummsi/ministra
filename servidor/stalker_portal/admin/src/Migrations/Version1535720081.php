<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720081 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `video_series_files` CHANGE COLUMN `quality` `quality` VARCHAR(16) NOT NULL DEFAULT '' ;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `video_series_files` CHANGE COLUMN `quality` `quality` SMALLINT NOT NULL;
--
EOL
);
    }
}
