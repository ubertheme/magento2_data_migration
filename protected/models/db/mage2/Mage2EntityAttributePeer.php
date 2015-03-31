<?php

/**
 * This is the model class for table "eav_entity_attribute".
 *
 * The followings are the available columns in table 'eav_entity_attribute':
 * @property string $entity_attribute_id
 * @property integer $entity_type_id
 * @property integer $attribute_set_id
 * @property integer $attribute_group_id
 * @property integer $attribute_id
 * @property integer $sort_order
 *
 * The followings are the available model relations:
 * @property EavAttribute $attribute
 * @property EavAttributeGroup $attributeGroup
 */
class Mage2EntityAttributePeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{eav_entity_attribute}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entity_attribute_id, entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order', 'safe', 'on'=>'search'),
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
			'attribute' => array(self::BELONGS_TO, 'EavAttribute', 'attribute_id'),
			'attributeGroup' => array(self::BELONGS_TO, 'EavAttributeGroup', 'attribute_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'entity_attribute_id' => 'Entity Attribute',
			'entity_type_id' => 'Entity Type',
			'attribute_set_id' => 'Attribute Set',
			'attribute_group_id' => 'Attribute Group',
			'attribute_id' => 'Attribute',
			'sort_order' => 'Sort Order',
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

		$criteria->compare('entity_attribute_id',$this->entity_attribute_id,true);
		$criteria->compare('entity_type_id',$this->entity_type_id);
		$criteria->compare('attribute_set_id',$this->attribute_set_id);
		$criteria->compare('attribute_group_id',$this->attribute_group_id);
		$criteria->compare('attribute_id',$this->attribute_id);
		$criteria->compare('sort_order',$this->sort_order);

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
	 * @return Mage2EntityAttributePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
