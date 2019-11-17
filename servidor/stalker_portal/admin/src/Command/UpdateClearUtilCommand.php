<?php

namespace Ministra\Admin\Command;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\a1d0f8bc3ea86fe7395988b56c201809c;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class UpdateClearUtilCommand extends \Symfony\Component\Console\Command\Command
{
    use \Ministra\Admin\Command\ContainerTrait;
    public function configure()
    {
        $this->setName('mtv:clear-util:update')->setDescription('Update clear util');
    }
    public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        (new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\a1d0f8bc3ea86fe7395988b56c201809c($this->container->get('util.path')))->D5fbd130f6d2916c59b1e7e4619c5a533();
    }
}
