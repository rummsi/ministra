<?php

namespace Ministra\Admin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
class AnalyzingRoutesCommand extends \Symfony\Component\Console\Command\Command
{
    use \Ministra\Admin\Command\ContainerTrait;
    protected function configure()
    {
        $this->setName('mtv:routes:check')->addOption('file', 'f', \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Output files for routes list')->setDescription('Generate routes list');
    }
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $file = $input->getOption('file') ?: __DIR__ . '/../../resources/routes.json';
        if (!\is_file($file)) {
            throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException("File {$file} not found");
        }
        $controllers = \json_decode(\file_get_contents($file));
        if (!$controllers) {
            throw new \RuntimeException('Invalid file content');
        }
        $notFind = [];
        foreach ($controllers as $controller => $actions) {
            foreach ($actions as $action) {
                $action = $action === 'index' ? '' : $action;
                if (!$this->findRoute($controller, $action)) {
                    $notFind[] = "{$controller}/{$action}";
                    echo "Not found: {$controller}/{$action}" . PHP_EOL;
                }
            }
        }
        \file_put_contents(__DIR__ . '/../../resources/not_find.json', \json_encode($notFind));
    }
    private function findRoute($controller, $action)
    {
        return (int) $this->getConnection()->createQueryBuilder()->select(['id'])->from('adm_grp_action_access')->where('controller_name = :controller')->andWhere('action_name = :action')->setParameter('controller', $controller)->setParameter('action', $action)->execute()->fetchColumn();
    }
}
