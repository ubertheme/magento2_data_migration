<?php

/**
 * This is the model class for table "catalog_product_option_type_value".
 *
 * The followings are the available columns in table 'catalog_product_option_type_value':
 * @property string $option_type_id
 * @property string $option_id
 * @property string $sku
 * @property string $sort_order
 *
 * The followings are the available model relations:
 * @property CatalogProductOptionTypePrice[] $catalogProductOptionTypePrices
 * @property CatalogProductOptionTypeTitle[] $catalogProductOptionTypeTitles
 * @property CatalogProductOption $option
 */
class Mage2CatalogProductOptionTypeValuePeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_option_type_value}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('option_id, sort_order', 'length', 'max'=>10),
			array('sku', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('option_type_id, option_id, sku, sort_order', 'safe', 'on'=>'search'),
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
			'catalogProductOptionTypePrices' => array(self::HAS_MANY, 'CatalogProductOptionTypePrice', 'option_type_id'),
			'catalogProductOptionTypeTitles' => array(self::HAS_MANY, 'CatalogProductOptionTypeTitle', 'option_type_id'),
			'option' => array(self::BELONGS_TO, 'CatalogProductOption', 'option_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'option_type_id' => 'Option Type',
			'option_id' => 'Option',
			'sku' => 'Sku',
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

		$criteria->compare('option_type_id',$this->option_type_id,true);
		$criteria->compare('option_id',$this->option_id,true);
		$criteria->compare('sku',$this->sku,true);
		$criteria->compare('sort_order',$this->sort_order,true);

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
	 * @return Mage2CatalogProductOptionTypeValuePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
