<?php

/**
 * This is the model class for table "customer_eav_attribute".
 *
 * The followings are the available columns in table 'customer_eav_attribute':
 * @property integer $attribute_id
 * @property integer $is_visible
 * @property string $input_filter
 * @property integer $multiline_count
 * @property string $validate_rules
 * @property integer $is_system
 * @property string $sort_order
 * @property string $data_model
 * @property string $is_used_for_customer_segment
 */
class Mage1CustomerEavAttributePeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{customer_eav_attribute}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('attribute_id', 'required'),
			array('attribute_id, is_visible, multiline_count, is_system', 'numerical', 'integerOnly'=>true),
			array('input_filter, data_model', 'length', 'max'=>255),
			array('sort_order, is_used_for_customer_segment', 'length', 'max'=>10),
			array('validate_rules', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('attribute_id, is_visible, input_filter, multiline_count, validate_rules, is_system, sort_order, data_model, is_used_for_customer_segment', 'safe', 'on'=>'search'),
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
			'is_visible' => 'Is Visible',
			'input_filter' => 'Input Filter',
			'multiline_count' => 'Multiline Count',
			'validate_rules' => 'Validate Rules',
			'is_system' => 'Is System',
			'sort_order' => 'Sort Order',
			'data_model' => 'Data Model',
			'is_used_for_customer_segment' => 'Is Used For Customer Segment',
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
		$criteria->compare('is_visible',$this->is_visible);
		$criteria->compare('input_filter',$this->input_filter,true);
		$criteria->compare('multiline_count',$this->multiline_count);
		$criteria->compare('validate_rules',$this->validate_rules,true);
		$criteria->compare('is_system',$this->is_system);
		$criteria->compare('sort_order',$this->sort_order,true);
		$criteria->compare('data_model',$this->data_model,true);
		$criteria->compare('is_used_for_customer_segment',$this->is_used_for_customer_segment,true);

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
	 * @return Mage1CustomerEavAttributePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
