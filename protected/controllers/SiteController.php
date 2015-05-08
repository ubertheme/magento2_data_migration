<?php

class SiteController extends Controller
{
	public $layout = 'main';

    public function actionTest(){}

    private function _dumpData($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
