<?php

/**
 * This is the model class for table "catalog_product_link_attribute".
 *
 * The followings are the available columns in table 'catalog_product_link_attribute':
 * @property integer $product_link_attribute_id
 * @property integer $link_type_id
 * @property string $product_link_attribute_code
 * @property string $data_type
 *
 * The followings are the available model relations:
 * @property CatalogProductLinkType $linkType
 * @property CatalogProductLinkAttributeDecimal[] $catalogProductLinkAttributeDecimals
 * @property CatalogProductLinkAttributeInt[] $catalogProductLinkAttributeInts
 * @property CatalogProductLinkAttributeVarchar[] $catalogProductLinkAttributeVarchars
 */
class Mage1CatalogProductLinkAttributePeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_link_attribute}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('link_type_id', 'numerical', 'integerOnly'=>true),
			array('product_link_attribute_code, data_type', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_link_attribute_id, link_type_id, product_link_attribute_code, data_type', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'product_link_attribute_id' => 'Product Link Attribute',
			'link_type_id' => 'Link Type',
			'product_link_attribute_code' => 'Product Link Attribute Code',
			'data_type' => 'Data Type',
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

		$criteria->compare('product_link_attribute_id',$this->product_link_attribute_id);
		$criteria->compare('link_type_id',$this->link_type_id);
		$criteria->compare('product_link_attribute_code',$this->product_link_attribute_code,true);
		$criteria->compare('data_type',$this->data_type,true);

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
	 * @return Mage1CatalogProductLinkAttributePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
