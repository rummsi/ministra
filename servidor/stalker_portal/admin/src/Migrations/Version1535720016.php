<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720016 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE `cat_genre` SET title='foreign cartoons' WHERE title='foreign' and category_alias='animation';
UPDATE `cat_genre` SET title='our cartoons' WHERE title='ours' and category_alias='animation';
UPDATE `cat_genre` SET title='cartoon series' WHERE title='series' and category_alias='animation';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
