<?php
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(
    require(dirname(__FILE__) . '/config.php'), array(

        'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
        'name'=>'Migrate Data Tool: Allow migrate data from Magento community 1.x to Magento 2.x',

        // preloading 'log' component
        'preload'=>array('log'),

        'defaultController'=>'migrate',

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
                        //'levels'=>'error, warning, info',
                        'levels'=>'error',
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