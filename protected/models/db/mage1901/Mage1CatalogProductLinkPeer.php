<?php

/**
 * This is the model class for table "catalog_product_link".
 *
 * The followings are the available columns in table 'catalog_product_link':
 * @property string $link_id
 * @property string $product_id
 * @property string $linked_product_id
 * @property integer $link_type_id
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $linkedProduct
 * @property CatalogProductLinkType $linkType
 * @property CatalogProductEntity $product
 * @property CatalogProductLinkAttributeDecimal[] $catalogProductLinkAttributeDecimals
 * @property CatalogProductLinkAttributeInt[] $catalogProductLinkAttributeInts
 * @property CatalogProductLinkAttributeVarchar[] $catalogProductLinkAttributeVarchars
 */
class Mage1CatalogProductLinkPeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_link}}';
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
			array('product_id, linked_product_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('link_id, product_id, linked_product_id, link_type_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'link_id' => 'Link',
			'product_id' => 'Product',
			'linked_product_id' => 'Linked Product',
			'link_type_id' => 'Link Type',
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

		$criteria->compare('link_id',$this->link_id,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('linked_product_id',$this->linked_product_id,true);
		$criteria->compare('link_type_id',$this->link_type_id);

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
	 * @return Mage1CatalogProductLinkPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
