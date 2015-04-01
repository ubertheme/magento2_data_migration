<?php

class MigrateController extends Controller
{
	public $layout = '2column_left';

    protected function beforeAction($action) {
        //increase the max execution time
        @ini_set('max_execution_time', -1);

        // SET FOREIGN_KEY_CHECKS=0;
        $sql = "SET FOREIGN_KEY_CHECKS=0";
        Yii::app()->mage2->createCommand($sql)->execute();


        //initial needed session variables
        //needed session variables
        $migrated_data = array(
            'website_ids' => array(),
            'store_group_ids' => array(),
            'store_ids' => array(),
            'category_ids' => array(),
            'product_type_ids' => array(),
            'product_ids' => array()
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
        // SET FOREIGN_KEY_CHECKS=0;
        $sql = "SET FOREIGN_KEY_CHECKS=1";
        Yii::app()->mage2->createCommand($sql)->execute();

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
     * Migrate Websites & Store groups & Store views
     */
    public function actionStep1()
    {
        $step = MigrateSteps::model()->find("sorder = 1");
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
                    $resetSQLFile = $dataPath . "step1_reset.sql";
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

                //start migrate process
                $website_ids = Yii::app()->request->getParam('website_ids', array());
                $store_group_ids = Yii::app()->request->getParam('store_group_ids', array());
                $store_ids = Yii::app()->request->getParam('store_ids', array());

                // if has selected websites, store groups, stores
                if (sizeof($website_ids) > 0 AND sizeof($store_group_ids) > 0 AND sizeof($store_ids) > 0){
                    foreach ($websites as $website){
                        if (in_array($website->website_id, $website_ids)){
                            $condition = "code = '{$website->code}'";
                            $website2 = Mage2Website::model()->find($condition);
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
                                                            //$condition = "code = '{$store->code}' AND website_id = {$website->website_id} AND group_id = {$store->group_id}";
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
                                                            if ($store2->save())
                                                            {
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
    public function actionStep2()
    {
        $step = MigrateSteps::model()->find("sorder = 2");
        $result = MigrateSteps::checkStep($step->sorder);
        if ($result['allowed']){

            //get migrated data of step1 from session
            $migrated_store_ids = isset(Yii::app()->session['migrated_store_ids']) ? Yii::app()->session['migrated_store_ids'] : array();

            $total_attribute_set = $total_attribute_group = $total_attribute = $total_entity_attribute = 0;
            $migrated_attribute_set_ids = $migrated_attribute_group_ids = $migrated_attribute_ids = array();

            //get all product attribute sets in magento1
            $condition = "entity_type_id = 4";
            $attribute_sets = Mage1AttributeSet::model()->findAll($condition);

            //get all product attributes
            //$condition = "entity_type_id = 4 AND is_user_defined = 1";
            $condition = "entity_type_id = 4";
            $attributes = Mage1Attribute::model()->findAll($condition);

            if (Yii::app()->request->isPostRequest){

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

                        $condition = "entity_type_id = 4 AND attribute_code = '{$attribute_code2}'";
                        $attribute2 = Mage2Attribute::model()->find($condition);
                        if (!$attribute2){
                            $attribute2 = new Mage2Attribute();
                            $attribute2->entity_type_id = $attribute->entity_type_id;
                            $attribute2->attribute_code = $attribute->attribute_code;
                            $attribute2->attribute_model = $attribute->attribute_model;
                            $attribute2->backend_model = null;
                            $attribute2->backend_type = $attribute->backend_type;
                            $attribute2->backend_table = $attribute->backend_table;
                            //$attribute2->frontend_model = $attribute->frontend_model; // note this was changed in magento2
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
                                    $catalog_eav_attribute2->search_weight = $catalog_eav_attribute->search_weight;
                                    //this attribute removed in Magento2 beta11
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
                    $condition = "entity_type_id = 4 AND attribute_id IN ($str_migrated_attribute_ids)";
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
                                //$entity_attribute2->entity_attribute_id = $entity_attribute->entity_attribute_id;
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
    public function actionStep3()
    {
        $step = MigrateSteps::model()->find("sorder = 3");
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
                    $resetSQLFile = $dataPath . "step3_reset.sql";
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

                //get all categories from magento1
                $categories = Mage1CatalogCategoryEntity::model()->findAll();

                /*
                 * Get black list attribute ids
                 * We do not migrate bellow attributes
                */
                $checkList = array(
                    MigrateSteps::getMage1AttributeId('display_mode', 3) => 'PRODUCTS',
                    MigrateSteps::getMage1AttributeId('landing_page', 3) => '',
                    MigrateSteps::getMage1AttributeId('custom_design', 3) => '',
                    MigrateSteps::getMage1AttributeId('custom_design_from', 3) => '',
                    MigrateSteps::getMage1AttributeId('custom_design_to', 3) => '',
                    MigrateSteps::getMage1AttributeId('page_layout', 3) => '',
                    MigrateSteps::getMage1AttributeId('custom_layout_update', 3) => '',
                    MigrateSteps::getMage1AttributeId('custom_apply_to_products', 3) => 1,
                    MigrateSteps::getMage1AttributeId('custom_use_parent_settings', 3) => 1,
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
                                            //we have to get correct attribute_id & store_id migrated
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

                                    //catalog_category_entity_text
                                    $condition = "entity_id = {$category->entity_id}";
                                    if ($migrated_store_ids) {
                                        $str_store_ids = implode(',', $migrated_store_ids);
                                        $condition .= " AND store_id IN ({$str_store_ids})";
                                    }
                                    $models = Mage1CatalogCategoryEntityText::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id & store_id migrated
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
                                            //we have to get correct attribute_id & store_id migrated
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
    public function actionStep4()
    {
        $step = MigrateSteps::model()->find("sorder = 4");
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
                    $resetSQLFile = $dataPath . "step4_reset.sql";
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

                /*
                 * Get black list attribute ids
                 * We do not migrate bellow attributes
                */
                $checkList = array(
                    MigrateSteps::getMage1AttributeId('custom_design', 4) => '',
                    MigrateSteps::getMage1AttributeId('custom_design_from', 4) => null,
                    MigrateSteps::getMage1AttributeId('custom_design_to', 4) => null,
                    MigrateSteps::getMage1AttributeId('page_layout', 4) => '',
                    MigrateSteps::getMage1AttributeId('custom_layout_update', 4) => null,
                );
                $keyCheckList = array_keys($checkList);

                $selected_product_types = Yii::app()->request->getPost('product_type_ids', array());
                if ($selected_product_types){
                    foreach ($selected_product_types as $type_id){
                        // get products by type_id
                        // catalog_product_entity
                        $products = Mage1CatalogProductEntity::model()->findAll("type_id = '{$type_id}'");
                        if ($products){
                            foreach ($products as $product){
                                $product2 = Mage2CatalogProductEntity::model()->find("entity_id = $product->entity_id");
                                if (!$product2){
                                    $product2 = new Mage2CatalogProductEntity();
                                    $product2->entity_id = $product->entity_id;
                                    $product2->attribute_set_id = $product->attribute_set_id;
                                    $product2->type_id = $type_id;
                                    $product2->sku = $product->sku;
                                    $product2->has_options = $product->has_options;
                                    $product2->required_options = $product->required_options;
                                    $product2->created_at = $product->created_at;
                                    $product2->updated_at = $product->updated_at;
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
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogProductEntityInt::model()->find($condition);
                                                if (!$model2){
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
                                            //we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogProductEntityText::model()->find($condition);
                                                if (!$model2){
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
                                            //we have to get correct attribute_id & store_id migrated
                                            $store_id2 = MigrateSteps::getMage2StoreId($model->store_id);
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogProductEntityVarchar::model()->find($condition);
                                                if (!$model2){
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
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogProductEntityDatetime::model()->find($condition);
                                                if (!$model2){
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
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogProductEntityDecimal::model()->find($condition);
                                                if (!$model2){
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
                                                $condition = "entity_id= {$model->entity_id} AND attribute_id = {$attribute_id2} AND store_id = {$store_id2}";
                                                $model2 = Mage2CatalogProductEntityGallery::model()->find($condition);
                                                if (!$model2){
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
                                    }

                                    //catalog_product_entity_media_gallery
                                    $condition = "entity_id = {$product->entity_id}";
                                    $models = Mage1CatalogProductEntityMediaGallery::model()->findAll($condition);
                                    if ($models){
                                        foreach ($models as $model){
                                            //we have to get correct attribute_id migrated
                                            $attribute_id2 = MigrateSteps::getMage2AttributeId($model->attribute_id, 4);
                                            if ($attribute_id2){
                                                $model2 = Mage2CatalogProductEntityMediaGallery::model()->find("value_id = {$model->value_id}");
                                                if (!$model2){
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
                                                                    $gallery_value2 = Mage2CatalogProductEntityMediaGalleryValue::model()->find("value_id = {$model->value_id} AND store_id = {$store_id2}");
                                                                    if (!$gallery_value2){
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
                                        }
                                    }

                                    //catalog_product_option
                                    $condition = "product_id = {$product->entity_id}";
                                    $product_options = Mage1CatalogProductOption::model()->findAll($condition);
                                    if ($product_options){
                                        foreach ($product_options as $product_option){
                                            $condition = "option_id = {$product_option->option_id}";
                                            $product_option2 = Mage2CatalogProductOption::model()->find($condition);
                                            if (!$product_option2){
                                                $product_option2 = new Mage2CatalogProductOption();
                                                $product_option2->option_id = $product_option->option_id;
                                                $product_option2->product_id = $product_option->product_id;
                                                $product_option2->type = $product_option->type;
                                                $product_option2->is_require = $product_option->is_require;
                                                $product_option2->sku = $product_option->sku;
                                                $product_option2->max_characters = $product_option->max_characters;
                                                $product_option2->file_extension = $product_option->file_extension;
                                                $product_option2->image_size_x = $product_option->image_size_x;
                                                $product_option2->image_size_y = $product_option->image_size_y;
                                                $product_option2->sort_order = $product_option->sort_order;
                                                if ($product_option2->save()){

                                                    //catalog_product_option_type_value
                                                    $condition = "option_id = {$product_option->option_id}";
                                                    $option_type_values = Mage1CatalogProductOptionTypeValue::model()->findAll($condition);
                                                    if ($option_type_values){
                                                        foreach ($option_type_values as $option_type_value){
                                                            $option_type_value2 = Mage2CatalogProductOptionTypeValue::model()->find("option_type_id = {$option_type_value->option_type_id}");
                                                            if (!$option_type_value2){
                                                                $option_type_value2 = new Mage2CatalogProductOptionTypeValue();
                                                                $option_type_value2->option_type_id = $option_type_value->option_type_id;
                                                                $option_type_value2->option_id = $option_type_value->option_id;
                                                                $option_type_value2->sku = $option_type_value->sku;
                                                                $option_type_value2->sort_order = $option_type_value->sort_order;
                                                                if ($option_type_value2->save()){
                                                                    //catalog_product_option_type_price & catalog_product_option_type_title
                                                                    if ($migrated_store_ids){
                                                                        foreach ($migrated_store_ids as $store_id){
                                                                            $store_id2 = MigrateSteps::getMage2StoreId($store_id);

                                                                            //catalog_product_option_type_price
                                                                            $condition = "option_type_id = {$option_type_value->option_type_id} AND store_id = {$store_id}";
                                                                            $option_type_price = Mage1CatalogProductOptionTypePrice::model()->find($condition);
                                                                            if ($option_type_price){
                                                                                $condition = "option_type_id = {$option_type_value->option_type_id} AND store_id = {$store_id2}";
                                                                                $option_type_price2 = Mage2CatalogProductOptionTypePrice::model()->find($condition);
                                                                                if (!$option_type_price2){
                                                                                    $option_type_price2 = new Mage2CatalogProductOptionTypePrice();
                                                                                    $option_type_price2->option_type_price_id = $option_type_price->option_type_price_id;
                                                                                    $option_type_price2->option_type_id = $option_type_price->option_type_id;
                                                                                    $option_type_price2->store_id = $store_id2;
                                                                                    $option_type_price2->price = $option_type_price->price;
                                                                                    $option_type_price2->price_type = $option_type_price->price_type;
                                                                                    $option_type_price2->save();
                                                                                }
                                                                            }

                                                                            //catalog_product_option_type_title
                                                                            $condition = "option_type_id = {$option_type_value->option_type_id} AND store_id = {$store_id}";
                                                                            $option_type_title = Mage1CatalogProductOptionTypeTitle::model()->find($condition);
                                                                            if ($option_type_title){
                                                                                $condition = "option_type_id = {$option_type_title->option_type_id} AND store_id = {$store_id2}";
                                                                                $option_type_title2 = Mage2CatalogProductOptionTypeTitle::model()->find($condition);
                                                                                if (!$option_type_title2){
                                                                                    $option_type_title2 = new Mage2CatalogProductOptionTypeTitle();
                                                                                    $option_type_title2->option_type_title_id = $option_type_title->option_type_title_id;
                                                                                    $option_type_title2->option_type_id = $option_type_title->option_type_id;
                                                                                    $option_type_title2->store_id = $store_id2;
                                                                                    $option_type_title2->title = $option_type_title->title;
                                                                                    $option_type_title2->save();
                                                                                }
                                                                            }
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
                                                                $option_price2 = Mage2CatalogProductOptionPrice::model()->find("option_id = {$product_option->option_id} AND store_id = {$store_id2}");
                                                                if (!$option_price2){
                                                                    $option_price2 = new Mage2CatalogProductOptionPrice();
                                                                    $option_price2->option_price_id = $option_price->option_price_id;
                                                                    $option_price2->option_id = $option_price->option_id;
                                                                    $option_price2->store_id = $store_id2;
                                                                    $option_price2->price = $option_price->price;
                                                                    $option_price2->price_type = $option_price->price_type;
                                                                    $option_price2->save();
                                                                }
                                                            }

                                                            //catalog_product_option_title
                                                            $option_title = Mage1CatalogProductOptionTitle::model()->find("option_id = {$product_option->option_id} AND store_id = {$store_id}");
                                                            if ($option_title){
                                                                $option_title2 = Mage2CatalogProductOptionTitle::model()->find("option_id = {$product_option->option_id} AND store_id = {$store_id2}");
                                                                if (!$option_title2){
                                                                    $option_title2 = new Mage2CatalogProductOptionTitle();
                                                                    $option_title2->option_title_id = $option_title->option_title_id;
                                                                    $option_title2->option_id = $option_title->option_id;
                                                                    $option_title2->store_id = $store_id2;
                                                                    $option_title2->title = $option_title->title;
                                                                    $option_title2->save();
                                                                }
                                                            }
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
                                                    $model2 = Mage2StockStatus::model()->find("website_id = {$model->website_id} AND product_id = {$model->product_id}");
                                                    if (!$model2){
                                                        $model2 = new Mage2StockStatus();
                                                        $model2->product_id = $model->product_id;
                                                        $model2->website_id = $model->website_id;
                                                        $model2->stock_id = $model->stock_id;
                                                        $model2->qty = $model->qty;
                                                        $model2->stock_status = $model->stock_status;
                                                        if ($model2->save()){
                                                            //cataloginventory_stock_item
                                                            $stock_item = Mage1StockItem::model()->find("product_id = {$model->product_id} AND stock_id = {$model->stock_id}");
                                                            if ($stock_item){
                                                                $stock_item2 = Mage2StockItem::model()->find("product_id = {$model->product_id} AND website_id = {$website_id}");
                                                                if (!$stock_item2){
                                                                    $stock_item2 = new Mage2StockItem();
                                                                    $stock_item2->attributes = $stock_item->attributes;
                                                                    $stock_item2->website_id = $website_id;
                                                                    $stock_item2->save();
                                                                }
                                                            }
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
                                            $condition = "store_id = {$store_id2} AND entity_id = {$url->product_id} AND entity_type = 'product'";
                                            $url2 = Mage2UrlRewrite::model()->find($condition);
                                            if (!$url2) {
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
                                                    $condition = "url_rewrite_id = {$url->url_rewrite_id}";
                                                    $catalog_url2 = Mage2CatalogUrlRewriteProductCategory::model()->find($condition);
                                                    if (!$catalog_url2) {
                                                        $catalog_url2 = new Mage2CatalogUrlRewriteProductCategory();
                                                        $catalog_url2->url_rewrite_id = $url->url_rewrite_id;
                                                        $catalog_url2->category_id = $url->category_id;
                                                        $catalog_url2->product_id = $url->product_id;
                                                        $catalog_url2->save();
                                                    }
                                                }
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
                                    $condition = "product_id = {$model->product_id} AND website_id = {$model->website_id}";
                                    $model2 = Mage2CatalogProductWebsite::model()->find($condition);
                                    if (!$model2){
                                        $model2 = new Mage2CatalogProductWebsite();
                                        $model2->product_id = $model->product_id;
                                        $model2->website_id = $model->website_id;
                                        $model2->save();
                                    }
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
                                        $model2 = Mage2CatalogCategoryProduct::model()->find("category_id = {$model->category_id} AND product_id = {$model->product_id}");
                                        if (!$model2){
                                            $model2 = new Mage2CatalogCategoryProduct();
                                            $model2->category_id = $model->category_id;
                                            $model2->product_id = $model->product_id;
                                            $model2->position = $model->position;
                                            $model2->save();
                                        }
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
                                $condition = "link_id = {$model->link_id}";
                                $model2 = Mage2CatalogProductLink::model()->find($condition);
                                if (!$model2) {
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
                                                $condition = "value_id = {$item->value_id}";
                                                $item2 = Mage2CatalogProductLinkAttributeDecimal::model()->find($condition);
                                                if (!$item2){
                                                    $item2 = new Mage2CatalogProductLinkAttributeDecimal();
                                                    $item2->value_id = $item->value_id;
                                                    $item2->product_link_attribute_id = MigrateSteps::getMage2ProductLinkAttrId($item->product_link_attribute_id);
                                                    $item2->link_id = $item->link_id;
                                                    $item2->value = $item->value;
                                                    $item2->save();
                                                }
                                            }
                                        }
                                        //catalog_product_link_attribute_int
                                        $condition = "link_id = {$model2->link_id}";
                                        $items = Mage1CatalogProductLinkAttributeInt::model()->findAll($condition);
                                        if ($items){
                                            foreach ($items as $item){
                                                $condition = "value_id = {$item->value_id}";
                                                $item2 = Mage2CatalogProductLinkAttributeInt::model()->find($condition);
                                                if (!$item2){
                                                    $item2 = new Mage2CatalogProductLinkAttributeInt();
                                                    $item2->value_id = $item->value_id;
                                                    $item2->product_link_attribute_id = MigrateSteps::getMage2ProductLinkAttrId($item->product_link_attribute_id);
                                                    $item2->link_id = $item->link_id;
                                                    $item2->value = $item->value;
                                                    $item2->save();
                                                }
                                            }
                                        }
                                        //catalog_product_link_attribute_varchar
                                        $condition = "link_id = {$model2->link_id}";
                                        $items = Mage1CatalogProductLinkAttributeVarchar::model()->findAll($condition);
                                        if ($items){
                                            foreach ($items as $item){
                                                $condition = "value_id = {$item->value_id}";
                                                $item2 = Mage2CatalogProductLinkAttributeVarchar::model()->find($condition);
                                                if (!$item2){
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
                                    //$condition = "product_id = {$model->product_id} AND parent_id = {$model->parent_id}";
                                    $condition = "link_id = {$model->link_id}";
                                    $model2 = Mage2CatalogProductSuperLink::model()->find($condition);
                                    if (!$model2) {
                                        $model2 = new Mage2CatalogProductSuperLink();
                                        $model2->link_id = $model->link_id;
                                        $model2->product_id = $model->product_id;
                                        $model2->parent_id = $model->parent_id;
                                        $model2->save();
                                    }
                                }
                            }

                            //catalog_product_relation
                            $condition = "parent_id IN ({$str_product_ids}) AND child_id IN ({$str_product_ids})";
                            $models = Mage1CatalogProductRelation::model()->findAll($condition);
                            if ($models){
                                foreach ($models as $model){
                                    $condition = "parent_id = {$model->parent_id} AND child_id = {$model->child_id}";
                                    $model2 = Mage2CatalogProductRelation::model()->find($condition);
                                    if (!$model2){
                                        $model2 = new Mage2CatalogProductRelation();
                                        $model2->parent_id = $model->parent_id;
                                        $model2->child_id = $model->child_id;
                                        $model2->save();
                                    }
                                }
                            }

                            //catalog_product_super_attribute
                            $condition = "product_id IN ({$str_product_ids})";
                            $models = Mage1CatalogProductSuperAttribute::model()->findAll($condition);
                            if ($models) {
                                foreach ($models as $model){
                                    $condition = "product_super_attribute_id = {$model->product_super_attribute_id}";
                                    $model2 = Mage2CatalogProductSuperAttribute::model()->find($condition);
                                    if (!$model2) {
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
                                                    $condition = "value_id = {$super_attribute_label->value_id}";
                                                    $super_attribute_label2 = Mage2CatalogProductSuperAttributeLabel::model()->find($condition);
                                                    if (!$super_attribute_label2) {
                                                        $super_attribute_label2 = new Mage2CatalogProductSuperAttributeLabel();
                                                        $super_attribute_label2->value_id = $super_attribute_label->value_id;
                                                        $super_attribute_label2->product_super_attribute_id = $super_attribute_label->product_super_attribute_id;
                                                        $super_attribute_label2->store_id = $store_id2;
                                                        $super_attribute_label2->use_default = $super_attribute_label->use_default;
                                                        $super_attribute_label2->value = $super_attribute_label->value;
                                                        $super_attribute_label2->save();
                                                    }
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
                                                    $condition = "value_id = {$super_attribute_pricing->value_id}";
                                                    $super_attribute_pricing2 = Mage2CatalogProductSuperAttributePricing::model()->find($condition);
                                                    if (!$super_attribute_pricing2) {
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
                                    $condition = "option_id = {$model->option_id}";
                                    $model2 = Mage2CatalogProductBundleOption::model()->find($condition);
                                    if (!$model2) {
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
                                                    $condition = "value_id = {$bundle_option_value->value_id}";
                                                    $bundle_option_value2 = Mage2CatalogProductBundleOptionValue::model()->find($condition);
                                                    if (!$bundle_option_value2){
                                                        $bundle_option_value2 = new Mage2CatalogProductBundleOptionValue();
                                                        $bundle_option_value2->value_id = $bundle_option_value->value_id;
                                                        $bundle_option_value2->option_id = $bundle_option_value->option_id;
                                                        $bundle_option_value2->store_id = MigrateSteps::getMage2StoreId($bundle_option_value->store_id);
                                                        $bundle_option_value2->title = $bundle_option_value->title;
                                                        $bundle_option_value2->save();
                                                    }
                                                }
                                            }
                                            //catalog_product_bundle_selection
                                            $condition = "option_id = {$model2->option_id} AND product_id IN ({$str_product_ids})";
                                            $bundle_selections = Mage1CatalogProductBundleSelection::model()->findAll($condition);
                                            if ($bundle_selections){
                                                foreach ($bundle_selections as $bundle_selection){
                                                    $condition = "selection_id = {$bundle_selection->selection_id}";
                                                    $bundle_selection2 = Mage2CatalogProductBundleSelection::model()->find($condition);
                                                    if (!$bundle_selection2){
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
                                                                        $condition = "selection_id = {$selection_price->selection_id} AND website_id = {$selection_price->website_id}";
                                                                        $selection_price2 = Mage2CatalogProductBundleSelectionPrice::model()->find($condition);
                                                                        if (!$selection_price2) {
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
                                    $condition = "link_id = {$model->link_id}";
                                    $model2 = Mage2DownloadableLink::model()->find($condition);
                                    if (!$model2){
                                        $model2 = new Mage2DownloadableLink();
                                        $model2->link_id = $model->link_id;
                                        $model2->product_id = $model->product_id;
                                        $model2->sort_order = $model->sort_order;
                                        $model2->number_of_downloads = $model->number_of_downloads;
                                        $model2->is_shareable = $model->is_shareable;
                                        $model2->link_url = $model->link_url;
                                        $model2->link_file = $model->link_file;
                                        $model2->link_type = $model->link_type;
                                        $model2->sample_url = $model->sample_url;
                                        $model2->sample_file = $model->sample_file;
                                        $model2->sample_type = $model2->sample_type;
                                        if ($model2->save()) {
                                            if ($migrated_website_ids){
                                                //downloadable_link_price
                                                $str_website_ids = implode(',', $migrated_website_ids);
                                                $condition = "link_id = {$model2->link_id} AND website_id IN ({$str_website_ids})";
                                                $link_prices = Mage1DownloadableLinkPrice::model()->findAll($condition);
                                                if ($link_prices){
                                                    foreach ($link_prices as $link_price){
                                                        $condition = "price_id = {$link_price->price_id}";
                                                        $link_price2 = Mage2DownloadableLinkPrice::model()->find($condition);
                                                        if (!$link_price2){
                                                            $link_price2 = new Mage2DownloadableLinkPrice();
                                                            $link_price2->price_id = $link_price->price_id;
                                                            $link_price2->link_id = $link_price->link_id;
                                                            $link_price2->website_id = $link_price->website_id;
                                                            $link_price2->price = $link_price->price;
                                                            $link_price2->save();
                                                        }
                                                    }
                                                }
                                                //downloadable_link_title
                                                if ($migrated_store_ids) {
                                                    $str_store_ids = implode(',', $migrated_store_ids);
                                                    $condition = "link_id = {$model2->link_id} AND store_id IN ({$str_store_ids})";
                                                    $link_titles = Mage1DownloadableLinkTitle::model()->findAll($condition);
                                                    if ($link_titles) {
                                                        foreach ($link_titles as $link_title){
                                                            $condition = "title_id = {$link_title->title_id}";
                                                            $link_title2 = Mage2DownloadableLinkTitle::model()->find($condition);
                                                            if (!$link_title2){
                                                                $link_title2 = new Mage2DownloadableLinkTitle();
                                                                $link_title2->title_id = $link_title->title_id;
                                                                $link_title2->link_id = $link_title->link_id;
                                                                $link_title2->store_id = MigrateSteps::getMage2StoreId($link_title->store_id);
                                                                $link_title2->title = $link_title->title;
                                                                $link_title2->save();
                                                            }
                                                        }
                                                    }
                                                }
                                                //downloadable_sample
                                                //downloadable_sample_title
                                            }
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
    public function actionStep5()
    {
        $step = MigrateSteps::model()->find("sorder = 5");
        $result = MigrateSteps::checkStep($step->sorder);
        if ($result['allowed']){

            $this->render("step{$step->sorder}", array('step' => $step));
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
