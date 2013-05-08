<?php
namespace GoalioModuleInstaller\Controller;

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

        $commandManager = $this->getCommandManager();

        $commands = $this->getCommands();

        // Restrict commands only to module namespace
        if($moduleParam !== null) {
            $commands = (isset($commands[$moduleParam])) ? $commands[$moduleParam] : array();
        }

        //$params = $this->gatherValuesForParams();

        foreach($commands as $moduleCommands) {
            if(isset($moduleCommands[$commandParam])) {
                foreach($moduleCommands[$commandParam] as $commandOptions) {
                    $command = $commandManager->get($commandOptions['type'], $commandOptions['options']);
                    $command->execute($params);
                }
            }
        }
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

        $routeParams  = Json::decode($this->params('params'));

        $paramsConfig = $this->getParams();

        // Restrict commands only to module namespace
        if($moduleParam !== null) {
            $paramsConfig = (isset($paramsConfig[$moduleParam])) ? $paramsConfig[$moduleParam] : array();
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
}