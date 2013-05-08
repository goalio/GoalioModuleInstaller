<?php
namespace GoalioModuleInstaller\Command;

use Zend\ServiceManager\AbstractPluginManager;

class CommandManager extends AbstractPluginManager {

    protected $invokables = array(
        'closure'    => 'GoalioModuleInstaller\Command\Closure',
        'controller' => 'GoalioModuleInstaller\Command\Controller',
        'service'    => 'GoalioModuleInstaller\Command\Service',
    );

    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof CommandInterface) {
            throw new Exception\InvalidElementException(sprintf(
                'Command of type %s is invalid; must implement GoalioModuleInstaller\Command\CommandInterface',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin))
            ));
        }
    }
}