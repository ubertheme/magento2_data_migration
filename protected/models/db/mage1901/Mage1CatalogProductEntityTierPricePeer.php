<?php

/**
 * This is the model class for table "catalog_product_entity_tier_price".
 *
 * The followings are the available columns in table 'catalog_product_entity_tier_price':
 * @property integer $value_id
 * @property string $entity_id
 * @property integer $all_groups
 * @property integer $customer_group_id
 * @property string $qty
 * @property string $value
 * @property integer $website_id
 *
 * The followings are the available model relations:
 * @property CustomerGroup $customerGroup
 * @property CatalogProductEntity $entity
 * @property CoreWebsite $website
 */
class Mage1CatalogProductEntityTierPricePeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_entity_tier_price}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('website_id', 'required'),
			array('all_groups, customer_group_id, website_id', 'numerical', 'integerOnly'=>true),
			array('entity_id', 'length', 'max'=>10),
			array('qty, value', 'length', 'max'=>12),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('value_id, entity_id, all_groups, customer_group_id, qty, value, website_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'value_id' => 'Value',
			'entity_id' => 'Entity',
			'all_groups' => 'All Groups',
			'customer_group_id' => 'Customer Group',
			'qty' => 'Qty',
			'value' => 'Value',
			'website_id' => 'Website',
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

		$criteria->compare('value_id',$this->value_id);
		$criteria->compare('entity_id',$this->entity_id,true);
		$criteria->compare('all_groups',$this->all_groups);
		$criteria->compare('customer_group_id',$this->customer_group_id);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('website_id',$this->website_id);

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
	 * @return Mage1CatalogProductEntityTierPricePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
