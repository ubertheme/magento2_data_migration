<?php

/**
 * This is the model class for table "customer_eav_attribute_website".
 *
 * The followings are the available columns in table 'customer_eav_attribute_website':
 * @property integer $attribute_id
 * @property integer $website_id
 * @property integer $is_visible
 * @property integer $is_required
 * @property string $default_value
 * @property integer $multiline_count
 */
class Mage2CustomerEavAttributeWebsitePeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{customer_eav_attribute_website}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('attribute_id, website_id', 'required'),
			array('attribute_id, website_id, is_visible, is_required, multiline_count', 'numerical', 'integerOnly'=>true),
			array('default_value', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('attribute_id, website_id, is_visible, is_required, default_value, multiline_count', 'safe', 'on'=>'search'),
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
			'attribute_id' => 'Attribute',
			'website_id' => 'Website',
			'is_visible' => 'Is Visible',
			'is_required' => 'Is Required',
			'default_value' => 'Default Value',
			'multiline_count' => 'Multiline Count',
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

		$criteria->compare('attribute_id',$this->attribute_id);
		$criteria->compare('website_id',$this->website_id);
		$criteria->compare('is_visible',$this->is_visible);
		$criteria->compare('is_required',$this->is_required);
		$criteria->compare('default_value',$this->default_value,true);
		$criteria->compare('multiline_count',$this->multiline_count);

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
	 * @return Mage2CustomerEavAttributeWebsitePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
