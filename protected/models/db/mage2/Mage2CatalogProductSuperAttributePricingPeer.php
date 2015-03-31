<?php

/**
 * This is the model class for table "catalog_product_super_attribute_pricing".
 *
 * The followings are the available columns in table 'catalog_product_super_attribute_pricing':
 * @property string $value_id
 * @property string $product_super_attribute_id
 * @property string $value_index
 * @property integer $is_percent
 * @property string $pricing_value
 * @property integer $website_id
 *
 * The followings are the available model relations:
 * @property StoreWebsite $website
 * @property CatalogProductSuperAttribute $productSuperAttribute
 */
class Mage2CatalogProductSuperAttributePricingPeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_super_attribute_pricing}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_percent, website_id', 'numerical', 'integerOnly'=>true),
			array('product_super_attribute_id', 'length', 'max'=>10),
			array('value_index', 'length', 'max'=>255),
			array('pricing_value', 'length', 'max'=>12),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('value_id, product_super_attribute_id, value_index, is_percent, pricing_value, website_id', 'safe', 'on'=>'search'),
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
			'website' => array(self::BELONGS_TO, 'StoreWebsite', 'website_id'),
			'productSuperAttribute' => array(self::BELONGS_TO, 'CatalogProductSuperAttribute', 'product_super_attribute_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'value_id' => 'Value',
			'product_super_attribute_id' => 'Product Super Attribute',
			'value_index' => 'Value Index',
			'is_percent' => 'Is Percent',
			'pricing_value' => 'Pricing Value',
			'website_id' => 'Website',
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

		$criteria->compare('value_id',$this->value_id,true);
		$criteria->compare('product_super_attribute_id',$this->product_super_attribute_id,true);
		$criteria->compare('value_index',$this->value_index,true);
		$criteria->compare('is_percent',$this->is_percent);
		$criteria->compare('pricing_value',$this->pricing_value,true);
		$criteria->compare('website_id',$this->website_id);

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
	 * @return Mage2CatalogProductSuperAttributePricingPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
