<?php

/**
 * This is the model class for table "core_store_group".
 *
 * The followings are the available columns in table 'core_store_group':
 * @property integer $group_id
 * @property integer $website_id
 * @property string $name
 * @property string $root_category_id
 * @property integer $default_store_id
 *
 * The followings are the available model relations:
 * @property CoreStore[] $coreStores
 * @property CoreWebsite $website
 */
class Mage1StoreGroupPeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{core_store_group}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('website_id, default_store_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('root_category_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('group_id, website_id, name, root_category_id, default_store_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'group_id' => 'Group',
			'website_id' => 'Website',
			'name' => 'Name',
			'root_category_id' => 'Root Category',
			'default_store_id' => 'Default Store',
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

		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('website_id',$this->website_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('root_category_id',$this->root_category_id,true);
		$criteria->compare('default_store_id',$this->default_store_id);

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
	 * @return Mage1StoreGroupPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
