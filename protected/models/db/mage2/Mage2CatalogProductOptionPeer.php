<?php

/**
 * This is the model class for table "catalog_product_option".
 *
 * The followings are the available columns in table 'catalog_product_option':
 * @property string $option_id
 * @property string $product_id
 * @property string $type
 * @property integer $is_require
 * @property string $sku
 * @property string $max_characters
 * @property string $file_extension
 * @property integer $image_size_x
 * @property integer $image_size_y
 * @property string $sort_order
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $product
 * @property CatalogProductOptionPrice[] $catalogProductOptionPrices
 * @property CatalogProductOptionTitle[] $catalogProductOptionTitles
 * @property CatalogProductOptionTypeValue[] $catalogProductOptionTypeValues
 */
class Mage2CatalogProductOptionPeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_option}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_require, image_size_x, image_size_y', 'numerical', 'integerOnly'=>true),
			array('product_id, max_characters, sort_order', 'length', 'max'=>10),
			array('type, file_extension', 'length', 'max'=>50),
			array('sku', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('option_id, product_id, type, is_require, sku, max_characters, file_extension, image_size_x, image_size_y, sort_order', 'safe', 'on'=>'search'),
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
			'product' => array(self::BELONGS_TO, 'CatalogProductEntity', 'product_id'),
			'catalogProductOptionPrices' => array(self::HAS_MANY, 'CatalogProductOptionPrice', 'option_id'),
			'catalogProductOptionTitles' => array(self::HAS_MANY, 'CatalogProductOptionTitle', 'option_id'),
			'catalogProductOptionTypeValues' => array(self::HAS_MANY, 'CatalogProductOptionTypeValue', 'option_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'option_id' => 'Option',
			'product_id' => 'Product',
			'type' => 'Type',
			'is_require' => 'Is Require',
			'sku' => 'Sku',
			'max_characters' => 'Max Characters',
			'file_extension' => 'File Extension',
			'image_size_x' => 'Image Size X',
			'image_size_y' => 'Image Size Y',
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

		$criteria->compare('option_id',$this->option_id,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('is_require',$this->is_require);
		$criteria->compare('sku',$this->sku,true);
		$criteria->compare('max_characters',$this->max_characters,true);
		$criteria->compare('file_extension',$this->file_extension,true);
		$criteria->compare('image_size_x',$this->image_size_x);
		$criteria->compare('image_size_y',$this->image_size_y);
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
	 * @return Mage2CatalogProductOptionPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
