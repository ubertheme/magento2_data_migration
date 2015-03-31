<?php

/**
 * This is the model class for table "{{migrate_steps}}".
 *
 * The followings are the available columns in table '{{migrate_steps}}':
 * @property string $id
 * @property string $title
 * @property string $code
 * @property integer $status
 * @property string $migrated_data
 * @property string $descriptions
 * @property integer $sorder
 */
class MigrateSteps extends MigrateStepsPeer
{
    const STATUS_DONE = 1;
    const STATUS_NOT_DONE = 0;
    const SQL_COMMAND_DELIMETER = ';';

    public static function getNextSteps(){
        $step = null;
        $criteria = new CDbCriteria();
        $criteria->select = "t.sorder";
        $criteria->order = "t.sorder ASC";
        $criteria->condition = "status = ". self::STATUS_NOT_DONE;
        $nextStep = MigrateSteps::model()->find($criteria);
        if ($nextStep){
            $step = "step{$nextStep->sorder}";
        } else {
            $step = "step1";
        }

        return $step;
    }

    public static function checkStep($currentStepIndex = null){
        $result = array(
            'allowed' => true
        );
        $criteria = new CDbCriteria();
        $criteria->select = "t.sorder";
        $criteria->order = "t.sorder ASC";
        $criteria->condition = "t.sorder < {$currentStepIndex} AND status = ". self::STATUS_NOT_DONE;
        $step = MigrateSteps::model()->find($criteria);
        if ($step){
            $result['allowed'] = false;
            $result['back_step'] = "step{$step->sorder}";
        }

        return $result;
    }

    public static function getMage2StoreId($mage1StoreId){
        $id = null;
        if (isset($mage1StoreId)){
            $store1 = Mage1Store::model()->find("store_id = {$mage1StoreId}");
            if ($store1){
                $store2 = Mage2Store::model()->find("code = '{$store1->code}'");
                if ($store2) {
                    $id = $store2->store_id;
                }
            }
        }

        return $id;
    }

    public static function getMage2AttributeId($mage1AttrId, $entityTypeId = 3){
        $id = null;
        if (isset($mage1AttrId)){
            $attr1 = Mage1Attribute::model()->find("entity_type_id = {$entityTypeId} AND attribute_id = {$mage1AttrId}");
            if ($attr1){
                //msrp_enabled was changed to msrp in magento2
                if ($attr1->attribute_code == 'msrp_enabled')
                    $attribute_code2 = 'msrp';
                else
                    $attribute_code2 = $attr1->attribute_code;
                $attr2 = Mage2Attribute::model()->find("entity_type_id = {$entityTypeId} AND attribute_code = '{$attribute_code2}'");
                if ($attr2) {
                    $id = $attr2->attribute_id;
                }
            }
        }

        return $id;
    }

    public static function getMage1AttributeCode($mage1AttrId){
        $code = null;
        if (isset($mage1AttrId)){
            $attr1 = Mage1Attribute::model()->find("attribute_id = {$mage1AttrId}");
            if ($attr1){
                $code = $attr1->attribute_code;
            }
        }
        return $code;
    }

    public static function getMage1AttributeId($mage1AttrCode, $entityTypeId = 3){
        $id = null;
        if (isset($mage1AttrCode)){
            $attr1 = Mage1Attribute::model()->find("entity_type_id = {$entityTypeId} AND attribute_code = '{$mage1AttrCode}'");
            if ($attr1){
                $id = $attr1->attribute_id;
            }
        }

        return $id;
    }

    public static function executeFile($filePath) {

        if (!isset($filePath)) return false;

        try {
            $tempLine = '';
            $lines = file($filePath);
            // Loop through each line
            foreach ($lines as $line)
            {
                // Skip it if it's a comment
                if (substr($line, 0, 2) == '--' || $line == '')
                    continue;
                // Add this line to the current segment
                $tempLine .= $line;
                // If it has a semicolon at the end, it's the end of the query
                if (substr(trim($line), -1, 1) == self::SQL_COMMAND_DELIMETER)
                {
                    // Perform the query
                    Yii::app()->mage2->createCommand($tempLine)->execute();

                    // Reset temp variable to empty
                    $tempLine = '';
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * Build tree categories function
     * @param int $parent_id
     * @return array
     */
    public static function getMage1CategoryTree($parent_id = 1) {
        $categories = array();
        $models = Mage1CatalogCategoryEntity::model()->findAll("parent_id = {$parent_id}");
        if ($models){
            foreach ($models as $model) {
                $category = array();
                $category['entity_id'] = $model->entity_id;
                $category['name'] = self::getMage1CategoryName($model->entity_id);
                $category['parent_id'] = $model->parent_id;
                $category['children'] = self::getMage1CategoryTree($category['entity_id']);
                $categories[$model->entity_id] = (object)$category;
            }
        }

        return $categories;
    }

    /**
     * get Category Name in Magento1
     * @param $category_id
     * @return null|string
     */
    public static function getMage1CategoryName($category_id){
        $attribute_id = 41;
        $name = null;
        $model = Mage1CatalogCategoryEntityVarchar::model()->find("entity_id = {$category_id} AND attribute_id = {$attribute_id}");
        if ($model){
            $name = $model->value;
        }

        return $name;
    }

    /**
     * Get Product Link Attribute Id in Magento2 database
     * @param $linkAttrId1 The Link Attribute Id in Magento1 database
     */
    public static function getMage2ProductLinkAttrId($linkAttrId1){
        $linkAttrId2 = null;
        $model1 = Mage1CatalogProductLinkAttribute::model()->findByPk($linkAttrId1);
        if ($model1){
            $condition = "link_type_id = {$model1->link_type_id} AND product_link_attribute_code = '{$model1->product_link_attribute_code}'";
            $model2 = Mage2CatalogProductLinkAttribute::model()->find($condition);
            if ($model2){
                $linkAttrId2 = $model2->product_link_attribute_id;
            }
        }

        return $linkAttrId2;
    }

    public static function getTotalProductsByType($type_id = 'simple'){
        $total = 0;
        if ($type_id){
            $migrated_store_ids = isset(Yii::app()->session['migrated_store_ids']) ? Yii::app()->session['migrated_store_ids'] : array();
            $str_store_ids = implode(',', $migrated_store_ids);
            $db = Yii::app()->mage1;
            $tablePrefix = $db->tablePrefix;

            $sql = "SELECT COUNT(DISTINCT  e.entity_id) AS total FROM {$tablePrefix}catalog_product_entity e";
            $sql .= " LEFT JOIN {$tablePrefix}catalog_product_entity_int ei ON e.entity_id = ei.entity_id";
            $sql .= " WHERE e.type_id = '{$type_id}' AND ei.store_id IN ({$str_store_ids})";
            $total = $db->createCommand($sql)->queryScalar();
        }

        return $total;
    }

    public static function getTotalVisibleProductsAttr(){
        $tablePrefix = Yii::app()->mage1->tablePrefix;

        $sql = "SELECT COUNT(*) FROM `{$tablePrefix}eav_attribute` e INNER JOIN `{$tablePrefix}catalog_eav_attribute` ce ON e.attribute_id = ce.attribute_id WHERE e.entity_type_id = 4 AND ce.is_visible = 1";
        $total = Yii::app()->mage1->createCommand($sql)->queryScalar();

        return $total;
    }
}