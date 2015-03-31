<?php

/**
 * This is the model class for table "eav_attribute".
 *
 * The followings are the available columns in table 'eav_attribute':
 * @property integer $attribute_id
 * @property integer $entity_type_id
 * @property string $attribute_code
 * @property string $attribute_model
 * @property string $backend_model
 * @property string $backend_type
 * @property string $backend_table
 * @property string $frontend_model
 * @property string $frontend_input
 * @property string $frontend_label
 * @property string $frontend_class
 * @property string $source_model
 * @property integer $is_required
 * @property integer $is_user_defined
 * @property string $default_value
 * @property integer $is_unique
 * @property string $note
 *
 * The followings are the available model relations:
 * @property CatalogCategoryEntityDatetime[] $catalogCategoryEntityDatetimes
 * @property CatalogCategoryEntityDecimal[] $catalogCategoryEntityDecimals
 * @property CatalogCategoryEntityInt[] $catalogCategoryEntityInts
 * @property CatalogCategoryEntityText[] $catalogCategoryEntityTexts
 * @property CatalogCategoryEntityVarchar[] $catalogCategoryEntityVarchars
 * @property CatalogEavAttribute $catalogEavAttribute
 * @property CatalogProductEntityDatetime[] $catalogProductEntityDatetimes
 * @property CatalogProductEntityDecimal[] $catalogProductEntityDecimals
 * @property CatalogProductEntityGallery[] $catalogProductEntityGalleries
 * @property CatalogProductEntityInt[] $catalogProductEntityInts
 * @property CatalogProductEntityMediaGallery[] $catalogProductEntityMediaGalleries
 * @property CatalogProductEntityText[] $catalogProductEntityTexts
 * @property CatalogProductEntityVarchar[] $catalogProductEntityVarchars
 * @property CustomerAddressEntityDatetime[] $customerAddressEntityDatetimes
 * @property CustomerAddressEntityDecimal[] $customerAddressEntityDecimals
 * @property CustomerAddressEntityInt[] $customerAddressEntityInts
 * @property CustomerAddressEntityText[] $customerAddressEntityTexts
 * @property CustomerAddressEntityVarchar[] $customerAddressEntityVarchars
 * @property CustomerEavAttribute $customerEavAttribute
 * @property StoreWebsite[] $storeWebsites
 * @property CustomerEntityDatetime[] $customerEntityDatetimes
 * @property CustomerEntityDecimal[] $customerEntityDecimals
 * @property CustomerEntityInt[] $customerEntityInts
 * @property CustomerEntityText[] $customerEntityTexts
 * @property CustomerEntityVarchar[] $customerEntityVarchars
 * @property CustomerFormAttribute[] $customerFormAttributes
 * @property EavEntityType $entityType
 * @property EavAttributeLabel[] $eavAttributeLabels
 * @property EavAttributeOption[] $eavAttributeOptions
 * @property EavEntityAttribute[] $eavEntityAttributes
 * @property EavFormElement[] $eavFormElements
 * @property GoogleshoppingAttributes[] $googleshoppingAttributes
 * @property SalesruleProductAttribute[] $salesruleProductAttributes
 * @property WeeeTax[] $weeeTaxes
 */
class Mage2AttributePeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{eav_attribute}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('entity_type_id, is_required, is_user_defined, is_unique', 'numerical', 'integerOnly'=>true),
			array('attribute_code, attribute_model, backend_model, backend_table, frontend_model, frontend_label, frontend_class, source_model, note', 'length', 'max'=>255),
			array('backend_type', 'length', 'max'=>8),
			array('frontend_input', 'length', 'max'=>50),
			array('default_value', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('attribute_id, entity_type_id, attribute_code, attribute_model, backend_model, backend_type, backend_table, frontend_model, frontend_input, frontend_label, frontend_class, source_model, is_required, is_user_defined, default_value, is_unique, note', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'catalogCategoryEntityDatetimes' => array(self::HAS_MANY, 'CatalogCategoryEntityDatetime', 'attribute_id'),
			'catalogCategoryEntityDecimals' => array(self::HAS_MANY, 'CatalogCategoryEntityDecimal', 'attribute_id'),
			'catalogCategoryEntityInts' => array(self::HAS_MANY, 'CatalogCategoryEntityInt', 'attribute_id'),
			'catalogCategoryEntityTexts' => array(self::HAS_MANY, 'CatalogCategoryEntityText', 'attribute_id'),
			'catalogCategoryEntityVarchars' => array(self::HAS_MANY, 'CatalogCategoryEntityVarchar', 'attribute_id'),
			'catalogEavAttribute' => array(self::HAS_ONE, 'CatalogEavAttribute', 'attribute_id'),
			'catalogProductEntityDatetimes' => array(self::HAS_MANY, 'CatalogProductEntityDatetime', 'attribute_id'),
			'catalogProductEntityDecimals' => array(self::HAS_MANY, 'CatalogProductEntityDecimal', 'attribute_id'),
			'catalogProductEntityGalleries' => array(self::HAS_MANY, 'CatalogProductEntityGallery', 'attribute_id'),
			'catalogProductEntityInts' => array(self::HAS_MANY, 'CatalogProductEntityInt', 'attribute_id'),
			'catalogProductEntityMediaGalleries' => array(self::HAS_MANY, 'CatalogProductEntityMediaGallery', 'attribute_id'),
			'catalogProductEntityTexts' => array(self::HAS_MANY, 'CatalogProductEntityText', 'attribute_id'),
			'catalogProductEntityVarchars' => array(self::HAS_MANY, 'CatalogProductEntityVarchar', 'attribute_id'),
			'customerAddressEntityDatetimes' => array(self::HAS_MANY, 'CustomerAddressEntityDatetime', 'attribute_id'),
			'customerAddressEntityDecimals' => array(self::HAS_MANY, 'CustomerAddressEntityDecimal', 'attribute_id'),
			'customerAddressEntityInts' => array(self::HAS_MANY, 'CustomerAddressEntityInt', 'attribute_id'),
			'customerAddressEntityTexts' => array(self::HAS_MANY, 'CustomerAddressEntityText', 'attribute_id'),
			'customerAddressEntityVarchars' => array(self::HAS_MANY, 'CustomerAddressEntityVarchar', 'attribute_id'),
			'customerEavAttribute' => array(self::HAS_ONE, 'CustomerEavAttribute', 'attribute_id'),
			'storeWebsites' => array(self::MANY_MANY, 'StoreWebsite', 'customer_eav_attribute_website(attribute_id, website_id)'),
			'customerEntityDatetimes' => array(self::HAS_MANY, 'CustomerEntityDatetime', 'attribute_id'),
			'customerEntityDecimals' => array(self::HAS_MANY, 'CustomerEntityDecimal', 'attribute_id'),
			'customerEntityInts' => array(self::HAS_MANY, 'CustomerEntityInt', 'attribute_id'),
			'customerEntityTexts' => array(self::HAS_MANY, 'CustomerEntityText', 'attribute_id'),
			'customerEntityVarchars' => array(self::HAS_MANY, 'CustomerEntityVarchar', 'attribute_id'),
			'customerFormAttributes' => array(self::HAS_MANY, 'CustomerFormAttribute', 'attribute_id'),
			'entityType' => array(self::BELONGS_TO, 'EavEntityType', 'entity_type_id'),
			'eavAttributeLabels' => array(self::HAS_MANY, 'EavAttributeLabel', 'attribute_id'),
			'eavAttributeOptions' => array(self::HAS_MANY, 'EavAttributeOption', 'attribute_id'),
			'eavEntityAttributes' => array(self::HAS_MANY, 'EavEntityAttribute', 'attribute_id'),
			'eavFormElements' => array(self::HAS_MANY, 'EavFormElement', 'attribute_id'),
			'googleshoppingAttributes' => array(self::HAS_MANY, 'GoogleshoppingAttributes', 'attribute_id'),
			'salesruleProductAttributes' => array(self::HAS_MANY, 'SalesruleProductAttribute', 'attribute_id'),
			'weeeTaxes' => array(self::HAS_MANY, 'WeeeTax', 'attribute_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'attribute_id' => 'Attribute',
			'entity_type_id' => 'Entity Type',
			'attribute_code' => 'Attribute Code',
			'attribute_model' => 'Attribute Model',
			'backend_model' => 'Backend Model',
			'backend_type' => 'Backend Type',
			'backend_table' => 'Backend Table',
			'frontend_model' => 'Frontend Model',
			'frontend_input' => 'Frontend Input',
			'frontend_label' => 'Frontend Label',
			'frontend_class' => 'Frontend Class',
			'source_model' => 'Source Model',
			'is_required' => 'Is Required',
			'is_user_defined' => 'Is User Defined',
			'default_value' => 'Default Value',
			'is_unique' => 'Is Unique',
			'note' => 'Note',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('attribute_id',$this->attribute_id);
		$criteria->compare('entity_type_id',$this->entity_type_id);
		$criteria->compare('attribute_code',$this->attribute_code,true);
		$criteria->compare('attribute_model',$this->attribute_model,true);
		$criteria->compare('backend_model',$this->backend_model,true);
		$criteria->compare('backend_type',$this->backend_type,true);
		$criteria->compare('backend_table',$this->backend_table,true);
		$criteria->compare('frontend_model',$this->frontend_model,true);
		$criteria->compare('frontend_input',$this->frontend_input,true);
		$criteria->compare('frontend_label',$this->frontend_label,true);
		$criteria->compare('frontend_class',$this->frontend_class,true);
		$criteria->compare('source_model',$this->source_model,true);
		$criteria->compare('is_required',$this->is_required);
		$criteria->compare('is_user_defined',$this->is_user_defined);
		$criteria->compare('default_value',$this->default_value,true);
		$criteria->compare('is_unique',$this->is_unique);
		$criteria->compare('note',$this->note,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->mage2;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Mage2AttributePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
