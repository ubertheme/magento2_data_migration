<?php

/**
 * This is the model class for table "eav_attribute_group".
 *
 * The followings are the available columns in table 'eav_attribute_group':
 * @property integer $attribute_group_id
 * @property integer $attribute_set_id
 * @property string $attribute_group_name
 * @property integer $sort_order
 * @property integer $default_id
 *
 * The followings are the available model relations:
 * @property EavAttributeSet $attributeSet
 * @property EavEntityAttribute[] $eavEntityAttributes
 */
class Mage1AttributeGroupPeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{eav_attribute_group}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('attribute_set_id, sort_order, default_id', 'numerical', 'integerOnly'=>true),
			array('attribute_group_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('attribute_group_id, attribute_set_id, attribute_group_name, sort_order, default_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'attribute_group_id' => 'Attribute Group',
			'attribute_set_id' => 'Attribute Set',
			'attribute_group_name' => 'Attribute Group Name',
			'sort_order' => 'Sort Order',
			'default_id' => 'Default',
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

		$criteria->compare('attribute_group_id',$this->attribute_group_id);
		$criteria->compare('attribute_set_id',$this->attribute_set_id);
		$criteria->compare('attribute_group_name',$this->attribute_group_name,true);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('default_id',$this->default_id);

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
	 * @return Mage1AttributeGroupPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
