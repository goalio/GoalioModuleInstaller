<?php
namespace GoalioInstaller\ModuleManager\Feature;

interface InstallerCommandProviderInterface
{
    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getInstallerElementConfig();
}
