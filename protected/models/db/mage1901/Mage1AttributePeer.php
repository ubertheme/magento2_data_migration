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
 * @property CatalogProductIndexEav[] $catalogProductIndexEavs
 * @property CatalogProductIndexEavDecimal[] $catalogProductIndexEavDecimals
 * @property CustomerAddressEntityDatetime[] $customerAddressEntityDatetimes
 * @property CustomerAddressEntityDecimal[] $customerAddressEntityDecimals
 * @property CustomerAddressEntityInt[] $customerAddressEntityInts
 * @property CustomerAddressEntityText[] $customerAddressEntityTexts
 * @property CustomerAddressEntityVarchar[] $customerAddressEntityVarchars
 * @property CustomerEavAttribute $customerEavAttribute
 * @property CoreWebsite[] $coreWebsites
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
class Mage1AttributePeer extends Mage1ActiveRecord
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
		return Yii::app()->mage1;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Mage1AttributePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
