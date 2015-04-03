<?php

/**
 * This is the model class for table "customer_entity".
 *
 * The followings are the available columns in table 'customer_entity':
 * @property string $entity_id
 * @property integer $website_id
 * @property string $email
 * @property integer $group_id
 * @property string $increment_id
 * @property integer $store_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $is_active
 * @property integer $disable_auto_group_change
 */
class Mage2CustomerEntityPeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{customer_entity}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_at, updated_at', 'required'),
			array('website_id, group_id, store_id, is_active, disable_auto_group_change', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>255),
			array('increment_id', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entity_id, website_id, email, group_id, increment_id, store_id, created_at, updated_at, is_active, disable_auto_group_change', 'safe', 'on'=>'search'),
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
			'website_id' => 'Website',
			'email' => 'Email',
			'group_id' => 'Group',
			'increment_id' => 'Increment',
			'store_id' => 'Store',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'is_active' => 'Is Active',
			'disable_auto_group_change' => 'Disable Auto Group Change',
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
		$criteria->compare('website_id',$this->website_id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('increment_id',$this->increment_id,true);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('disable_auto_group_change',$this->disable_auto_group_change);

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
	 * @return Mage2CustomerEntityPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
