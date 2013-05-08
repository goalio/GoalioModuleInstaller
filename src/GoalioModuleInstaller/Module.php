<?php
namespace GoalioModuleInstaller;

use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\ModuleManager;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface {

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__      => __DIR__,
                ),
            ),
        );
    }

    public function init(ModuleManager $moduleManager) {
        $serviceManager  = $moduleManager->getEvent()->getParam('ServiceManager');
        $serviceListener = $serviceManager->get('ServiceListener');
        $serviceListener->addServiceManager(
            'GoalioModuleInstaller\CommandManager',
            'installer_commands',
            'GoalioInstaller\ModuleManager\Feature\InstallerCommandProviderInterface',
            'getInstallerCommandConfig'
        );
    }

    public function getConfig() {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'GoalioModuleInstaller\CommandManager' => 'GoalioModuleInstaller\Service\CommandManagerFactory',
            ),
        );
    }

    public function getControllerConfig() {
        return array(
            'factories' => array(
                'GoalioModuleInstaller\Controller\Installer' => function($sl) {
                    $serviceLocator = $sl->getServiceLocator();
                    $config    = $serviceLocator->get('Config');

                    $controller = new Controller\InstallerController();

                    $commands  = (isset($config['goalio_module_installer']['commands'])) ? $config['goalio_module_installer']['commands'] : array();
                    $controller->setCommands($commands);

                    $params  = (isset($config['goalio_module_installer']['params'])) ? $config['goalio_module_installer']['params'] : array();
                    $controller->setParams($params);

                    $moduleManager = $serviceLocator->get('ModuleManager');
                    $controller->setModuleManager($moduleManager);

                    $commandManager = $serviceLocator->get('GoalioModuleInstaller\CommandManager');
                    $controller->setCommandManager($commandManager);

                    return $controller;
                }
            )
        );
    }

    public function getConsoleBanner(AdapterInterface $console) {
        return 'GoalioModuleInstaller 0.0.1';
    }

    public function getConsoleUsage(AdapterInterface $console){
        return array(
            'installer <command> [<module>] [--silent|-s]' => 'Execute a command, optionally only on a specific module. The silent option confirms all optional commands with "yes"',
            'installer (--help|-?)'                        => 'Show available commands',
        );
    }

}

