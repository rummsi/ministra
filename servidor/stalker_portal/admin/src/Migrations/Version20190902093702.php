<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\fcf29683d9d7fe70bf7e8e5d99360976\U7d0431d9c504db487c5109886c97e082;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\X7db24d699d9dc4413e7a61bb94cc44d8;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b4c75f356ba107b83536139965f5fb66d\f634b2f995cc6b1b3311171fb0680721;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
class Version20190902093702 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $style = new \Symfony\Component\Console\Style\SymfonyStyle(new \Symfony\Component\Console\Input\ArrayInput([]), new \Symfony\Component\Console\Output\ConsoleOutput());
        $style->createProgressBar();
        $style->success('Start write user device info to statistics table');
        $count = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\X7db24d699d9dc4413e7a61bb94cc44d8::a0a3921a25e19d949bd4be9d65f0e1e0()->b2752823b7677523753979de3a5daba5(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\fcf29683d9d7fe70bf7e8e5d99360976\U7d0431d9c504db487c5109886c97e082::class)->createQueryBuilder()->select('count(id)')->from('users')->execute()->fetchColumn();
        $style->progressStart($count);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\X7db24d699d9dc4413e7a61bb94cc44d8::a0a3921a25e19d949bd4be9d65f0e1e0()->b2752823b7677523753979de3a5daba5(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b4c75f356ba107b83536139965f5fb66d\f634b2f995cc6b1b3311171fb0680721::class)->run($style);
        $style->success('End write user device info to statistics table');
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if ($schema->hasTable('users_devices_statistic')) {
            $query = $this->connection->getDatabasePlatform()->getTruncateTableSQL('users_devices_statistic');
            $this->addSql($query);
        }
    }
    public function getDescription()
    {
        return 'Migrate data to devices statistics table';
    }
}
