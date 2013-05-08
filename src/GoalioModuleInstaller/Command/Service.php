<?php

namespace GoalioModuleInstaller\Command;

class ServiceCommand implements CommandInterface {

    public function __contruct($options) {

        dump($options);
        stop();

    }

    public function execute() {

    }

}