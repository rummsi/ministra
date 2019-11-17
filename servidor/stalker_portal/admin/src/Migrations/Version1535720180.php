<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720180 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE `video` SET `rating_imdb` = `rating_kinopoisk`, `rating_kinopoisk` = '' WHERE NOT(`rating_imdb`) AND `autocomplete_provider` = 'tmdb';
UPDATE `video` SET `rating_count_imdb` = `rating_count_kinopoisk`, `rating_count_kinopoisk` = '' WHERE NOT(`rating_count_imdb`) AND `autocomplete_provider` = 'tmdb';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
