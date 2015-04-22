<?php

class MigrateController extends Controller
{
	public $layout = '2column_left';

    protected function beforeAction($action) {

        //increase the max execution time
        @ini_set('max_execution_time', -1);

        //initial needed session variables
        //needed session variables
        $migrated_data = array(
            'website_ids' => array(),
            'store_group_ids' => array(),
            'store_ids' => array(),
            'category_ids' => array(),
            'product_type_ids' => array(),
            'product_ids' => array(),
            'customer_group_ids' => array(),
            'customer_ids' => array(),
            'sales_object_ids' => array(),
            'sales_order_ids' => array(),
            'sales_quote_ids' => array(),
            'sales_invoice_ids' => array(),
            'sales_shipment_ids' => array(),
            'sales_credit_ids' => array()
        );
        $migratedObj = (object) $migrated_data;
        //update migrated data
        $steps = MigrateSteps::model()->findAll("status = " . MigrateSteps::STATUS_DONE);
        if ($steps){
            foreach ($steps as $step) {
                $migrated_data = json_decode($step->migrated_data);
                if ($migrated_data) {
                    $attributes = get_object_vars($migrated_data);
                    if ($attributes){
                        foreach ($attributes as $attr => $value){
                            $migratedObj->$attr = $value;
                        }
                    }
                }
            }
        }
        //initial session
        $attributes = get_object_vars($migratedObj);
        if ($attributes){
            foreach ($attributes as $attr => $value){
                Yii::app()->session['migrated_'.$attr] = $value;
            }
        }
        //end initial needed session variables

        return parent::beforeAction($action);
    }

    /**
     * This method is invoked right after an action is executed.
     * You may override this method to do some postprocessing for the action.
     * @param CAction $action the action just executed.
     */
    protected function afterAction($action)
    {
        return parent::afterAction($action);
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

	/**
	 * Displays the index page
	 */
	public function actionIndex()
	{
        $nextStep = MigrateSteps::getNextSteps();
		$this->redirect(array($nextStep));
	}

    /**
     * Database settings
     */
    public function actionStep1()
    {
        $step = MigrateSteps::model()->find("sorder = 1");
        if (Yii::app()->request->isPostRequest){

            $step->migrated_data = json_encode($_POST);

            //validate database
            $err_msg = array();
            $validate = @mysql_connect($_POST['mg1_host'], $_POST['mg1_db_user'], $_POST['mg1_db_pass']);
            if (!$validate){
                $err_msg[] = Yii::t('frontend', "Couldn't connected to Magento 1 database.");
            }else{
                if (!mysql_select_db( $_POST['mg1_db_name'], $validate)){
                    $err_msg[] = Yii::t('frontend', "Database Name of Magento 1 was not found in database.");
                    $validate = false;
                }else{
                    //validate magento2
                    mysql_close($validate);
                    $validate = @mysql_connect($_POST['mg2_host'], $_POST['mg2_db_user'], $_POST['mg2_db_pass']);
                    if (!$validate){
                        $err_msg[] = Yii::t('frontend', "Couldn't connected to Magento 2 database.");
                    }else{
                        if (!mysql_select_db( $_POST['mg2_db_name'], $validate)){
                            $err_msg[] = Yii::t('frontend', "Database Name of Magento 2 was not found in database.");
                            $validate = false;
                        } else{
                            mysql_close($validate);
                        }
                    }
                }
            }

            if ($validate){
                //save to config file
                $configTemplate = Yii::app()->basePath .DIRECTORY_SEPARATOR. "config".DIRECTORY_SEPARATOR."config.template";
                $configFilePath = Yii::app()->basePath .DIRECTORY_SEPARATOR. "config".DIRECTORY_SEPARATOR."config.php";
                if (file_exists($configTemplate)){
                    if (file_exists($configFilePath) && is_writable($configFilePath)){
                        $configs = file_get_contents($configTemplate);
                        //replace needed configs
                        $configs = str_replace('{MG1_HOST}', $_POST['mg1_host'], $configs);
                        $configs = str_replace('{MG1_DB_NAME}', $_POST['mg1_db_name'], $configs);
                        $configs = str_replace('{MG1_DB_USER}', $_POST['mg1_db_user'], $configs);
                        $configs = str_replace('{MG1_DB_PASS}', $_POST['mg1_db_pass'], $configs);
                        $configs = str_replace('{MG1_DB_PREFIX}', $_POST['mg1_db_prefix'], $configs);
                        $configs = str_replace('{MG1_VERSION}', $_POST['mg1_version'], $configs);
                        //Mage2
                        $configs = str_replace('{MG2_HOST}', $_POST['mg2_host'], $configs);
                        $configs = str_replace('{MG2_DB_NAME}', $_POST['mg2_db_name'], $configs);
                        $configs = str_replace('{MG2_DB_USER}', $_POST['mg2_db_user'], $configs);
                        $configs = str_replace('{MG2_DB_PASS}', $_POST['mg2_db_pass'], $configs);
                        $configs = str_replace('{MG2_DB_PREFIX}', $_POST['mg2_db_prefix'], $configs);

                        //save
                        if (file_put_contents($configFilePath, $configs)){
                            //save settings to database
                            $step->status = MigrateSteps::STATUS_DONE;
                            if ($step->save()){

                                $this->refresh();

                                //alert message
                                Yii::app()->user->setFlash('success', Yii::t('frontend', "Your settings was saved successfully."));
                            }
                        }
                    }else{
                        Yii::app()->user->setFlash('note', Yii::t('frontend', "The config file was not exists or not ablewrite permission.<br/>Please make writeable for config file and try again."));
                    }
                }else{
                    Yii::app()->user->setFlash('note', Yii::t('frontend', "The config.template file was not exists."));
                }
            }else{
                Yii::app()->user->setFlash('error', implode('</br>', $err_msg));
            }
        }

        $settings = (object)json_decode($step->migrated_data);
        $assign_data = array(
            'step' => $step,
            'settings' => $settings
        );
        $this->render("step{$step->sorder}", $assign_data);
    }

    /**
     * Migrate Websites & Store groups & Store views
     */
    public function actionStep2()
    {
        $step = MigrateSteps::model()->find("sorder = 2");
        $result = MigrateSteps::checkStep($step->sorder);
        if ($result['allowed']){

            //variables to log
            $migrated_website_ids = $migrated_store_group_ids = $migrated_store_ids = array();

            //Get list front-end websites from magento1
            $condition = "code <> 'admin'";
            $websites = Mage1Website::model()->findAll($condition);

            if (Yii::app()->request->isPostRequest){

                //reset this step if has
                $is_reset = Yii::app()->request->getPost('reset');
                if ($is_reset){
                    $dataPath = Yii::app()->basePath .DIRECTORY_SEPARATOR. "data".DIRECTORY_SEPARATOR;
                    $resetSQLFile = $dataPath . "step2_reset.sql";
                    if (file_exists($resetSQLFile)) {
                        $rs = MigrateSteps::executeFile($resetSQLFile);
                        if ($rs){
                            //reset step status
                            $step->status = MigrateSteps::STATUS_NOT_DONE;
                            $step->migrated_data = null;
                            if ($step->update()){
                                $this->refresh();
                            }
                        }
                    }
                }

                //uncheck foreign key
                Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=0")->execute();

                //start migrate process
                $website_ids = Yii::app()->request->getParam('website_ids', array());
                $store_group_ids = Yii::app()->request->getParam('store_group_ids', array());
                $store_ids = Yii::app()->request->getParam('store_ids', array());

                // if has selected websites, store groups, stores
                if (sizeof($website_ids) > 0 AND sizeof($store_group_ids) > 0 AND sizeof($store_ids) > 0){
                    foreach ($websites as $website){
                        if (in_array($website->website_id, $website_ids)){

                            $website2 = Mage2Website::model()->find("code = '{$website->code}'");
                            if (!$website2) { // if not found
                                $website2 = new Mage2Website();
                            }

                            $website2->website_id = $website->website_id;
                            $website2->code = $website->code;
                            $website2->name = $website->name;
                            $website2->sort_order = $website->sort_order;
                            $website2->default_group_id = $website->default_group_id;
                            $website2->is_default = $website->is_default;

                            if ($website2->save()) {
                                //update to log
                                $migrated_website_ids[] = $website->website_id;

                                if ($store_group_ids && isset($store_group_ids[$website->website_id])) {
                                    //Migrate store group of this website
                                    $str_group_ids = implode(',', $store_group_ids[$website->website_id]);
                                    $condition = "website_id = {$website->website_id} AND group_id IN ({$str_group_ids})";
                                    $storeGroups = Mage1StoreGroup::model()->findAll($condition);
                                    if ($storeGroups){
                                        foreach ($storeGroups as $storeGroup){
                                            $condition = "website_id = {$website->website_id} AND group_id = {$storeGroup->group_id}";
                                            $storeGroup2 = Mage2StoreGroup::model()->find($condition);
                                            if (!$storeGroup2) {
                                                $storeGroup2 = new Mage2StoreGroup();
                                            }
                                            $storeGroup2->group_id = $storeGroup->group_id;
                                            $storeGroup2->website_id = $storeGroup->website_id;
                                            $storeGroup2->name = $storeGroup->name;
                                            $storeGroup2->root_category_id = $storeGroup->root_category_id;
                                            $storeGroup2->default_store_id = $storeGroup->default_store_id;

                                            if ($storeGroup2->save()) {
                                                //update to log
                                                $migrated_store_group_ids[] = $storeGroup->group_id;

                                                if ($store_ids && isset($store_ids[$storeGroup->group_id])){
                                                    //Migrate stores of current store group
                                                    $str_store_ids = implode(',', $store_ids[$storeGroup->group_id]);
                                                    $condition = "website_id = {$website->website_id} AND store_id IN ({$str_store_ids})";
                                                    $stores = Mage1Store::model()->findAll($condition);
                                                    if ($stores){
                                                        foreach ($stores as $store){
                                                            $condition = "code = '{$store->code}'";
                                                            $store2 = Mage2Store::model()->find($condition);
                                                            if (!$store2){
                                                                $store2 = new Mage2Store();
                                                            }
                                                            $store2->code = $store->code;
                                                            $store2->website_id = $store->website_id;
                                                            $store2->group_id = $store->group_id;
                                                            $store2->name = $store->name;
                                                            $store2->sort_order = $store->sort_order;
                                                            $store2->is_active = $store->is_active;
                                                            if ($store2->save()){
                                                                //update to log
                                                                $migrated_store_ids[] = $store->store_id;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //Update step status
                    if ($migrated_website_ids || $migrated_store_group_ids || $migrated_store_ids){
                        $step->status = MigrateSteps::STATUS_DONE;

                        //add admin website id
                        array_push($migrated_website_ids, '0');
                        //add store group admin
                        array_push($migrated_store_group_ids, '0');
                        //add admin store id
                        array_push($migrated_store_ids, '0');

                        $migrated_data = array(
                            'website_ids' => $migrated_website_ids,
                            'store_group_ids' => $migrated_store_group_ids,
                            'store_ids' => $migrated_store_ids
                        );
                        $step->migrated_data = json_encode($migrated_data);

                        if ($step->update()) {
                            //Update session
                            Yii::app()->session['migrated_website_ids'] = $migrated_website_ids;
                            Yii::app()->session['migrated_store_group_ids'] = $migrated_store_group_ids;
                            Yii::app()->session['migrated_store_ids'] = $migrated_store_ids;

                            //check foreign key
                            Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=1")->execute();

                            //alert message
                            $message = "Migrated successfully. Total Website: %s1, Total Store Groups: %s2, Total Store Views: %s3";
                            $message = Yii::t('frontend', $message, array('%s1'=> (sizeof($migrated_website_ids)-1), '%s2'=> (sizeof($migrated_store_group_ids)-1), '%s3' => (sizeof($migrated_store_ids)-1) ));
                            Yii::app()->user->setFlash('success', $message);
                        }
                    }
                }else{
                    Yii::app()->user->setFlash('note', Yii::t('frontend', 'You have to select at least one website, one store group, one store to migrate.'));
                }
            }

            $assign_data = array(
                'step' => $step,
                'websites' => $websites,
            );
            $this->render("step{$step->sorder}", $assign_data);
        }else{
            Yii::app()->user->setFlash('note', Yii::t('frontend', "The first you need to finish the %s.", array("%s" => ucfirst($result['back_step']))));
            $this->redirect(array($result['back_step']));
        }
    }

    /**
     * Migrate Attributes
     */
    public function actionStep3()
    {
        $step = MigrateSteps::model()->find("sorder = 3");
        $result = MigrateSteps::checkStep($step->sorder);
        if ($result['allowed']){

            //get migrated data of step1 from session
            $migrated_store_ids = isset(Yii::app()->session['migrated_store_ids']) ? Yii::app()->session['migrated_store_ids'] : array();

            $total_attribute_set = $total_attribute_group = $total_attribute = $total_entity_attribute = 0;
            $migrated_attribute_set_ids = $migrated_attribute_group_ids = $migrated_attribute_ids = array();

            //get product entity type id
            $product_entity_type_id = MigrateSteps::getMage1EntityTypeId(MigrateSteps::PRODUCT_TYPE_CODE);

            //get all product attribute sets in magento1
            $attribute_sets = Mage1AttributeSet::model()->findAll("entity_type_id = {$product_entity_type_id}");

            //get all product attributes
            //$condition = "entity_type_id = {$product_entity_type_id} AND is_user_defined = 1";
            $attributes = Mage1Attribute::model()->findAll("entity_type_id = {$product_entity_type_id}");

            if (Yii::app()->request->isPostRequest){

                $is_reset = Yii::app()->request->getPost('reset');
                if ($is_reset){
                    $dataPath = Yii::app()->basePath .DIRECTORY_SEPARATOR. "data".DIRECTORY_SEPARATOR;
                    $resetSQLFile = $dataPath . "step3_reset.sql";
                    if (file_exists($resetSQLFile)) {
                        $rs = MigrateSteps::executeFile($resetSQLFile);
                        if ($rs){
                            //reset step status
                            $step->status = MigrateSteps::STATUS_NOT_DONE;
                            $step->migrated_data = null;
                            if ($step->update()){
                                $this->refresh();
                            }
                        }
                    }
                }

                //uncheck foreign key
                Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=0")->execute();

                //migrate attribute sets
                if ($attribute_sets){
                    foreach ($attribute_sets as $attribute_set) {
                        $condition = "attribute_set_id = {$attribute_set->attribute_set_id}";
                        $attribute_set2 = Mage2AttributeSet::model()->find($condition);
                        if (!$attribute_set2){
                            $attribute_set2 = new Mage2AttributeSet();
                            $attribute_set2->attribute_set_id = $attribute_set->attribute_set_id;
                            $attribute_set2->entity_type_id = $attribute_set->entity_type_id;
                            $attribute_set2->attribute_set_name = $attribute_set->attribute_set_name;
                            $attribute_set2->sort_order = $attribute_set->sort_order;
                        }

                        if ($attribute_set2->save()){
                            $total_attribute_set++;
                            $migrated_attribute_set_ids[] = $attribute_set->attribute_set_id;
                        }

                        //get all attribute groups of current attribute set
                        $condition = "attribute_set_id = {$attribute_set->attribute_set_id}";
                        $attribute_groups = Mage1AttributeGroup::model()->findAll($condition);
                        if ($attribute_groups) {
                            foreach ($attribute_groups as $attribute_group) {
                                //$condition = "attribute_group_id = {$attribute_group->attribute_group_id} AND attribute_set_id = {$attribute_group->attribute_set_id}";
                                $condition = "attribute_group_id = {$attribute_group->attribute_group_id}";
                                $attribute_group2 = Mage2AttributeGroup::model()->find($condition);
                                if (!$attribute_group2) {
                                    $attribute_group2 = new Mage2AttributeGroup();
                                    $attribute_group2->attribute_group_id = $attribute_group->attribute_group_id;
                                    $attribute_group2->attribute_set_id = $attribute_group->attribute_set_id;
                                    $attribute_group2->attribute_group_name = $attribute_group->attribute_group_name;
                                    $attribute_group2->sort_order = $attribute_group->sort_order;
                                    $attribute_group2->default_id = $attribute_group->default_id;
                                    //NOTE: this values is new added in Magento2, we will update after migrated in back-end of Magento2
                                    $attribute_group2->attribute_group_code = null;
                                    $attribute_group2->tab_group_code = null;
                                }
                                if ($attribute_group2->save()) {
                                    $total_attribute_group++;
                                    $migrated_attribute_group_ids[] = $attribute_group->attribute_group_id;
                                }
                            }
                        }
                    }
                }

                //migrate product attributes
                if ($attributes){
                    foreach ($attributes as $attribute){

                        //msrp_enabled was changed to msrp in magento2
                        if ($attribute->attribute_code == 'msrp_enabled')
                            $attribute_code2 = 'msrp';
                        else
                            $attribute_code2 = $attribute->attribute_code;

                        $condition = "entity_type_id = {$product_entity_type_id} AND attribute_code = '{$attribute_code2}'";
                        $attribute2 = Mage2Attribute::model()->find($condition);
                        if (!$attribute2){
                            $attribute2 = new Mage2Attribute();
                            $attribute2->entity_type_id = $attribute->entity_type_id;
                            $attribute2->attribute_code = $attribute->attribute_code;
                            $attribute2->attribute_model = $attribute->attribute_model;
                            $attribute2->backend_model = null;
                            $attribute2->backend_type = $attribute->backend_type;
                            $attribute2->backend_table = $attribute->backend_table;
                            // note: this was changed in magento2, we don't migrate this field
                            //$attribute2->frontend_model = $attribute->frontend_model;
                            $attribute2->frontend_model = null;
                            $attribute2->frontend_input = $attribute->frontend_input;
                            $attribute2->frontend_label = $attribute->frontend_label;
                            $attribute2->frontend_class = $attribute->frontend_class;
                            $attribute2->source_model = null;
                            $attribute2->is_required = $attribute->is_required;
                            $attribute2->is_user_defined = $attribute->is_user_defined;
                            $attribute2->default_value = $attribute->default_value;
                            $attribute2->is_unique = $attribute->is_unique;
                            $attribute2->note = $attribute->note;
                        }

                        //save or update data of a attribute
                        if ($attribute2->save()){
                            //update total
                            $total_attribute++;
                            $migrated_attribute_ids[] = $attribute->attribute_id;

                            if ($migrated_store_ids) {
                                //eav_attribute_label
                                $condition = "attribute_id = {$attribute->attribute_id}";
                                $str_store_ids = implode(',', $migrated_store_ids);
                                $condition .= " AND store_id IN ({$str_store_ids})";
                                $attribute_labels = Mage1AttributeLabel::model()->findAll($condition);
                                if ($attribute_labels) {
                                    foreach ($attribute_labels as $attribute_label){
                                        $mage2StoreId = MigrateSteps::getMage2StoreId($attribute_label->store_id);
                                        $condition = "attribute_id = {$attribute2->attribute_id} AND store_id = {$mage2StoreId} AND value = '{$attribute_label->value}'";
                                        $attribute_label2 = Mage2AttributeLabel::model()->find($condition);
                                        if (!$attribute_label2) {
                                            $attribute_label2 = new Mage2AttributeLabel();
                                            $attribute_label2->attribute_label_id = $attribute_label->attribute_label_id;
                                            $attribute_label2->attribute_id = $attribute2->attribute_id;
                                            $attribute_label2->store_id = MigrateSteps::getMage2StoreId($attribute_label->store_id);
                                            $attribute_label2->value = $attribute_label->value;
                                        }
                                        //save or update
                                        $attribute_label2->save();
                                    }
                                }
                            }

                            //eav_attribute_option
                            $condition = "attribute_id = {$attribute->attribute_id}";
                            $attribute_options = Mage1AttributeOption::model()->findAll($condition);
                            if ($attribute_options){
                                foreach ($attribute_options as $attribute_option){
                                    $condition = "attribute_id = {$attribute2->attribute_id} AND option_id = {$attribute_option->option_id}";
                                    $attribute_option2 = Mage2AttributeOption::model()->find($condition);
                                    if (!$attribute_option2) {
                                        $attribute_option2 = new Mage2AttributeOption();
                                        $attribute_option2->option_id = $attribute_option->option_id;
                                        $attribute_option2->attribute_id = $attribute2->attribute_id;
                                        $attribute_option2->sort_order = $attribute_option->sort_order;
                                    }
                                    //save or update
                                    if ($attribute_option2->save()){
                                        //eav_attribute_option_value,
                                        if ($migrated_store_ids) {
                                            //get all option values of current option in Magento1
                                            $condition = "option_id = {$attribute_option->option_id}";
                                            $str_store_ids = implode(',', $migrated_store_ids);
                                            $condition .= " AND store_id IN ({$str_store_ids})";
                                            $option_values = Mage1AttributeOptionValue::model()->findAll($condition);
                                            if ($option_values){
                                                foreach ($option_values as $option_value){
                                                    $condition = "value_id = {$option_value->value_id}";
                                                    $option_value2 = Mage2AttributeOptionValue::model()->find($condition);
                                                    if (!$option_value2) {
                                                        $option_value2 = new Mage2AttributeOptionValue();
                                                        $option_value2->value_id = $option_value->value_id;
                                                        $option_value2->option_id = $option_value->option_id;
                                                        $option_value2->store_id = MigrateSteps::getMage2StoreId($option_value->store_id);
                                                        $option_value2->value = $option_value->value;
                                                    }
                                                    //update or save
                                                    $option_value2->save();
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            //catalog_eav_attribute
                            $catalog_eav_attribute = Mage1CatalogEavAttribute::model()->find("attribute_id = {$attribute->attribute_id}");
                            if ($catalog_eav_attribute) {
                                $catalog_eav_attribute2 = Mage2CatalogEavAttribute::model()->find("attribute_id = {$attribute2->attribute_id}");
                                if (!$catalog_eav_attribute2){
                                    $catalog_eav_attribute2 = new Mage2CatalogEavAttribute();
                                    $catalog_eav_attribute2->attribute_id = $attribute2->attribute_id;
                                    $catalog_eav_attribute2->frontend_input_renderer = $catalog_eav_attribute->frontend_input_renderer;
                                    $catalog_eav_attribute2->is_global = $catalog_eav_attribute->is_global;
                                    $catalog_eav_attribute2->is_visible = $catalog_eav_attribute->is_visible;
                                    $catalog_eav_attribute2->is_searchable = $catalog_eav_attribute->is_searchable;
                                    $catalog_eav_attribute2->is_filterable = $catalog_eav_attribute->is_filterable;
                                    $catalog_eav_attribute2->is_comparable = $catalog_eav_attribute->is_comparable;
                                    $catalog_eav_attribute2->is_visible_on_front = $catalog_eav_attribute->is_visible_on_front;
                                    $catalog_eav_attribute2->is_html_allowed_on_front = $catalog_eav_attribute->is_html_allowed_on_front;
                                    $catalog_eav_attribute2->is_used_for_price_rules = $catalog_eav_attribute->is_used_for_price_rules;
                                    $catalog_eav_attribute2->is_filterable_in_search = $catalog_eav_attribute->is_filterable_in_search;
                                    $catalog_eav_attribute2->used_in_product_listing = $catalog_eav_attribute->used_in_product_listing;
                                    $catalog_eav_attribute2->used_for_sort_by = $catalog_eav_attribute->used_for_sort_by;
                                    $catalog_eav_attribute2->apply_to = $catalog_eav_attribute->apply_to;
                                    $catalog_eav_attribute2->is_visible_in_advanced_search = $catalog_eav_attribute->is_filterable_in_search;
                                    $catalog_eav_attribute2->position = $catalog_eav_attribute->position;
                                    $catalog_eav_attribute2->is_wysiwyg_enabled = $catalog_eav_attribute->is_wysiwyg_enabled;
                                    $catalog_eav_attribute2->is_used_for_promo_rules = $catalog_eav_attribute->is_used_for_promo_rules;
                                    $catalog_eav_attribute2->is_required_in_admin_store = 0;

                                    // note: This for Magento1 version < 1.9.1.0
                                    if (isset($catalog_eav_attribute->search_weight)){
                                        $catalog_eav_attribute2->search_weight = $catalog_eav_attribute->search_weight;
                                    }

                                    // note: this attribute removed from Magento2 0.42.0 beta11
                                    //$catalog_eav_attribute2->is_configurable = $catalog_eav_attribute->is_configurable;
                                    $catalog_eav_attribute2->save();
                                }
                            }
                        }
                    }//end foreach attributes
                }

                //eav_entity_attribute
                //we only migrate related with products
                if ($migrated_attribute_set_ids && $migrated_attribute_group_ids && $migrated_attribute_ids) {
                    //make condition
                    $str_migrated_attribute_ids = implode(',', $migrated_attribute_ids);
                    $str_migrated_attribute_set_ids = implode(',', $migrated_attribute_set_ids);
                    $str_migrated_attribute_group_ids = implode(',', $migrated_attribute_group_ids);
                    $condition = "entity_type_id = {$product_entity_type_id} AND attribute_id IN ($str_migrated_attribute_ids)";
                    $condition .= " AND attribute_set_id IN ({$str_migrated_attribute_set_ids})";
                    $condition .= " AND attribute_group_id IN ({$str_migrated_attribute_group_ids})";
                    $entity_attributes = Mage1EntityAttribute::model()->findAll($condition);
                    if ($entity_attributes){
                        foreach ($entity_attributes as $entity_attribute){
                            $attributeId2 = MigrateSteps::getMage2AttributeId($entity_attribute->attribute_id, '4');
                            $condition = "attribute_id = {$attributeId2} AND entity_type_id = 4";
                            $condition .= " AND attribute_set_id = {$entity_attribute->attribute_set_id}";
                            //$condition .= " AND attribute_group_id = {$entity_attribute->attribute_group_id}";
                            $entity_attribute2 = Mage2EntityAttribute::model()->find($condition);
                            if (!$entity_attribute2) {
                                $entity_attribute2 = new Mage2EntityAttribute();
                                $entity_attribute2->entity_type_id = $entity_attribute->entity_type_id;
                                $entity_attribute2->attribute_set_id = $entity_attribute->attribute_set_id;
                                $entity_attribute2->attribute_group_id = $entity_attribute->attribute_group_id;
                                $entity_attribute2->attribute_id = $attributeId2;
                                $entity_attribute2->sort_order = $entity_attribute->sort_order;
                            }
                            //save or update
                            if ($entity_attribute2->save()){
                                $total_entity_attribute++;
                            }
                        }
                    }
                }

                //Update step status
                if ($total_attribute_set && $total_attribute_group && $total_attribute){
                    $step->status = MigrateSteps::STATUS_DONE;
                    if ($step->update()) {

                        //check foreign key
                        Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=1")->execute();

                        $message = "Migrated successfully. Total Attribute Sets: %s1, Total Attribute Groups: %s2, Total Attributes: %s3";
                        $message = Yii::t('frontend', $message, array('%s1'=> $total_attribute_set, '%s2'=> $total_attribute_group, '%s3' => $total_attribute));
                        Yii::app()->user->setFlash('success', $message);
                    }
                }
            }//end post request

            $assign_data = array(
                'step' => $step,
                'attribute_sets' => $attribute_sets,
                'attributes' => $attributes
            );
            $this->render("step{$step->sorder}", $assign_data);
        }else{
            Yii::app()->user->setFlash('note', Yii::t('frontend', "The first you need to finish the %s.", array("%s" => ucfirst($result['back_step']))));
            $this->redirect(array($result['back_step']));
        }
    }

    /**
     * Migrate Categories
     */
    public function actionStep4()
    {
        $step = MigrateSteps::model()->find("sorder = 4");
        $result = MigrateSteps::checkStep($step->sorder);
        if ($result['allowed']){

            //variables to log
            $migrated_category_ids = array();

            //get migrated store_ids from session
            $migrated_store_ids = isset(Yii::app()->session['migrated_store_ids']) ? Yii::app()->session['migrated_store_ids'] : array();

            //get all categories from magento1 with level > 0
            $categories = Mage1CatalogCategoryEntity::model()->findAll("level > 0");

            if (Yii::app()->request->isPostRequest){

                //reset database of this step if has
                $is_reset = Yii::app()->request->getPost('reset');
                if ($is_reset){
                    $dataPath = Yii::app()->basePath .DIRECTORY_SEPARATOR. "data".DIRECTORY_SEPARATOR;
                    $resetSQLFile = $dataPath . "step4_reset.sql";
                    if (file_exists($resetSQLFile)) {
                        $rs = MigrateSteps::executeFile($resetSQLFile);
                        if ($rs){
                            //delete url related data in url_rewrite table and catalog_url_rewrite_product_category table
                            Mage2UrlRewrite::model()->deleteAll("entity_type = 'category'");

                            //reset step status
                            $step->status = MigrateSteps::STATUS_NOT_DONE;
                            $step->migrated_data = null;
                            if ($step->update()){
                                $this->refresh();
                            }
                        }
                    }
                }

                //uncheck foreign key
                Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=0")->execute();

                //get all categories from magento1
                $categories = Mage1CatalogCategoryEntity::model()->findAll();

                /*
                 * Get black list attribute ids
                 * We do not migrate bellow attributes
                */
                $entity_type_id = MigrateSteps::getMage1EntityTypeId(MigrateSteps::CATEGORY_TYPE_CODE);
                $checkList = array(
                    MigrateSteps::getMage1AttributeId('display_mode', $entity_type_id) => 'PRODUCTS',
                    MigrateSteps::getMage1AttributeId('landing_page', $entity_type_id) => '',
                    MigrateSteps::getMage1AttributeId('custom_design', $entity_type_id) => '',
                    MigrateSteps::getMage1AttributeId('custom_design_from', $entity_type_id) => null,
                    MigrateSteps::getMage1AttributeId('custom_design_to', $entity_type_id) => null,
                    MigrateSteps::getMage1AttributeId('page_layout', $entity_type_id) => '',
                    MigrateSteps::getMage1AttributeId('custom_layout_update', $entity_type_id) => '',
                    MigrateSteps::getMage1AttributeId('custom_apply_to_products', $entity_type_id) => 1,
                    MigrateSteps::getMage1AttributeId('custom_use_parent_settings', $entity_type_id) => 1,
                );
                $keyCheckList = array_keys($checkList);

                //handle selected category ids
                $category_ids = Yii::app()->request->getPost('category_ids', array());

                //catalog_category_entity
                // if has selected category to migrate
                if ($category_ids){
                    if ($categories){
                        foreach ($categories as $category){
                            if (in_array($category->entity_id, $category_ids)){
                                $condition = "entity_id = {$category->entity_id}";
                                $category2 = Mage2CatalogCategoryEntity::model()->find($condition);
                                if (!$category2){
                                    $category2 = new Mage2CatalogCategoryEntity();
                                    $category2->entity_id = $category->entity_id;
                                    $category2->attribute_set_id = $category->attribute_set_id;
                                    $category2->parent_id = $category->parent_id;
                                    $category2->created_at = $category->created_at;
                                    $category2->updated_at = $category->updated_at;
                                }
                                $category2->path = $category->path;
                                $category2->position = $category->position;
                                $category2->level = $category->level;
                                $category2->children_count = $category->children_count;

                                //update or save
                                if ($category2->save()){
                                    //update to log
                                    $migrated_category_ids[] = $category->entity_id;

                                    //catalog_category_entity_datetime
                                    $condition = "entity_id = {$category->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogCategoryEntityDatetime::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //note: we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 3);
                                            if ($attribute_id2){
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogCategoryEntityDatetime::model()->find($condition);
                                                if (!$model2){
                                                    $model2 = new Mage2CatalogCategoryEntityDatetime();
                                                    $model2->attribute_id = $attribute_id2;
                                                    $model2->store_id = $store_id2;
                                                    $model2->entity_id = $model->entity_id;
                                                    //note: we need check and fixed for some attributes
                                                    if (in_array($model->attribute_id, $keyCheckList)){
                                                        $model2->value = $checkList[$model->attribute_id];
                                                    } else {
                                                        $model2->value = $model->value;
                                                    }
                                                    $model2->save();
                                                }
                                            }
                                        }
                                    }

                                    //catalog_category_entity_decimal
                                    $condition = "entity_id = {$category->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogCategoryEntityDecimal::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 3);
                                            if ($attribute_id2){
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogCategoryEntityDecimal::model()->find($condition);
                                                if (!$model2){
                                                    $model2 = new Mage2CatalogCategoryEntityDecimal();
                                                    $model2->attribute_id = $attribute_id2;
                                                    $model2->store_id = $store_id2;
                                                    $model2->entity_id = $model->entity_id;
                                                    //we need check and fixed for some attributes
                                                    if (in_array($model->attribute_id, $keyCheckList)){
                                                        $model2->value = $checkList[$model->attribute_id];
                                                    } else {
                                                        $model2->value = $model->value;
                                                    }
                                                    $model2->save();
                                                }
                                            }
                                        }
                                    }

                                    //catalog_category_entity_int
                                    $condition = "entity_id = {$category->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogCategoryEntityInt::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 3);
                                            if ($attribute_id2){
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogCategoryEntityInt::model()->find($condition);
                                                if (!$model2){
                                                    $model2 = new Mage2CatalogCategoryEntityInt();
                                                    $model2->attribute_id = $attribute_id2;
                                                    $model2->store_id = $store_id2;
                                                    $model2->entity_id = $model->entity_id;
                                                    //note: we need check and fixed for some attributes
                                                    if (in_array($model->attribute_id, $keyCheckList)){
                                                        $model2->value = $checkList[$model->attribute_id];
                                                    } else {
                                                        $model2->value = $model->value;
                                                    }
                                                    $model2->save();
                                                }
                                            }
                                        }
                                    }

                                    //catalog_category_entity_text
                                    $condition = "entity_id = {$category->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogCategoryEntityText::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //note: we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 3);
                                            if ($attribute_id2){
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogCategoryEntityText::model()->find($condition);
                                                if (!$model2){
                                                    $model2 = new Mage2CatalogCategoryEntityText();
                                                    $model2->attribute_id = $attribute_id2;
                                                    $model2->store_id = $store_id2;
                                                    $model2->entity_id = $model->entity_id;
                                                    //we need check and fixed for some attributes
                                                    if (in_array($model->attribute_id, $keyCheckList)){
                                                        $model2->value = $checkList[$model->attribute_id];
                                                    } else {
                                                        $model2->value = $model->value;
                                                    }
                                                    $model2->save();
                                                }
                                            }
                                        }
                                    }

                                    //catalog_category_entity_varchar
                                    $condition = "entity_id = {$category->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogCategoryEntityVarchar::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //note: we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 3);
                                            if ($attribute_id2){
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogCategoryEntityVarchar::model()->find($condition);
                                                if (!$model2){
                                                    $model2 = new Mage2CatalogCategoryEntityVarchar();
                                                    $model2->attribute_id = $attribute_id2;
                                                    $model2->store_id = $store_id2;
                                                    $model2->entity_id = $model->entity_id;
                                                    //we need check and fixed for some attributes
                                                    if (in_array($model->attribute_id, $keyCheckList)){
                                                        $model2->value = $checkList[$model->attribute_id];
                                                    } else {
                                                        $model2->value = $model->value;
                                                    }
                                                    $model2->save();
                                                }
                                            }
                                        }
                                    }

                                    //url_rewrite for category
                                    $condition = "category_id = {$category->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $urls = Mage1UrlRewrite::model()->findAll($condition);
                                    if ($urls){
                                        foreach ($urls as $url){
                                            $store_id2 = MigrateSteps::getMage2StoreId($url->store_id);
                                            $condition = "store_id = {$store_id2} AND entity_id = {$url->category_id} AND entity_type = 'category'";
                                            $url2 = Mage2UrlRewrite::model()->find($condition);
                                            if (!$url2) {
                                                $url2 = new Mage2UrlRewrite();
                                                $url2->url_rewrite_id = $url->url_rewrite_id;
                                                $url2->entity_type = 'category';
                                                $url2->entity_id = $url->category_id;
                                                $url2->request_path = $url->request_path;
                                                $url2->target_path = $url->target_path;
                                                $url2->redirect_type = 0;
                                                $url2->store_id = $store_id2;
                                                $url2->description = $url->description;
                                                $url2->is_autogenerated = $url->is_system;
                                                $url2->metadata = null;
                                                $url2->save();
                                            }
                                        }
                                    }
                                }// end save a category
                            }
                        }
                    }
                }else{
                    Yii::app()->user->setFlash('note', Yii::t('frontend', 'You have to select at least one Category to migrate.'));
                }

                //Update step status
                if ($migrated_category_ids){
                    $step->status = MigrateSteps::STATUS_DONE;
                    $step->migrated_data = json_encode(array(
                        'category_ids' => $migrated_category_ids
                    ));
                    if ($step->update()) {
                        //update session
                        Yii::app()->session['migrated_category_ids'] = $migrated_category_ids;

                        //check foreign key
                        Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=1")->execute();

                        $message = "Migrated successfully. Total Categories migrated: %s1";
                        $message = Yii::t('frontend', $message, array('%s1'=> sizeof($migrated_category_ids)));
                        Yii::app()->user->setFlash('success', $message);
                    }
                }
            }//end post request

            $assign_data = array(
                'step' => $step,
                'categories' => $categories
            );
            $this->render("step{$step->sorder}", $assign_data);
        }else{
            Yii::app()->user->setFlash('note', Yii::t('frontend', "The first you need to finish the %s.", array("%s" => ucfirst($result['back_step']))));
            $this->redirect(array($result['back_step']));
        }
    }

    /**
     * Migrate Products
     */
    public function actionStep5()
    {
        $step = MigrateSteps::model()->find("sorder = 5");
        $result = MigrateSteps::checkStep($step->sorder);
        if ($result['allowed']){
            //get migrated website ids from session if has
            $migrated_website_ids = isset(Yii::app()->session['migrated_website_ids']) ? Yii::app()->session['migrated_website_ids'] : array();

            //get migrated store_ids from session
            $migrated_store_ids = isset(Yii::app()->session['migrated_store_ids']) ? Yii::app()->session['migrated_store_ids'] : array();

            //get migrated category ids
            $migrated_category_ids = isset(Yii::app()->session['migrated_category_ids']) ? Yii::app()->session['migrated_category_ids'] : array();

            //product types
            $product_type_ids = array('simple', 'configurable', 'grouped', 'virtual', 'bundle', 'downloadable');

            //variables to log
            $migrated_product_type_ids = array();
            $migrated_product_ids = array();

            if (Yii::app()->request->isPostRequest){

                //reset database of this step if has
                $is_reset = Yii::app()->request->getPost('reset');
                if ($is_reset){
                    $dataPath = Yii::app()->basePath .DIRECTORY_SEPARATOR. "data".DIRECTORY_SEPARATOR;
                    $resetSQLFile = $dataPath . "step5_reset.sql";
                    if (file_exists($resetSQLFile)) {
                        $rs = MigrateSteps::executeFile($resetSQLFile);
                        if ($rs){
                            //delete url related data in url_rewrite table and catalog_url_rewrite_product_category table
                            Mage2UrlRewrite::model()->deleteAll("entity_type = 'product'");

                            //reset step status
                            $step->status = MigrateSteps::STATUS_NOT_DONE;
                            $step->migrated_data = null;
                            if ($step->update()){
                                $this->refresh();
                            }
                        }
                    }
                }

                //uncheck foreign key
                Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=0")->execute();

                /*
                 * Get black list attribute ids
                 * We do not migrate bellow attributes
                */
                $entity_type_id = MigrateSteps::getMage1EntityTypeId(MigrateSteps::PRODUCT_TYPE_CODE);
                $checkList = array(
                    MigrateSteps::getMage1AttributeId('custom_design', $entity_type_id) => '',
                    MigrateSteps::getMage1AttributeId('custom_design_from', $entity_type_id) => null,
                    MigrateSteps::getMage1AttributeId('custom_design_to', $entity_type_id) => null,
                    MigrateSteps::getMage1AttributeId('page_layout', $entity_type_id) => '',
                    MigrateSteps::getMage1AttributeId('custom_layout_update', $entity_type_id) => null,
                );
                $keyCheckList = array_keys($checkList);

                $selected_product_types = Yii::app()->request->getPost('product_type_ids', array());
                if ($selected_product_types){
                    foreach ($selected_product_types as $type_id){
                        // get products by type_id
                        //catalog_product_entity
                        $products = Mage1CatalogProductEntity::model()->findAll("type_id = '{$type_id}'");
                        if ($products){
                            foreach ($products as $product){

                                $product2 = new Mage2CatalogProductEntity();
                                foreach ($product2->attributes as $key => $value){
                                    if (isset($product->$key)){
                                        $product2->$key = $product->$key;
                                    }
                                }

                                //save or update
                                if ($product2->save()){
                                    //update to log
                                    $migrated_product_ids[] = $product->entity_id;

                                    //catalog_product_entity_int
                                    $condition = "entity_id = {$product->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }

                                    $models = Mage1CatalogProductEntityInt::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){ // if exists
                                                $model2 = new Mage2CatalogProductEntityInt();
                                                $model2->attribute_id = $attribute_id2;
                                                $model2->store_id = $store_id2;
                                                $model2->entity_id = $model->entity_id;
                                                //we need check and fixed for some attributes
                                                if (in_array($model->attribute_id, $keyCheckList)){
                                                    $model2->value = $checkList[$model->attribute_id];
                                                } else {
                                                    $model2->value = $model->value;
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }

                                    //catalog_product_entity_text
                                    $condition = "entity_id = {$product->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogProductEntityText::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //note: we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $model2 = new Mage2CatalogProductEntityText();
                                                $model2->attribute_id = $attribute_id2;
                                                $model2->store_id = $store_id2;
                                                $model2->entity_id = $model->entity_id;
                                                //we need check and fixed for some attributes
                                                if (in_array($model->attribute_id, $keyCheckList)){
                                                    $model2->value = $checkList[$model->attribute_id];
                                                } else {
                                                    $model2->value = $model->value;
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }

                                    //catalog_product_entity_varchar
                                    $condition = "entity_id = {$product->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogProductEntityVarchar::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //note: we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $model2 = new Mage2CatalogProductEntityVarchar();
                                                $model2->attribute_id = $attribute_id2;
                                                $model2->store_id = $store_id2;
                                                $model2->entity_id = $model->entity_id;
                                                //we need check and fixed for some attributes
                                                if (in_array($model->attribute_id, $keyCheckList)){
                                                    $model2->value = $checkList[$model->attribute_id];
                                                } else {
                                                    $model2->value = $model->value;
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }

                                    //catalog_product_entity_datetime
                                    $condition = "entity_id = {$product->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogProductEntityDatetime::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $model2 = new Mage2CatalogProductEntityDatetime();
                                                $model2->attribute_id = $attribute_id2;
                                                $model2->store_id = $store_id2;
                                                $model2->entity_id = $model->entity_id;
                                                //we need check and fixed for some attributes
                                                if (in_array($model->attribute_id, $keyCheckList)){
                                                    $model2->value = $checkList[$model->attribute_id];
                                                } else {
                                                    $model2->value = $model->value;
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }

                                    //catalog_product_entity_decimal
                                    $condition = "entity_id = {$product->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogProductEntityDecimal::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $model2 = new Mage2CatalogProductEntityDecimal();
                                                $model2->attribute_id = $attribute_id2;
                                                $model2->store_id = $store_id2;
                                                $model2->entity_id = $model->entity_id;
                                                //we need check and fixed for some attributes
                                                if (in_array($model->attribute_id, $keyCheckList)){
                                                    $model2->value = $checkList[$model->attribute_id];
                                                } else {
                                                    $model2->value = $model->value;
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }

                                    //catalog_product_entity_gallery
                                    $condition = "entity_id = {$product->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogProductEntityGallery::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $model2 = new Mage2CatalogProductEntityGallery();
                                                $model2->attribute_id = $attribute_id2;
                                                $model2->store_id = $store_id2;
                                                $model2->entity_id = $model->entity_id;
                                                $model2->position = $model->position;
                                                $model2->value = $model->value;
                                                $model2->save();
                                            }
                                        }
                                    }

                                    //catalog_product_entity_media_gallery
                                    $condition = "entity_id = {$product->entity_id}";
                                    $models = Mage1CatalogProductEntityMediaGallery::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id migrated
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $model2 = new Mage2CatalogProductEntityMediaGallery();
                                                $model2->value_id = $model->value_id;
                                                $model2->attribute_id = $attribute_id2;
                                                $model2->entity_id = $model->entity_id;
                                                $model2->value = $model->value;
                                                if ($model2->save()){
                                                    //catalog_product_entity_media_gallery_value
                                                    //we have migrate by migrated stores
                                                    if ($migrated_store_ids){
                                                        foreach ($migrated_store_ids as $store_id){
                                                            $store_id2 = MigrateSteps::getMage2StoreId($store_id);
                                                            $gallery_value = Mage1CatalogProductEntityMediaGalleryValue::model()->find("value_id = {$model->value_id} AND store_id = {$store_id}");
                                                            if ($gallery_value){
                                                                $gallery_value2 = new Mage2CatalogProductEntityMediaGalleryValue();
                                                                $gallery_value2->value_id = $gallery_value->value_id;
                                                                $gallery_value2->store_id = $store_id2;
                                                                $gallery_value2->entity_id = $model->entity_id;
                                                                $gallery_value2->label = $gallery_value->label;
                                                                $gallery_value2->position = $gallery_value->position;
                                                                $gallery_value2->disabled = $gallery_value->disabled;
                                                                $gallery_value2->save();
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //catalog_product_option
                                    $condition = "product_id = {$product->entity_id}";
                                    $product_options = Mage1CatalogProductOption::model()->findAll($condition);
                                    if ($product_options){
                                        foreach ($product_options as $product_option){
                                            $product_option2 = new Mage2CatalogProductOption();
                                            foreach ($product_option2->attributes as $key => $value){
                                                if (isset($product_option->$key)){
                                                    $product_option2->$key = $product_option->$key;
                                                }
                                            }
                                            if ($product_option2->save()){

                                                //catalog_product_option_type_value
                                                $condition = "option_id = {$product_option->option_id}";
                                                $option_type_values = Mage1CatalogProductOptionTypeValue::model()->findAll($condition);
                                                if ($option_type_values){
                                                    foreach ($option_type_values as $option_type_value){
                                                        $option_type_value2 = new Mage2CatalogProductOptionTypeValue();
                                                        foreach ($option_type_value2->attributes as $key => $value){
                                                            if (isset($option_type_value->$key)){
                                                                $option_type_value2->$key = $option_type_value->$key;
                                                            }
                                                        }

                                                        if ($option_type_value2->save()){
                                                            //catalog_product_option_type_price & catalog_product_option_type_title
                                                            if ($migrated_store_ids){
                                                                foreach ($migrated_store_ids as $store_id){
                                                                    $store_id2 = MigrateSteps::getMage2StoreId($store_id);

                                                                    //catalog_product_option_type_price
                                                                    $condition = "option_type_id = {$option_type_value->option_type_id} AND store_id = {$store_id}";
                                                                    $option_type_price = Mage1CatalogProductOptionTypePrice::model()->find($condition);
                                                                    if ($option_type_price){
                                                                        $option_type_price2 = new Mage2CatalogProductOptionTypePrice();
                                                                        foreach ($option_type_price2->attributes as $key => $value){
                                                                            if (isset($option_type_price->$key)){
                                                                                $option_type_price2->$key = $option_type_price->$key;
                                                                            }
                                                                        }
                                                                        $option_type_price2->store_id = $store_id2;
                                                                        $option_type_price2->save();
                                                                    }

                                                                    //catalog_product_option_type_title
                                                                    $condition = "option_type_id = {$option_type_value->option_type_id} AND store_id = {$store_id}";
                                                                    $option_type_title = Mage1CatalogProductOptionTypeTitle::model()->find($condition);
                                                                    if ($option_type_title){
                                                                        $option_type_title2 = new Mage2CatalogProductOptionTypeTitle();
                                                                        foreach ($option_type_title2->attributes as $key => $value){
                                                                            if (isset($option_type_title->$key)){
                                                                                $option_type_title2->$key = $option_type_title->$key;
                                                                            }
                                                                        }
                                                                        $option_type_title2->store_id = $store_id2;
                                                                        $option_type_title2->save();
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                                //we have to migrate by migrated stores
                                                if ($migrated_store_ids){
                                                    foreach ($migrated_store_ids as $store_id){
                                                        $store_id2 = MigrateSteps::getMage2StoreId($store_id);

                                                        //catalog_product_option_price
                                                        $option_price = Mage1CatalogProductOptionPrice::model()->find("option_id = {$product_option->option_id} AND store_id = {$store_id}");
                                                        if ($option_price){
                                                            $option_price2 = new Mage2CatalogProductOptionPrice();
                                                            foreach ($option_price2->attributes as $key => $value){
                                                                if (isset($option_price->$key)){
                                                                    $option_price2->$key = $option_price->$key;
                                                                }
                                                            }
                                                            $option_price2->store_id = $store_id2;
                                                            $option_price2->save();
                                                        }

                                                        //catalog_product_option_title
                                                        $option_title = Mage1CatalogProductOptionTitle::model()->find("option_id = {$product_option->option_id} AND store_id = {$store_id}");
                                                        if ($option_title){
                                                            $option_title2 = new Mage2CatalogProductOptionTitle();
                                                            foreach ($option_title2->attributes as $key => $value){
                                                                if (isset($option_title->$key)){
                                                                    $option_title2->$key = $option_title->$key;
                                                                }
                                                            }
                                                            $option_title2->store_id = $store_id2;
                                                            $option_title2->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //cataloginventory_stock_status
                                    if ($migrated_website_ids){
                                        foreach ($migrated_website_ids as $website_id){
                                            $models = Mage1StockStatus::model()->findAll("website_id = {$website_id} AND product_id = {$product->entity_id}");
                                            if ($models){
                                                foreach ($models as $model){
                                                    $model2 = new Mage2StockStatus();
                                                    foreach ($model2->attributes as $key => $value){
                                                        if (isset($model->$key)){
                                                            $model2->$key = $model->$key;
                                                        }
                                                    }
                                                    if ($model2->save()){
                                                        //cataloginventory_stock_item
                                                        $stock_item = Mage1StockItem::model()->find("product_id = {$model->product_id} AND stock_id = {$model->stock_id}");
                                                        if ($stock_item){
                                                            $stock_item2 = new Mage2StockItem();
                                                            foreach ($stock_item2->attributes as $key => $value){
                                                                if (isset($stock_item->$key)){
                                                                    $stock_item2->$key = $stock_item->$key;
                                                                }
                                                            }
                                                            $stock_item2->website_id = $website_id;
                                                            $stock_item2->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //url_rewrite
                                    $condition = "product_id = {$product->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $urls = Mage1UrlRewrite::model()->findAll($condition);
                                    if ($urls){
                                        foreach ($urls as $url){
                                            $store_id2 = MigrateSteps::getMage2StoreId($url->store_id);
                                            $url2 = new Mage2UrlRewrite();
                                            $url2->url_rewrite_id = $url->url_rewrite_id;
                                            $url2->entity_type = 'product';
                                            $url2->entity_id = $url->product_id;
                                            $url2->request_path = $url->request_path;
                                            $url2->target_path = $url->target_path;
                                            $url2->redirect_type = 0;
                                            $url2->store_id = $store_id2;
                                            $url2->description = $url->description;
                                            $url2->is_autogenerated = $url->is_system;
                                            if ($url->category_id)
                                                $url2->metadata = serialize(array('category_id'=>$url->category_id));
                                            else
                                                $url2->metadata = null;
                                            if ($url2->save()) {
                                                //catalog_url_rewrite_product_category
                                                $catalog_url2 = new Mage2CatalogUrlRewriteProductCategory();
                                                $catalog_url2->url_rewrite_id = $url->url_rewrite_id;
                                                $catalog_url2->category_id = $url->category_id;
                                                $catalog_url2->product_id = $url->product_id;
                                                $catalog_url2->save();
                                            }
                                        }
                                    }
                                }//end save a product
                            }// endforeach products

                        }// endif has products

                        //update to log
                        $migrated_product_type_ids[] = $type_id;
                    }//end foreach product types

                    //Start migrate related data with a product
                    if ($migrated_product_ids){

                        //make string product ids
                        $str_product_ids = implode(',', $migrated_product_ids);

                        //catalog_product_website
                        if ($migrated_website_ids){
                            $str_website_ids = implode(',', $migrated_website_ids);
                            $condition = "product_id IN ({$str_product_ids}) AND website_id IN ({$str_website_ids})";
                            $models = Mage1CatalogProductWebsite::model()->findAll($condition);
                            if ($models){
                                foreach ($models as $model){
                                    $model2 = new Mage2CatalogProductWebsite();
                                    $model2->product_id = $model->product_id;
                                    $model2->website_id = $model->website_id;
                                    $model2->save();
                                }
                            }
                        }

                        //catalog_category_product
                        if ($migrated_category_ids){
                            foreach ($migrated_category_ids as $category_id){
                                $condition = "product_id IN ({$str_product_ids}) AND category_id = {$category_id}";
                                $models = Mage1CatalogCategoryProduct::model()->findAll($condition);
                                if ($models){
                                    foreach ($models as $model){
                                        $model2 = new Mage2CatalogCategoryProduct();
                                        $model2->category_id = $model->category_id;
                                        $model2->product_id = $model->product_id;
                                        $model2->position = $model->position;
                                        $model2->save();
                                    }
                                }
                            }
                        }

                        //Cross sell, Up sell, Related & Grouped Products
                        /** catalog_product_link_type:
                         * 1 - relation - Related Products
                         * 3 - super - Grouped Products
                         * 4 - up_sell - Up Sell Products
                         * 5 - cross_sell - Cross Sell Products
                         *
                         * Note: Tables: catalog_product_link_type & catalog_product_link_attribute was not changed.
                         * So, we don't migrate these tables. But careful with id was changed in catalog_product_link_attribute
                         */
                        //link type ids to migration

                        //catalog_product_link
                        $link_type_ids = array(1, 4, 5);
                        if (in_array('grouped', $migrated_product_type_ids)){
                            $link_type_ids[] = 3;
                        }
                        $str_link_type_ids = implode(',', $link_type_ids);
                        $condition = "product_id IN ({$str_product_ids}) AND linked_product_id IN ({$str_product_ids}) AND link_type_id IN ({$str_link_type_ids})";
                        $models = Mage1CatalogProductLink::model()->findAll($condition);
                        if ($models){
                            foreach ($models as $model){
                                $model2 = new Mage2CatalogProductLink();
                                $model2->link_id = $model->link_id;
                                $model2->product_id = $model->product_id;
                                $model2->linked_product_id = $model->linked_product_id;
                                $model2->link_type_id = $model->link_type_id;
                                if ($model2->save()){
                                    //catalog_product_link_attribute_decimal
                                    $condition = "link_id = {$model2->link_id}";
                                    $items = Mage1CatalogProductLinkAttributeDecimal::model()->findAll($condition);
                                    if ($items){
                                        foreach ($items as $item){
                                            $item2 = new Mage2CatalogProductLinkAttributeDecimal();
                                            $item2->value_id = $item->value_id;
                                            $item2->product_link_attribute_id = MigrateSteps::getMage2ProductLinkAttrId($item->product_link_attribute_id);
                                            $item2->link_id = $item->link_id;
                                            $item2->value = $item->value;
                                            $item2->save();
                                        }
                                    }
                                    //catalog_product_link_attribute_int
                                    $condition = "link_id = {$model2->link_id}";
                                    $items = Mage1CatalogProductLinkAttributeInt::model()->findAll($condition);
                                    if ($items){
                                        foreach ($items as $item){
                                            $item2 = new Mage2CatalogProductLinkAttributeInt();
                                            $item2->value_id = $item->value_id;
                                            $item2->product_link_attribute_id = MigrateSteps::getMage2ProductLinkAttrId($item->product_link_attribute_id);
                                            $item2->link_id = $item->link_id;
                                            $item2->value = $item->value;
                                            $item2->save();
                                        }
                                    }
                                    //catalog_product_link_attribute_varchar
                                    $condition = "link_id = {$model2->link_id}";
                                    $items = Mage1CatalogProductLinkAttributeVarchar::model()->findAll($condition);
                                    if ($items){
                                        foreach ($items as $item){
                                            $item2 = new Mage2CatalogProductLinkAttributeVarchar();
                                            $item2->value_id = $item->value_id;
                                            $item2->product_link_attribute_id = MigrateSteps::getMage2ProductLinkAttrId($item->product_link_attribute_id);
                                            $item2->link_id = $item->link_id;
                                            $item2->value = $item->value;
                                            $item2->save();
                                        }
                                    }
                                }
                            }
                        } //End Cross sell, Up sell, Related & Grouped Products

                        //Configurable products
                        if (in_array('configurable', $migrated_product_type_ids)){
                            //catalog_product_super_link
                            $condition = "product_id IN ({$str_product_ids}) AND parent_id IN ({$str_product_ids})";
                            $models = Mage1CatalogProductSuperLink::model()->findAll($condition);
                            if ($models) {
                                foreach ($models as $model) {
                                    $model2 = new Mage2CatalogProductSuperLink();
                                    $model2->link_id = $model->link_id;
                                    $model2->product_id = $model->product_id;
                                    $model2->parent_id = $model->parent_id;
                                    $model2->save();
                                }
                            }

                            //catalog_product_relation
                            $condition = "parent_id IN ({$str_product_ids}) AND child_id IN ({$str_product_ids})";
                            $models = Mage1CatalogProductRelation::model()->findAll($condition);
                            if ($models){
                                foreach ($models as $model){
                                    $model2 = new Mage2CatalogProductRelation();
                                    $model2->parent_id = $model->parent_id;
                                    $model2->child_id = $model->child_id;
                                    $model2->save();
                                }
                            }

                            //catalog_product_super_attribute
                            $condition = "product_id IN ({$str_product_ids})";
                            $models = Mage1CatalogProductSuperAttribute::model()->findAll($condition);
                            if ($models) {
                                foreach ($models as $model){
                                    $model2 = new Mage2CatalogProductSuperAttribute();
                                    $model2->product_super_attribute_id = $model->product_super_attribute_id;
                                    $model2->product_id = $model->product_id;
                                    $model2->attribute_id = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                    $model2->position = $model->position;
                                    if ($model2->save()) {
                                        //catalog_product_super_attribute_label
                                        $condition = "product_super_attribute_id = {$model2->product_super_attribute_id}";
                                        if ($migrated_store_ids) {
                                            $str_store_ids = implode(',', $migrated_store_ids);
                                            $condition .= " AND store_id IN ({$str_store_ids})";
                                        }
                                        $super_attribute_labels = Mage1CatalogProductSuperAttributeLabel::model()->findAll($condition);
                                        if ($super_attribute_labels) {
                                            foreach ($super_attribute_labels as $super_attribute_label) {
                                                $store_id2 = MigrateSteps::getMage2StoreId($super_attribute_label->store_id);
                                                $super_attribute_label2 = new Mage2CatalogProductSuperAttributeLabel();
                                                $super_attribute_label2->value_id = $super_attribute_label->value_id;
                                                $super_attribute_label2->product_super_attribute_id = $super_attribute_label->product_super_attribute_id;
                                                $super_attribute_label2->store_id = $store_id2;
                                                $super_attribute_label2->use_default = $super_attribute_label->use_default;
                                                $super_attribute_label2->value = $super_attribute_label->value;
                                                $super_attribute_label2->save();
                                            }
                                        }

                                        //catalog_product_super_attribute_pricing
                                        $condition = "product_super_attribute_id = {$model2->product_super_attribute_id}";
                                        if ($migrated_website_ids) {
                                            $str_website_ids = implode(',', $migrated_website_ids);
                                            $condition .= " AND website_id IN ({$str_website_ids})";
                                        }
                                        $super_attribute_pricing_models = Mage1CatalogProductSuperAttributePricing::model()->findAll($condition);
                                        if ($super_attribute_pricing_models) {
                                            foreach ($super_attribute_pricing_models as $super_attribute_pricing) {
                                                $super_attribute_pricing2 = new Mage2CatalogProductSuperAttributePricing();
                                                $super_attribute_pricing2->value_id = $super_attribute_pricing->value_id;
                                                $super_attribute_pricing2->product_super_attribute_id = $super_attribute_pricing->product_super_attribute_id;
                                                $super_attribute_pricing2->value_index = $super_attribute_pricing->value_index;
                                                $super_attribute_pricing2->is_percent = $super_attribute_pricing->is_percent;
                                                $super_attribute_pricing2->pricing_value = $super_attribute_pricing->pricing_value;
                                                $super_attribute_pricing2->website_id = $super_attribute_pricing->website_id;
                                                $super_attribute_pricing2->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }//End Configurable products

                        //Bundle products
                        if (in_array('bundle', $migrated_product_type_ids)){
                            //catalog_product_bundle_option
                            $condition = "parent_id IN ({$str_product_ids})";
                            $models = Mage1CatalogProductBundleOption::model()->findAll($condition);
                            if ($models){
                                foreach ($models as $model){
                                    $model2 = new Mage2CatalogProductBundleOption();
                                    $model2->option_id = $model->option_id;
                                    $model2->parent_id = $model->parent_id;
                                    $model2->required = $model->required;
                                    $model2->position = $model->position;
                                    $model2->type = $model->type;
                                    if ($model2->save()) {
                                        //catalog_product_bundle_option_value
                                        $condition = "option_id = {$model2->option_id}";
                                        if ($migrated_store_ids) {
                                            $str_store_ids = implode(',', $migrated_store_ids);
                                            $condition .= " AND store_id IN ({$str_store_ids})";
                                        }
                                        $bundle_option_values = Mage1CatalogProductBundleOptionValue::model()->findAll($condition);
                                        if ($bundle_option_values){
                                            foreach ($bundle_option_values as $bundle_option_value) {
                                                $bundle_option_value2 = new Mage2CatalogProductBundleOptionValue();
                                                $bundle_option_value2->value_id = $bundle_option_value->value_id;
                                                $bundle_option_value2->option_id = $bundle_option_value->option_id;
                                                $bundle_option_value2->store_id = MigrateSteps::getMage2StoreId($bundle_option_value->store_id);
                                                $bundle_option_value2->title = $bundle_option_value->title;
                                                $bundle_option_value2->save();
                                            }
                                        }
                                        //catalog_product_bundle_selection
                                        $condition = "option_id = {$model2->option_id} AND product_id IN ({$str_product_ids})";
                                        $bundle_selections = Mage1CatalogProductBundleSelection::model()->findAll($condition);
                                        if ($bundle_selections){
                                            foreach ($bundle_selections as $bundle_selection){
                                                $bundle_selection2 = new Mage2CatalogProductBundleSelection();
                                                $bundle_selection2->selection_id = $bundle_selection->selection_id;
                                                $bundle_selection2->option_id = $bundle_selection->option_id;
                                                $bundle_selection2->parent_product_id = $bundle_selection->parent_product_id;
                                                $bundle_selection2->product_id = $bundle_selection->product_id;
                                                $bundle_selection2->position = $bundle_selection->position;
                                                $bundle_selection2->is_default = $bundle_selection->is_default;
                                                $bundle_selection2->selection_price_type = $bundle_selection->selection_price_type;
                                                $bundle_selection2->selection_price_value = $bundle_selection->selection_price_value;
                                                $bundle_selection2->selection_qty = $bundle_selection->selection_qty;
                                                $bundle_selection2->selection_can_change_qty = $bundle_selection->selection_can_change_qty;
                                                if ($bundle_selection2->save()) {
                                                    if ($migrated_website_ids){
                                                        $str_website_ids = implode(',', $migrated_website_ids);
                                                        //catalog_product_bundle_selection_price
                                                        $condition = "selection_id = {$bundle_selection2->selection_id} AND website_id IN ({$str_website_ids})";
                                                        $selection_prices = Mage1CatalogProductBundleSelectionPrice::model()->findAll($condition);
                                                        if ($selection_prices) {
                                                            foreach ($selection_prices as $selection_price){
                                                                $selection_price2 = new Mage2CatalogProductBundleSelectionPrice();
                                                                $selection_price2->selection_id = $selection_price->selection_id;
                                                                $selection_price2->website_id = $selection_price->website_id;
                                                                $selection_price2->selection_price_type = $selection_price->selection_price_type;
                                                                $selection_price2->selection_price_value = $selection_price->selection_price_value;
                                                                $selection_price2->save();
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }//End Bundle products

                        //Downloadable products
                        if (in_array('downloadable', $migrated_product_type_ids)){
                            //downloadable_link
                            $condition = "product_id IN ({$str_product_ids})";
                            $models = Mage1DownloadableLink::model()->findAll($condition);
                            if ($models){
                                foreach ($models as $model){
                                    $model2 = new Mage2DownloadableLink();
                                    foreach ($model2->attributes as $key => $value){
                                        if (isset($model->$key)){
                                            $model2->$key = $model->$key;
                                        }
                                    }
                                    if ($model2->save()) {
                                        if ($migrated_website_ids){
                                            //downloadable_link_price
                                            $str_website_ids = implode(',', $migrated_website_ids);
                                            $condition = "link_id = {$model2->link_id} AND website_id IN ({$str_website_ids})";
                                            $link_prices = Mage1DownloadableLinkPrice::model()->findAll($condition);
                                            if ($link_prices){
                                                foreach ($link_prices as $link_price){
                                                    $link_price2 = new Mage2DownloadableLinkPrice();
                                                    $link_price2->price_id = $link_price->price_id;
                                                    $link_price2->link_id = $link_price->link_id;
                                                    $link_price2->website_id = $link_price->website_id;
                                                    $link_price2->price = $link_price->price;
                                                    $link_price2->save();
                                                }
                                            }
                                            //downloadable_link_title
                                            if ($migrated_store_ids) {
                                                $str_store_ids = implode(',', $migrated_store_ids);
                                                $condition = "link_id = {$model2->link_id} AND store_id IN ({$str_store_ids})";
                                                $link_titles = Mage1DownloadableLinkTitle::model()->findAll($condition);
                                                if ($link_titles) {
                                                    foreach ($link_titles as $link_title){
                                                        $link_title2 = new Mage2DownloadableLinkTitle();
                                                        $link_title2->title_id = $link_title->title_id;
                                                        $link_title2->link_id = $link_title->link_id;
                                                        $link_title2->store_id = MigrateSteps::getMage2StoreId($link_title->store_id);
                                                        $link_title2->title = $link_title->title;
                                                        $link_title2->save();
                                                    }
                                                }
                                            }
                                            //downloadable_sample
                                            //downloadable_sample_title
                                        }
                                    }
                                }
                            }
                        } //End Downloadable products

                    }//End migrate related data with a product
                }else{
                    Yii::app()->user->setFlash('note', Yii::t('frontend', 'You have to select at least one Product type to migrate.'));
                }

                //Update step status
                if ($migrated_product_type_ids && $migrated_product_ids){
                    $step->status = MigrateSteps::STATUS_DONE;
                    $step->migrated_data = json_encode(array(
                        'product_type_ids' => $migrated_product_type_ids,
                        'product_ids' => $migrated_product_ids
                    ));
                    if ($step->update()) {
                        //Update session
                        Yii::app()->session['migrated_product_type_ids'] = $migrated_product_type_ids;
                        Yii::app()->session['migrated_product_ids'] = $migrated_product_ids;

                        //check foreign key
                        Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=1")->execute();

                        $message = "Migrated successfully. Total Products migrated: %s1";
                        $message = Yii::t('frontend', $message, array('%s1'=> sizeof($migrated_product_ids)));
                        Yii::app()->user->setFlash('success', $message);
                    }
                }
            }//end post request

            $assign_data = array(
                'step' => $step,
                'product_type_ids' => $product_type_ids
            );
            $this->render("step{$step->sorder}", $assign_data);
        }else{
            Yii::app()->user->setFlash('note', Yii::t('frontend', "The first you need to finish the %s.", array("%s" => ucfirst($result['back_step']))));
            $this->redirect(array($result['back_step']));
        }
    }

    /**
     * Migrate Customers
     */
    public function actionStep6()
    {
        $step = MigrateSteps::model()->find("sorder = 6");
        $result = MigrateSteps::checkStep($step->sorder);
        if ($result['allowed']){
            //get all current customer groups
            $customer_groups = Mage1CustomerGroup::model()->findAll();

            //variables to log
            $migrated_customer_group_ids = array();
            $migrated_customer_ids = array();

            if (Yii::app()->request->isPostRequest){

                //reset database of this step if has
                $is_reset = Yii::app()->request->getPost('reset');
                if ($is_reset){
                    $dataPath = Yii::app()->basePath .DIRECTORY_SEPARATOR. "data".DIRECTORY_SEPARATOR;
                    $resetSQLFile = $dataPath . "step6_reset.sql";
                    if (file_exists($resetSQLFile)) {
                        $rs = MigrateSteps::executeFile($resetSQLFile);
                        if ($rs){
                            //reset step status
                            $step->status = MigrateSteps::STATUS_NOT_DONE;
                            $step->migrated_data = null;
                            if ($step->update()){
                                $this->refresh();
                            }
                        }
                    }
                }

                //uncheck foreign key
                Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=0")->execute();

                $selected_group_ids = Yii::app()->request->getPost('customer_group_ids', array());
                if ($selected_group_ids){
                    foreach ($selected_group_ids as $group_id){
                        //customer_group
                        $customer_group1 = Mage1CustomerGroup::model()->findByPk($group_id);
                        $customer_group2 = Mage2CustomerGroup::model()->find("customer_group_id = {$group_id} AND customer_group_code = '{$customer_group1->customer_group_code}'");
                        if (!$customer_group2){
                            $customer_group2 = new Mage2CustomerGroup();
                            $customer_group2->customer_group_id = $group_id;
                            $customer_group2->customer_group_code = $customer_group1->customer_group_code;
                        }
                        //update tax class_id if have exits
                        $customer_group2->tax_class_id = $customer_group1->tax_class_id;

                        if ($customer_group2->save()){
                            $migrated_customer_group_ids[] = $customer_group2->customer_group_id;

                            //we will migrate related tax_class here
                            $tax_class1 = Mage1TaxClass::model()->findByPk($customer_group2->tax_class_id);
                            if ($tax_class1){
                                $tax_class2 = Mage2TaxClass::model()->findByPk($tax_class1->class_id);
                                if (!$tax_class2){
                                    $tax_class2 = new Mage2TaxClass();
                                    $tax_class2->class_id = $tax_class1->class_id;
                                }
                                $tax_class2->class_type = $tax_class1->class_type;
                                $tax_class2->class_name = $tax_class1->class_name;
                                $tax_class2->save();
                            }

                            //migrate all customers of this customer group
                            //customer_entity
                            $customers = Mage1CustomerEntity::model()->findAll("group_id = {$group_id}");
                            if ($customers){
                                foreach ($customers as $customer){
                                    $customer2 = Mage2CustomerEntity::model()->findByPk($customer->entity_id);
                                    if (!$customer2){
                                        $customer2 = new Mage2CustomerEntity();
                                        foreach ($customer2->attributes as $key => $value){
                                            if (isset($customer->$key)){
                                                $customer2->$key = $customer->$key;
                                            }
                                        }
                                        $customer2->store_id = MigrateSteps::getMage2StoreId($customer->store_id);

                                        if ($customer2->save()){
                                            $migrated_customer_ids[] = $customer2->entity_id;

                                            //customer_entity_datetime
                                            $models = Mage1CustomerEntityDatetime::model()->findAll("entity_id = $customer->entity_id");
                                            if ($models){
                                                foreach ($models as $model){
                                                    $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 1);
                                                    // This because some system customer attribute_code was not using in Magento2
                                                    if ($attribute_id2){
                                                        $model2 = new Mage2CustomerEntityDatetime();
                                                        foreach ($model2->attributes as $key => $value){
                                                            if (isset($model->$key)){
                                                                $model2->$key = $model->$key;
                                                            }
                                                        }
                                                        $model2->attribute_id = $attribute_id2;
                                                        $model2->save();
                                                    }
                                                }
                                            }
                                            //customer_entity_decimal
                                            $models = Mage1CustomerEntityDecimal::model()->findAll("entity_id = $customer->entity_id");
                                            if ($models){
                                                foreach ($models as $model){
                                                    $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 1);
                                                    // This because some system customer attribute_code was not using in Magento2
                                                    if ($attribute_id2){
                                                        $model2 = new Mage2CustomerEntityDecimal();
                                                        foreach ($model2->attributes as $key => $value){
                                                            if (isset($model->$key)){
                                                                $model2->$key = $model->$key;
                                                            }
                                                        }
                                                        $model2->attribute_id = $attribute_id2;
                                                        $model2->save();
                                                    }
                                                }
                                            }
                                            //customer_entity_int
                                            $models = Mage1CustomerEntityInt::model()->findAll("entity_id = $customer->entity_id");
                                            if ($models){
                                                foreach ($models as $model){
                                                    $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 1);
                                                    // This because some system customer attribute_code was not using in Magento2
                                                    //(example: reward_update_notification, reward_warning_notification)
                                                    if ($attribute_id2){
                                                        $model2 = new Mage2CustomerEntityInt();
                                                        foreach ($model2->attributes as $key => $value){
                                                            if (isset($model->$key)){
                                                                $model2->$key = $model->$key;
                                                            }
                                                        }
                                                        $model2->attribute_id = $attribute_id2;
                                                        $model2->save();
                                                    }
                                                }
                                            }
                                            //customer_entity_text
                                            $models = Mage1CustomerEntityText::model()->findAll("entity_id = $customer->entity_id");
                                            if ($models){
                                                foreach ($models as $model){
                                                    $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 1);
                                                    // This because some system customer attribute_code was not using in Magento2
                                                    if ($attribute_id2){
                                                        $model2 = new Mage2CustomerEntityText();
                                                        foreach ($model2->attributes as $key => $value){
                                                            if (isset($model->$key)){
                                                                $model2->$key = $model->$key;
                                                            }
                                                        }
                                                        $model2->attribute_id = $attribute_id2;
                                                        $model2->save();
                                                    }
                                                }
                                            }
                                            //customer_entity_varchar
                                            $models = Mage1CustomerEntityVarchar::model()->findAll("entity_id = $customer->entity_id");
                                            if ($models){
                                                foreach ($models as $model){
                                                    $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 1);
                                                    // This because some system customer attribute_code was not using in Magento2
                                                    if ($attribute_id2){
                                                        $model2 = new Mage2CustomerEntityVarchar();
                                                        foreach ($model2->attributes as $key => $value){
                                                            if (isset($model->$key)){
                                                                $model2->$key = $model->$key;
                                                            }
                                                        }
                                                        $model2->attribute_id = $attribute_id2;
                                                        $model2->save();
                                                    }
                                                }
                                            }

                                            //customer_address_entity
                                            $address_entities = Mage1CustomerAddressEntity::model()->findAll("parent_id = {$customer->entity_id}");
                                            if ($address_entities){
                                                foreach($address_entities as $address_entity){
                                                    $address_entity2 = new Mage2CustomerAddressEntity();
                                                    foreach ($address_entity2->attributes as $key => $value){
                                                        if (isset($address_entity->$key)){
                                                            $address_entity2->$key = $address_entity->$key;
                                                        }
                                                    }
                                                    if ($address_entity2->save()){
                                                        //customer_address_entity_datetime
                                                        $models = Mage1CustomerAddressEntityDatetime::model()->findAll("entity_id = $address_entity2->entity_id");
                                                        if ($models){
                                                            foreach ($models as $model){
                                                                $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 2);
                                                                // This because some system customer attribute_code was not using in Magento2
                                                                if ($attribute_id2){
                                                                    $model2 = new Mage2CustomerAddressEntityDatetime();
                                                                    foreach ($model2->attributes as $key => $value){
                                                                        if (isset($model->$key)){
                                                                            $model2->$key = $model->$key;
                                                                        }
                                                                    }
                                                                    $model2->attribute_id = $attribute_id2;
                                                                    $model2->save();
                                                                }
                                                            }
                                                        }

                                                        //customer_address_entity_decimal
                                                        $models = Mage1CustomerAddressEntityDecimal::model()->findAll("entity_id = $address_entity2->entity_id");
                                                        if ($models){
                                                            foreach ($models as $model){
                                                                $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 2);
                                                                // This because some system customer attribute_code was not using in Magento2
                                                                if ($attribute_id2){
                                                                    $model2 = new Mage2CustomerAddressEntityDecimal();
                                                                    foreach ($model2->attributes as $key => $value){
                                                                        if (isset($model->$key)){
                                                                            $model2->$key = $model->$key;
                                                                        }
                                                                    }
                                                                    $model2->attribute_id = $attribute_id2;
                                                                    $model2->save();
                                                                }
                                                            }
                                                        }

                                                        //customer_address_entity_int
                                                        $models = Mage1CustomerAddressEntityInt::model()->findAll("entity_id = $address_entity2->entity_id");
                                                        if ($models){
                                                            foreach ($models as $model){
                                                                $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 2);
                                                                // This because some system customer attribute_code was not using in Magento2
                                                                if ($attribute_id2){
                                                                    $model2 = new Mage2CustomerAddressEntityInt();
                                                                    foreach ($model2->attributes as $key => $value){
                                                                        if (isset($model->$key)){
                                                                            $model2->$key = $model->$key;
                                                                        }
                                                                    }
                                                                    $model2->attribute_id = $attribute_id2;
                                                                    $model2->save();
                                                                }
                                                            }
                                                        }
                                                        //customer_address_entity_text
                                                        $models = Mage1CustomerAddressEntityText::model()->findAll("entity_id = $address_entity2->entity_id");
                                                        if ($models){
                                                            foreach ($models as $model){
                                                                $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 2);
                                                                // This because some system customer attribute_code was not using in Magento2
                                                                if ($attribute_id2){
                                                                    $model2 = new Mage2CustomerAddressEntityText();
                                                                    foreach ($model2->attributes as $key => $value){
                                                                        if (isset($model->$key)){
                                                                            $model2->$key = $model->$key;
                                                                        }
                                                                    }
                                                                    $model2->attribute_id = $attribute_id2;
                                                                    $model2->save();
                                                                }
                                                            }
                                                        }
                                                        //customer_address_entity_varchar
                                                        $models = Mage1CustomerAddressEntityVarchar::model()->findAll("entity_id = $address_entity2->entity_id");
                                                        if ($models){
                                                            foreach ($models as $model){
                                                                $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 2);
                                                                // This because some system customer attribute_code was not using in Magento2
                                                                if ($attribute_id2){
                                                                    $model2 = new Mage2CustomerAddressEntityVarchar();
                                                                    foreach ($model2->attributes as $key => $value){
                                                                        if (isset($model->$key)){
                                                                            $model2->$key = $model->$key;
                                                                        }
                                                                    }
                                                                    $model2->attribute_id = $attribute_id2;
                                                                    $model2->save();
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }//end a customer entity address
                                        } //and save a customer entity
                                    }
                                }
                            }
                        }//end save a customer group
                    }

                    //customer_eav_attribute
                    //customer_eav_attribute_website
                    //customer_form_attribute

                }else{
                    Yii::app()->user->setFlash('note', Yii::t('frontend', 'You have not selected any Customer Groups.'));
                }

                //Update step status
                if ($migrated_customer_group_ids){
                    $step->status = MigrateSteps::STATUS_DONE;
                    $step->migrated_data = json_encode(array(
                        'customer_group_ids' => $migrated_customer_group_ids,
                        'customer_ids' => $migrated_customer_ids,
                    ));
                    if ($step->update()) {
                        //update session
                        Yii::app()->session['migrated_customer_group_ids'] = $migrated_customer_group_ids;
                        Yii::app()->session['migrated_customer_ids'] = $migrated_customer_ids;

                        //check foreign key
                        Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=1")->execute();

                        $message = "Migrated successfully. Total Customer Groups migrated: %s1 and total Customers migrated: %s2.";
                        $message = Yii::t('frontend', $message, array('%s1'=> sizeof($migrated_customer_group_ids), '%s2' => sizeof($migrated_customer_ids)));
                        Yii::app()->user->setFlash('success', $message);
                    }
                }
            }//end post request

            $this->render("step{$step->sorder}", array('step' => $step, 'customer_groups' => $customer_groups));
        }else{
            Yii::app()->user->setFlash('note', Yii::t('frontend', "The first you need to finish the %s.", array("%s" => ucfirst($result['back_step']))));
            $this->redirect(array($result['back_step']));
        }
    }

    /**
     * Migrate Data from:
     * Sales Orders, Sales Quote, Sales Payments, Sales Invoices, Sales Shipments
     */
    public function actionStep7()
    {
        $step = MigrateSteps::model()->find("sorder = 7");
        $result = MigrateSteps::checkStep($step->sorder);
        if ($result['allowed']){
            //declare objects to migrate
            $sales_objects = array(
                'order' => Yii::t('frontend', 'Sales Orders'),
                'quote' => Yii::t('frontend', 'Sales Quote'),
                'payment' => Yii::t('frontend', 'Sales Payments'),
                'invoice' => Yii::t('frontend', 'Sales Invoices'),
                'shipment' => Yii::t('frontend', 'Sales Shipments'),
                'credit' => Yii::t('frontend', 'Sales Credit Memo')
            );

            //variables to log
            $migrated_sales_object_ids = array();
            $migrated_order_ids = $migrated_quote_ids = $migrated_payment_ids = $migrated_invoice_ids = $migrated_shipment_ids = $migrated_credit_ids = array();
            $migrated_order_statuses = array();

            if (Yii::app()->request->isPostRequest){

                //reset database of this step if has
                $is_reset = Yii::app()->request->getPost('reset');
                if ($is_reset){
                    $dataPath = Yii::app()->basePath .DIRECTORY_SEPARATOR. "data".DIRECTORY_SEPARATOR;
                    $resetSQLFile = $dataPath . "step7_reset.sql";
                    if (file_exists($resetSQLFile)) {
                        $rs = MigrateSteps::executeFile($resetSQLFile);
                        if ($rs){
                            //reset step status
                            $step->status = MigrateSteps::STATUS_NOT_DONE;
                            $step->migrated_data = null;
                            if ($step->update()){
                                $this->refresh();
                            }
                        }
                    }
                }

                //uncheck foreign key
                Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=0")->execute();

                $selected_objects = Yii::app()->request->getPost('selected_objects', array());
                if ($selected_objects){
                    //get migrated data from first step in session
                    $migrated_store_ids = isset(Yii::app()->session['migrated_store_ids']) ? Yii::app()->session['migrated_store_ids'] : array();
                    $str_store_ids = implode(',', $migrated_store_ids);
                    $migrated_customer_ids = isset(Yii::app()->session['migrated_customer_ids']) ? Yii::app()->session['migrated_customer_ids'] : array();
                    $str_customer_ids = implode(',', $migrated_customer_ids);

                    if (in_array('order', $selected_objects)){
                        //sales_order_status
                        $models = Mage1SalesOrderStatus::model()->findAll();
                        if ($models) {
                            foreach ($models as $model){
                                $model2 = Mage2SalesOrderStatus::model()->find("status = '{$model->status}'");
                                if (!$model2){
                                    $model2 = new Mage2SalesOrderStatus();
                                    $model2->status = $model->status;
                                }
                                $model2->label = $model->label;

                                if ($model2->save()){
                                    $migrated_order_statuses[] = $model->status;

                                    //sales_order_status_label
                                    $condition = "status = '{$model->status}'";
                                    if ($str_store_ids) {
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1SalesOrderStatusLabel::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesOrderStatusLabel();
                                            $model2->attributes = $model->attributes;
                                            if ($model2->store_id){
                                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                            }
                                            $model2->save();
                                        }
                                    }
                                    //sales_order_status_state
                                    $condition = "status = '{$model->status}'";
                                    $models = Mage1SalesOrderStatusState::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = Mage2SalesOrderStatusState::model()->find("status = '{$model->status}' AND state = '{$model->state}'");
                                            if (!$model2){
                                                $model2 = new Mage2SalesOrderStatusState();
                                                $model2->status = $model->status;
                                                $model2->state = $model->state;
                                                //this field not exists in Magento1
                                                $model2->visible_on_front = 0;
                                            }
                                            $model2->is_default = $model->is_default;
                                            $model2->save();
                                        }
                                    }
                                }
                            }
                        }

                        //sales_order
                        $condition = "( store_id IN ({$str_store_ids}) OR store_id IS NULL ) AND ( customer_id IN ({$str_customer_ids}) OR customer_id IS NULL )";
                        $sales_orders = Mage1SalesOrder::model()->findAll($condition);
                        if ($sales_orders){
                            foreach ($sales_orders as $sales_order){
                                $sales_order2 = new Mage2SalesOrder();
                                foreach ($sales_order2->attributes as $key => $value){
                                    if (isset($sales_order->$key)){
                                        $sales_order2->$key = $sales_order->$key;
                                    }
                                }
                                //we have changed store_id in magento2
                                if ($sales_order2->store_id){
                                    $sales_order2->store_id = MigrateSteps::getMage2StoreId($sales_order2->store_id);
                                }
                                if ($sales_order2->save()){
                                    $migrated_order_ids[] = $sales_order->entity_id;

                                    //sales_order_address
                                    $models = Mage1SalesOrderAddress::model()->findAll("parent_id = {$sales_order->entity_id}");
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesOrderAddress();
                                            foreach ($model2->attributes as $key => $value){
                                                if (isset($model->$key)){
                                                    $model2->$key = $model->$key;
                                                }
                                            }
                                            $model2->save();
                                        }
                                    }
                                    //sales_order_grid
                                    $models = Mage1SalesOrderGrid::model()->findAll("entity_id = {$sales_order->entity_id}");
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesOrderGrid();
                                            foreach ($model2->attributes as $key => $value){
                                                if (isset($model->$key)){
                                                    $model2->$key = $model->$key;
                                                }
                                            }
                                            //we have changed store_id in magento2
                                            if ($model2->store_id){
                                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                            }
                                            $model2->save();
                                        }
                                    }
                                    //sales_order_item
                                    $models = Mage1SalesOrderItem::model()->findAll("order_id = {$sales_order->entity_id}");
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesOrderItem();
                                            foreach ($model2->attributes as $key => $value){
                                                if (isset($model->$key)){
                                                    $model2->$key = $model->$key;
                                                }
                                            }
                                            if ($model2->store_id){
                                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                            }
                                            $model2->save();
                                        }
                                    }
                                    //sales_order_status_history
                                    $models = Mage1SalesOrderStatusHistory::model()->findAll("parent_id = {$sales_order->entity_id}");
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesOrderStatusHistory();
                                            foreach ($model2->attributes as $key => $value){
                                                if (isset($model->$key)){
                                                    $model2->$key = $model->$key;
                                                }
                                            }
                                            $model2->save();
                                        }
                                    }
                                    //sales_order_tax
                                    $models = Mage1SalesOrderTax::model()->findAll("order_id = {$sales_order->entity_id}");
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesOrderTax();
                                            foreach ($model2->attributes as $key => $value){
                                                if (isset($model->$key)){
                                                    $model2->$key = $model->$key;
                                                }
                                            }
                                            if ($model2->save()){
                                                //sales_order_tax_item
                                                $items = Mage1SalesOrderTaxItem::model()->findAll("tax_id = {$model->tax_id}");
                                                if ($items){
                                                    foreach ($items as $item){
                                                        $item2 = new Mage2SalesOrderTaxItem();
                                                        foreach ($item2->attributes as $key => $value){
                                                            if (isset($item->$key)){
                                                                $item2->$key = $item->$key;
                                                            }
                                                        }
                                                        //bellow fields was not exists in Magento1 -> note
                                                        $item2->amount = 0;
                                                        $item2->base_amount = 0;
                                                        $item2->real_amount = 0;
                                                        $item2->real_base_amount = 0;
                                                        $item2->associated_item_id = null;
                                                        $item2->taxable_item_type = '';
                                                        $item2->save();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }//end save a sales order
                            }
                        }

                        //sales_order_aggregated_created
                        $condition = "store_id IN ({$str_store_ids}) OR store_id is NULL";
                        $models = Mage1SalesOrderAggregatedCreated::model()->findAll($condition);
                        if ($models){
                            foreach ($models as $model){
                                $model2 = new Mage2SalesOrderAggregatedCreated();
                                foreach ($model2->attributes as $key => $value){
                                    if (isset($model->$key)){
                                        $model2->$key = $model->$key;
                                    }
                                }
                                if ($model2->store_id){
                                    $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                }
                                $model2->save();
                            }
                        }
                        //sales_order_aggregated_updated
                        $condition = "store_id IN ({$str_store_ids}) OR store_id is NULL";
                        $models = Mage1SalesOrderAggregatedUpdated::model()->findAll($condition);
                        if ($models){
                            foreach ($models as $model){
                                $model2 = new Mage2SalesOrderAggregatedUpdated();
                                foreach ($model2->attributes as $key => $value){
                                    if (isset($model->$key)){
                                        $model2->$key = $model->$key;
                                    }
                                }
                                if ($model2->store_id){
                                    $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                }
                                $model2->save();
                            }
                        }

                        $migrated_sales_object_ids[] = 'order';
                    }//end migrate orders

                    //Sales Quote
                    if (in_array('quote', $selected_objects)){
                        //quote
                        //$condition = "( store_id IN ({$str_store_ids}) OR store_id IS NULL ) AND ( customer_id IN ({$str_customer_ids}) OR customer_id IS NULL )";
                        $condition = "( store_id IN ({$str_store_ids}) OR store_id IS NULL )";
                        $quotes = Mage1SalesQuote::model()->findAll($condition);
                        if ($quotes){
                            foreach ($quotes as $quote){
                                $quote2 = new Mage2SalesQuote();
                                foreach ($quote2->attributes as $key => $value){
                                    if (isset($quote->$key)){
                                        $quote2->$key = $quote->$key;
                                    }
                                }
                                $quote2->store_id = MigrateSteps::getMage2StoreId($quote->store_id);
                                if ($quote2->save()){
                                    $migrated_quote_ids[] = $quote->entity_id;

                                    //quote_item
                                    $models = Mage1SalesQuoteItem::model()->findAll("quote_id = {$quote->entity_id}");
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesQuoteItem();
                                            foreach ($model2->attributes as $key => $value){
                                                if (isset($model->$key)){
                                                    $model2->$key = $model->$key;
                                                }
                                            }
                                            $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                            if ($model2->save()){
                                                //quote_item_option
                                                $item_options = Mage1SalesQuoteItemOption::model()->findAll("item_id = {$model->item_id}");
                                                if ($item_options){
                                                    foreach ($item_options as $item_option){
                                                        $item_option2 = new Mage2SalesQuoteItemOption();
                                                        foreach ($item_option2->attributes as $key => $value){
                                                            if (isset($item_option->$key)){
                                                                $item_option2->$key = $item_option->$key;
                                                            }
                                                        }
                                                        $item_option2->save();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    //quote_payment
                                    $models = Mage1SalesQuotePayment::model()->findAll("quote_id = {$quote->entity_id}");
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesQuotePayment();
                                            foreach ($model2->attributes as $key => $value){
                                                if (isset($model->$key)){
                                                    $model2->$key = $model->$key;
                                                }
                                            }
                                            $model2->save();
                                        }
                                    }

                                    //quote_address
                                    $models = Mage1SalesQuoteAddress::model()->findAll("quote_id = {$quote->entity_id}");
                                    if ($models){
                                        foreach ($models as $model){
                                            $model2 = new Mage2SalesQuoteAddress();
                                            foreach ($model2->attributes as $key => $value){
                                                if (isset($model->$key)){
                                                    $model2->$key = $model->$key;
                                                }
                                            }
                                            if ($model2->save()){
                                                //quote_address_item
                                                $address_items = Mage1SalesQuoteAddressItem::model()->findAll("quote_address_id = {$model->address_id}");
                                                if ($address_items){
                                                    foreach ($address_items as $address_item){
                                                        $address_item2 = new Mage2SalesQuoteAddressItem();
                                                        foreach ($address_item2->attributes as $key => $value){
                                                            if (isset($address_item->$key)){
                                                                $address_item2->$key = $address_item->$key;
                                                            }
                                                        }
                                                        $address_item2->save();
                                                    }
                                                }
                                                //quote_shipping_rate
                                                $shipping_rates = Mage1SalesQuoteShippingRate::model()->findAll("address_id = {$model->address_id}");
                                                if ($shipping_rates){
                                                    foreach ($shipping_rates as $shipping_rate){
                                                        $shipping_rate2 = new Mage2SalesQuoteShippingRate();
                                                        foreach ($shipping_rate2->attributes as $key => $value){
                                                            if (isset($shipping_rate->$key)){
                                                                $shipping_rate2->$key = $shipping_rate->$key;
                                                            }
                                                        }
                                                        $shipping_rate2->save();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $migrated_sales_object_ids[] = 'quote';
                    }//end sales quote

                    if (in_array('payment', $selected_objects)){
                        if ($migrated_order_ids){
                            $str_order_ids = implode(',', $migrated_order_ids);
                            $condition = "parent_id IN ({$str_order_ids})";
                            //sales_order_payment
                            $sales_payments = Mage1SalesOrderPayment::model()->findAll($condition);
                            if ($sales_payments){
                                foreach ($sales_payments as $sales_payment){
                                    $sales_payment2 = new Mage2SalesOrderPayment();
                                    foreach($sales_payment2->attributes as $key => $value){
                                        if (isset($sales_payment->$key)){
                                            $sales_payment2->$key = $sales_payment->$key;
                                        }
                                    }
                                    //because the this field name was changed in Magento 2
                                    $sales_payment2->cc_last_4 = isset($sales_payment->cc_last4) ? $sales_payment->cc_last4 : null;
                                    if ($sales_payment2->save()){
                                        //sales_payment_transaction
                                        $models = Mage1SalesPaymentTransaction::model()->findAll("payment_id = {$sales_payment->entity_id}");
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesPaymentTransaction();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //sales_refunded_aggregated
                        $condition = "store_id IN ({$str_store_ids}) OR store_id IS NULL";
                        $models = Mage1SalesRefundedAggregated::model()->findAll($condition);
                        if ($models){
                            foreach ($models as $model){
                                $model2 = new Mage2SalesRefundedAggregated();
                                foreach ($model2->attributes as $key => $value){
                                    if (isset($model->$key)){
                                        $model2->$key = $model->$key;
                                    }
                                }
                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                $model2->save();
                            }
                        }
                        //sales_refunded_aggregated_order
                        $condition = "store_id IN ({$str_store_ids}) OR store_id IS NULL";
                        $models = Mage1SalesRefundedAggregatedOrder::model()->findAll($condition);
                        if ($models){
                            foreach ($models as $model){
                                $model2 = new Mage2SalesRefundedAggregatedOrder();
                                foreach ($model2->attributes as $key => $value){
                                    if (isset($model->$key)){
                                        $model2->$key = $model->$key;
                                    }
                                }
                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                $model2->save();
                            }
                        }

                        $migrated_sales_object_ids[] = 'payment';
                    }//end sales payment

                    if (in_array('invoice', $selected_objects)){
                        if ($migrated_order_ids){
                            $condition = "( store_id IN ({$str_store_ids}) OR store_id IS NULL )";
                            $str_order_ids = implode(',', $migrated_order_ids);
                            $condition .= " AND order_id IN ({$str_order_ids})";

                            //sales_invoice
                            $sales_invoices = Mage1SalesInvoice::model()->findAll($condition);
                            if ($sales_invoices){
                                foreach ($sales_invoices as $sales_invoice){
                                    $sales_invoice2 = new Mage2SalesInvoice();
                                    foreach ($sales_invoice2->attributes as $key => $value){
                                        if (isset($sales_invoice->$key)){
                                            $sales_invoice2->$key = $sales_invoice->$key;
                                        }
                                    }
                                    $sales_invoice2->store_id = MigrateSteps::getMage2StoreId($sales_invoice->store_id);
                                    if ($sales_invoice2->save()){
                                        $migrated_invoice_ids[] = $sales_invoice->entity_id;

                                        //sales_invoice_grid
                                        $condition = "entity_id = {$sales_invoice->entity_id}";
                                        $models = Mage1SalesInvoiceGrid::model()->findAll($condition);
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesInvoiceGrid();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                                //this field was not exists in Magento1
                                                $model2->updated_at = null;
                                                $model2->save();
                                            }
                                        }
                                        //sales_invoice_item
                                        $condition = "parent_id = {$sales_invoice->entity_id}";
                                        $models = Mage1SalesInvoiceItem::model()->findAll($condition);
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesInvoiceItem();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                //this field was not exists in Magento1
                                                $model2->tax_ratio = null;
                                                $model2->save();
                                            }
                                        }
                                        //sales_invoice_comment
                                        $condition = "parent_id = {$sales_invoice->entity_id}";
                                        $models = Mage1SalesInvoiceComment::model()->findAll($condition);
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesInvoiceComment();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //sales_invoiced_aggregated
                        $condition = "store_id IN ({$str_store_ids}) OR store_id IS NULL";
                        $models = Mage1SalesInvoicedAggregated::model()->findAll($condition);
                        if ($models){
                            foreach ($models as $model){
                                $model2 = new Mage2SalesInvoicedAggregated();
                                foreach ($model2->attributes as $key => $value){
                                    if (isset($model->$key)){
                                        $model2->$key = $model->$key;
                                    }
                                }
                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                $model2->save();
                            }
                        }
                        //sales_invoiced_aggregated_order
                        $condition = "store_id IN ({$str_store_ids}) OR store_id IS NULL";
                        $models = Mage1SalesInvoicedAggregatedOrder::model()->findAll($condition);
                        if ($models){
                            foreach ($models as $model){
                                $model2 = new Mage2SalesInvoicedAggregatedOrder();
                                foreach ($model2->attributes as $key => $value){
                                    if (isset($model->$key)){
                                        $model2->$key = $model->$key;
                                    }
                                }
                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                $model2->save();
                            }
                        }

                        $migrated_sales_object_ids[] = 'invoice';
                    }//end sales invoice migration

                    //Sales shipments migration
                    if (in_array('shipment', $selected_objects)){
                        if ($migrated_order_ids){
                            $condition = "( store_id IN ({$str_store_ids}) OR store_id IS NULL )";
                            $str_order_ids = implode(',', $migrated_order_ids);
                            $condition .= " AND order_id IN ({$str_order_ids})";

                            //sales_shipment
                            $sales_shipments = Mage1SalesShipment::model()->findAll($condition);
                            if ($sales_shipments){
                                foreach($sales_shipments as $sales_shipment){
                                    $sales_shipment2 = new Mage2SalesShipment();
                                    foreach ($sales_shipment2->attributes as $key => $value){
                                        if (isset($sales_shipment->$key)){
                                            $sales_shipment2->$key = $sales_shipment->$key;
                                        }
                                    }
                                    $sales_shipment2->store_id = MigrateSteps::getMage2StoreId($sales_shipment->store_id);
                                    if ($sales_shipment2->save()){
                                        $migrated_shipment_ids[] = $sales_shipment->entity_id;

                                        //sales_shipment_grid
                                        $models = Mage1SalesShipmentGrid::model()->findAll("entity_id = {$sales_shipment->entity_id}");
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesShipmentGrid();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                                //this field was not exists in Magento 1
                                                $model2->updated_at = null;
                                                $model2->save();
                                            }
                                        }
                                        //sales_shipment_item
                                        $models = Mage1SalesShipmentItem::model()->findAll("parent_id = {$sales_shipment->entity_id}");
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesShipmentItem();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->save();
                                            }
                                        }
                                        //sales_shipment_track
                                        $models = Mage1SalesShipmentTrack::model()->findAll("parent_id = {$sales_shipment->entity_id}");
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesShipmentTrack();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->save();
                                            }
                                        }
                                        //sales_shipment_comment
                                        $models = Mage1SalesShipmentComment::model()->findAll("parent_id = {$sales_shipment->entity_id}");
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesShipmentComment();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }
                                }
                            }
                            //sales_shipping_aggregated
                            $condition = "store_id IN ({$str_store_ids}) OR store_id IS NULL";
                            $models = Mage1SalesShippingAggregated::model()->findAll($condition);
                            if ($models){
                                foreach ($models as $model){
                                    $model2 = new Mage2SalesShippingAggregated();
                                    foreach ($model2->attributes as $key => $value){
                                        if (isset($model->$key)){
                                            $model2->$key = $model->$key;
                                        }
                                    }
                                    $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                    $model2->save();
                                }
                            }
                            //sales_shipping_aggregated_order
                            $condition = "store_id IN ({$str_store_ids}) OR store_id IS NULL";
                            $models = Mage1SalesShippingAggregatedOrder::model()->findAll($condition);
                            if ($models){
                                foreach ($models as $model){
                                    $model2 = new Mage2SalesShippingAggregatedOrder();
                                    foreach ($model2->attributes as $key => $value){
                                        if (isset($model->$key)){
                                            $model2->$key = $model->$key;
                                        }
                                    }
                                    $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                    $model2->save();
                                }
                            }

                            $migrated_sales_object_ids[] = 'shipment';
                        }
                    }//end sales shipment migration

                    //Sales credit memo migration
                    if (in_array('credit', $selected_objects)){
                        if ($migrated_order_ids){
                            $condition = "( store_id IN ({$str_store_ids}) OR store_id IS NULL )";
                            $str_order_ids = implode(',', $migrated_order_ids);
                            $condition .= " AND order_id IN ({$str_order_ids})";

                            //sales_creditmemo
                            $sales_credits = Mage1SalesCreditmemo::model()->findAll($condition);
                            if ($sales_credits){
                                foreach ($sales_credits as $sales_credit){
                                    $sales_credit2 = new Mage2SalesCreditmemo();
                                    foreach ($sales_credit2->attributes as $key => $value){
                                        if (isset($sales_credit->$key)){
                                            $sales_credit2->$key = $sales_credit->$key;
                                        }
                                    }
                                    $sales_credit2->store_id = MigrateSteps::getMage2StoreId($sales_credit->store_id);
                                    if ($sales_credit2->save()){
                                        //this for log
                                        $migrated_credit_ids[] = $sales_credit->store_id;

                                        //sales_creditmemo_grid
                                        $models = Mage1SalesCreditmemoGrid::model()->findAll("entity_id = {$sales_credit->entity_id}");
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesCreditmemoGrid();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->store_id = MigrateSteps::getMage2StoreId($model->store_id);
                                                //this field was not exists in Magento 1
                                                $model2->updated_at = null;
                                                $model2->save();
                                            }
                                        }
                                        //sales_creditmemo_item
                                        $models = Mage1SalesCreditmemoItem::model()->findAll("parent_id = {$sales_credit->entity_id}");
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesCreditmemoItem();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                //this field was not exists in Magento1
                                                $model2->tax_ratio = null;
                                                $model2->save();
                                            }
                                        }
                                        //sales_creditmemo_comment
                                        $models = Mage1SalesCreditmemoComment::model()->findAll("parent_id = {$sales_credit->entity_id}");
                                        if ($models){
                                            foreach ($models as $model){
                                                $model2 = new Mage2SalesCreditmemoComment();
                                                foreach ($model2->attributes as $key => $value){
                                                    if (isset($model->$key)){
                                                        $model2->$key = $model->$key;
                                                    }
                                                }
                                                $model2->save();
                                            }
                                        }
                                    }
                                }
                            }
                            $migrated_sales_object_ids[] = 'credit';
                        }
                    }//End Sales credit memo migration
                }else{
                    Yii::app()->user->setFlash('note', Yii::t('frontend', 'You have not selected any Object.'));
                }

                //Update step status
                if ($migrated_sales_object_ids && $migrated_order_ids){
                    $step->status = MigrateSteps::STATUS_DONE;
                    $step->migrated_data = json_encode(array(
                        'sales_object_ids' => $migrated_sales_object_ids,
                        'sales_order_ids' => $migrated_order_ids,
                        'sales_quote_ids' => $migrated_quote_ids,
                        'sales_invoice_ids' => $migrated_invoice_ids,
                        'sales_shipment_ids' => $migrated_shipment_ids,
                        'sales_credit_ids' => $migrated_credit_ids
                    ));
                    if ($step->update()) {

                        //check foreign key
                        Yii::app()->mage2->createCommand("SET FOREIGN_KEY_CHECKS=1")->execute();

                        //update session
                        Yii::app()->session['migrated_sales_object_ids'] = $migrated_sales_object_ids;
                        Yii::app()->session['migrated_sales_order_ids'] = $migrated_order_ids;

                        $message = Yii::t('frontend', 'Migrated successfully');
                        $message .= "<br/>". Yii::t('frontend', "Total Sales Orders migrated: %s1.", array('%s1' => sizeof($migrated_order_ids)));
                        $message .= "<br/>". Yii::t('frontend', "Total Orders Statuses migrated: %s2.", array('%s2' => sizeof($migrated_order_statuses)));
                        $message .= "<br/>". Yii::t('frontend', "Total Sales Quote migrated: %s3.", array('%s3' => sizeof($migrated_quote_ids)));
                        $message .= "<br/>". Yii::t('frontend', "Total Sales Invoices migrated: %s4.", array('%s4' => sizeof($migrated_invoice_ids)));
                        $message .= "<br/>". Yii::t('frontend', "Total Sales Shipments migrated: %s5.", array('%s5' => sizeof($migrated_shipment_ids)));
                        $message .= "<br/>". Yii::t('frontend', "Total Sales Credit Memo migrated: %s6.", array('%s6' => sizeof($migrated_credit_ids)));

                        Yii::app()->user->setFlash('success', $message);
                    }
                }
            }//end post request

            $this->render("step{$step->sorder}", array('step' => $step, 'sale_objects' => $sales_objects));
        }else{
            Yii::app()->user->setFlash('note', Yii::t('frontend', "The first you need to finish the %s.", array("%s" => ucfirst($result['back_step']))));
            $this->redirect(array($result['back_step']));
        }
    }

    /**
     * Reset Database of magento2
     */
    public function actionResetAll(){
        $dataPath = Yii::app()->basePath .DIRECTORY_SEPARATOR. "data".DIRECTORY_SEPARATOR;
        $steps = MigrateSteps::model()->findAll();
        if ($steps){
            foreach ($steps as $step){
                //only for step1
                if($step->sorder == 1){
                    $step->status = MigrateSteps::STATUS_NOT_DONE;
                    $step->migrated_data = null;
                    $step->update();
                }

                //other steps
                $resetSQLFile = $dataPath . "step{$step->sorder}_reset.sql";
                if (file_exists($resetSQLFile)) {
                    $rs = MigrateSteps::executeFile($resetSQLFile);
                    if ($rs){
                        $step->status = MigrateSteps::STATUS_NOT_DONE;
                        $step->migrated_data = null;
                        $step->update();
                    }
                }
            }

            //delete url related data in url_rewrite table and catalog_url_rewrite_product_category table
            Mage2UrlRewrite::model()->deleteAll("entity_type = 'category'");

            //delete url related data in url_rewrite table and catalog_url_rewrite_product_category table
            Mage2UrlRewrite::model()->deleteAll("entity_type = 'product'");

            Yii::app()->user->setFlash('success', Yii::t('frontend', "Reset all successfully."));
            //Redirect to next step
            $nextStep = MigrateSteps::getNextSteps();
            $this->redirect(array($nextStep));
        }
    }
}
