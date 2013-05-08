<?php
namespace GoalioModuleInstaller\Service;


use Zend\Mvc\Service\AbstractPluginManagerFactory;

class CommandManagerFactory extends AbstractPluginManagerFactory {

    const PLUGIN_MANAGER_CLASS = 'GoalioModuleInstaller\Command\CommandManager';

}