<?php

/**
 * This is the model class for table "customer_address_entity".
 *
 * The followings are the available columns in table 'customer_address_entity':
 * @property string $entity_id
 * @property integer $entity_type_id
 * @property integer $attribute_set_id
 * @property string $increment_id
 * @property string $parent_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $is_active
 */
class Mage1CustomerAddressEntityPeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{customer_address_entity}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_at', 'required'),
			array('entity_type_id, attribute_set_id, is_active', 'numerical', 'integerOnly'=>true),
			array('increment_id', 'length', 'max'=>50),
			array('parent_id', 'length', 'max'=>10),
			array('updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entity_id, entity_type_id, attribute_set_id, increment_id, parent_id, created_at, updated_at, is_active', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'entity_id' => 'Entity',
			'entity_type_id' => 'Entity Type',
			'attribute_set_id' => 'Attribute Set',
			'increment_id' => 'Increment',
			'parent_id' => 'Parent',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'is_active' => 'Is Active',
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

		$criteria->compare('entity_id',$this->entity_id,true);
		$criteria->compare('entity_type_id',$this->entity_type_id);
		$criteria->compare('attribute_set_id',$this->attribute_set_id);
		$criteria->compare('increment_id',$this->increment_id,true);
		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('is_active',$this->is_active);

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
	 * @return Mage1CustomerAddressEntityPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
