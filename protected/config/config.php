<?php

return array(
    'components'=>array(

        //Database of tool
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=ub_tool',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => 'ub_',
            'class'   => 'CDbConnection'
        ),

        //Database of Magento1
        'mage1' => array(
            'connectionString' => 'mysql:host=localhost;dbname=mage1901',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'class'   => 'CDbConnection'
        ),

        //Database of Magento2 beta
        'mage2' => array(
            'connectionString' => 'mysql:host=localhost;dbname=magento2_74_beta4',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'class'   => 'CDbConnection'
        )
    ),

    // auto loading model and component classes
    'import'=>array(
        'application.components.*',
        'application.models.*',
        'application.models.db.*',
        'application.models.db.mage2.*',

        'application.models.mage2.*',
        'application.models.mage1.*',

        //This can change for your magento1 version
        'application.models.db.mage19x.*',
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=> array(
        // this is displayed in the header section
        'title'=>'Migrate Data Tool for Magento - UberTheme',
        // this is used in error pages
        'adminEmail'=>'quynhvv@joomsolutions.com',
        // the copyright information displayed in the footer section
        'copyrightInfo'=>'Copyright &copy; 2015 by Ubertheme.com',
    )
);
