<?php

namespace GoalioModuleInstaller\Command;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception;

class Closure implements CommandInterface, ServiceLocatorAwareInterface {

    /**
     * @var CommandManager
     */
    protected $serviceLocator;

    protected $method = null;

    public function __construct($options) {

        if(!isset($options['method']) || !is_callable($options['method'])) {
            throw new Exception\InvalidArgumentException('Missing method or method is not callable for Closure command');
        }

        $this->method  = $options['method'];
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }


    public function execute($params) {
        $serviceLocator = $this->getServiceLocator()->getServiceLocator();

        $this->method->__invoke($params, $serviceLocator);
    }

}