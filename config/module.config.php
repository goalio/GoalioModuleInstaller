<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'goaliomoduleinstaller\installer' => array(
                    'options' => array(
                        'route'    => 'installer <command> [<module>] [--params=] [--silent|-s]',
                        'defaults' => array(
                            'controller' => 'GoalioModuleInstaller\Controller\Installer',
                            'action' => 'command',
                        ),
                    ),
                ),
            ),
        ),
    ),
);