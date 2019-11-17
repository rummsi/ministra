<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720061 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `filters` (`title`,       `description`,                            `method`,                `type`,       `values_set`,             `default`)
       VALUES         ('Media type', 'Users by type of mediacontent  watching', 'getUsersByPlayingType', 'VALUES_SET', 'getUsersPlayingTypeSet', '0');
UPDATE `filters` SET `title` = 'Streaming server' WHERE `method` = 'getUsersByUsingStreamServer';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DELETE FROM `filters` WHERE `method` = 'getUsersByPlayingType';
--
EOL
);
    }
}
