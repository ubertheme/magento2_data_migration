<?php
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(
    require(dirname(__FILE__) . '/config.php'), array(

        'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
        'name'=>'Migrate Data Tool: Allow migrate data from Magento 1.x to Magento 2.x',

        // preloading 'log' component
        'preload'=>array('log'),

        'defaultController'=>'migrate',

        // modules
        'modules' => array(
            // customize automatic code generation
            'gii' => array(
                'class' => 'system.gii.GiiModule',
                'password' => 'ub@123',
                'ipFilters' => array('10.0.0.170'),
                'newFileMode' => 0644,
                'newDirMode' => 0755,
                'generatorPaths' => array(
                    'application.gii', // a path alias
                ),
            ),
        ),

        // application components
        'components'=>array(
            'user'=>array(
                // enable cookie-based authentication
                'allowAutoLogin'=>true,
            ),

            'errorHandler'=>array(
                // use 'site/error' action to display errors
                'errorAction'=>'migrate/error',
            ),
            'urlManager'=>array(
                'urlFormat'=>'path',
                'rules'=>array(
                    '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                ),
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'CFileLogRoute',
                        'levels'=>'error, warning',
                    ),
                    // uncomment the following to show log messages on web pages
//                    array(
//                        'class'=>'CWebLogRoute',
//                    ),
                ),
            ),
        ),
    )
);