<?php

/**
 * This is the model class for table "catalog_product_entity_text".
 *
 * The followings are the available columns in table 'catalog_product_entity_text':
 * @property integer $value_id
 * @property string $entity_type_id
 * @property integer $attribute_id
 * @property integer $store_id
 * @property string $entity_id
 * @property string $value
 *
 * The followings are the available model relations:
 * @property CoreStore $store
 * @property EavAttribute $attribute
 * @property CatalogProductEntity $entity
 */
class Mage1CatalogProductEntityTextPeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_entity_text}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('attribute_id, store_id', 'numerical', 'integerOnly'=>true),
			array('entity_type_id, entity_id', 'length', 'max'=>10),
			array('value', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('value_id, entity_type_id, attribute_id, store_id, entity_id, value', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'value_id' => 'Value',
			'entity_type_id' => 'Entity Type',
			'attribute_id' => 'Attribute',
			'store_id' => 'Store',
			'entity_id' => 'Entity',
			'value' => 'Value',
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
		$criteria->compare('entity_type_id',$this->entity_type_id,true);
		$criteria->compare('attribute_id',$this->attribute_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('entity_id',$this->entity_id,true);
		$criteria->compare('value',$this->value,true);

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
	 * @return Mage1CatalogProductEntityTextPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
