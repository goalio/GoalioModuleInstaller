<?php
namespace GoalioModuleInstaller\Controller;

use Zend\Console\Prompt\Confirm;
use Zend\Console\Prompt\Line;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class InstallerController extends AbstractActionController {

    protected $commands;
    protected $params;

    /**
     * @var \Zend\ModuleManager\ModuleManager
     */
    protected $moduleManager;

    /**
     * @var \GoalioModuleInstaller\Command\CommandManager
     */
    protected $commandManager;

    public function setCommands($commands) {
        $this->commands = $commands;

        return $this;
    }

    public function getCommands() {
        return $this->commands;
    }

    public function setParams($params) {
        $this->params = $params;

        return $this;
    }

    public function getParams() {
        return $this->params;
    }

    public function setModuleManager($moduleManager) {
        $this->moduleManager = $moduleManager;

        return $this;
    }

    public function getModuleManager() {
        return $this->moduleManager;
    }

    public function setCommandManager($commandManager) {
        $this->commandManager = $commandManager;

        return $this;
    }

    public function getCommandManager() {
        return $this->commandManager;
    }

    public function commandAction() {

        $commandParam = $this->params('command');
        $moduleParam  = $this->params('module');

        $silent = $this->params('silent') || $this->params('s');

        $commandManager = $this->getCommandManager();

        $commands = $this->getCommands();

        // Restrict commands only to module namespace
        if($moduleParam !== null) {
            $commands = (isset($commands[$moduleParam])) ? array($commands[$moduleParam]) : array();
        }

        $console = $this->getServiceLocator()->get('console');
        $console->writeLine('--=-- Checking Parameters --=--');

        $params = $this->gatherValuesForParams();

        $console->writeLine();
        $console->writeLine('--=-- Executing Commands --=--');

        $count = 0;

        foreach($commands as $moduleName => $moduleCommands) {
            if(isset($moduleCommands[$commandParam])) {
                foreach($moduleCommands[$commandParam] as $commandName => $commandOptions) {

                    $optional = (isset($commandOptions['optional'])) ? $commandOptions['optional'] : false;
                    $description = (isset($commandOptions['description'])) ? $commandOptions['description'] : $commandName;

                    // Either silent (auto confirm), not optional, or optional and confirmed
                    if($silent || !$optional || ($optional && Confirm::prompt($moduleName . ' - ' . $description . ' [y/n]: ', 'y', 'n'))) {
                        if(!$optional) {
                            $console->writeLine($moduleName . ' - ' . $description . ' [non-optional]');
                        }
                        $command = $commandManager->get($commandOptions['type'], $commandOptions['options']);
                        $command->execute($params);
                        $count++;
                    }
                }
            }
        }

        $console->writeLine();
        return sprintf("Executed %s command(s)", $count);
    }


    public function enableModuleAction() {
        // add Module to application.config
    }

    public function disableModuleAction() {
        //remove module frpm application config
    }

    protected function gatherValuesForParams() {

        $commandParam = $this->params('command');
        $moduleParam  = $this->params('module');

        $routeParams  = $this->decodeParams($this->params('params'));

        $paramsConfig = $this->getParams();

        // Restrict commands only to module namespace
        if($moduleParam !== null) {
            $paramsConfig = (isset($paramsConfig[$moduleParam])) ? array($paramsConfig[$moduleParam]) : array();
        }

        $params = array();

        foreach($paramsConfig as $moduleParams) {
            if(isset($moduleParams[$commandParam])) {
                $params = array_merge($params, $moduleParams[$commandParam]);
            }
        }

        $values = array();

        foreach($params as $param => $description) {
            $values[$param] = $this->getValueForParam($param, $description, $routeParams);
        }

        return $values;
    }

    protected function getValueForParam($name, $description, $routeParams) {

        if(isset($routeParams[$name])) {
            return $routeParams[$name];
        }

        $prompt     = sprintf("%s: ", $description);

        $value = Line::prompt(
            $prompt
        );

        return $value;
    }

    protected function decodeParams($params) {
        $params = str_replace("'", '"', $params);
        return Json::decode($params, Json::TYPE_ARRAY);
    }
}