<?php

namespace Ministra\Admin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class ClearCacheCommand extends \Symfony\Component\Console\Command\Command
{
    use \Ministra\Admin\Command\ContainerTrait;
    protected function configure()
    {
        $this->setName('mtv:cache:clear')->setDescription('Reset all application cache');
    }
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->getContainer()->get('cache')->flush();
    }
}
