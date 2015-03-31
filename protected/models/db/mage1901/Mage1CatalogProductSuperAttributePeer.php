<?php

/**
 * This is the model class for table "catalog_product_super_attribute".
 *
 * The followings are the available columns in table 'catalog_product_super_attribute':
 * @property string $product_super_attribute_id
 * @property string $product_id
 * @property integer $attribute_id
 * @property integer $position
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $product
 * @property CatalogProductSuperAttributeLabel[] $catalogProductSuperAttributeLabels
 * @property CatalogProductSuperAttributePricing[] $catalogProductSuperAttributePricings
 */
class Mage1CatalogProductSuperAttributePeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_super_attribute}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('attribute_id, position', 'numerical', 'integerOnly'=>true),
			array('product_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_super_attribute_id, product_id, attribute_id, position', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'product_super_attribute_id' => 'Product Super Attribute',
			'product_id' => 'Product',
			'attribute_id' => 'Attribute',
			'position' => 'Position',
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

		$criteria->compare('product_super_attribute_id',$this->product_super_attribute_id,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('attribute_id',$this->attribute_id);
		$criteria->compare('position',$this->position);

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
	 * @return Mage1CatalogProductSuperAttributePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
