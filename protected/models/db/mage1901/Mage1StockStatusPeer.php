<?php

/**
 * This is the model class for table "cataloginventory_stock_status".
 *
 * The followings are the available columns in table 'cataloginventory_stock_status':
 * @property string $product_id
 * @property integer $website_id
 * @property integer $stock_id
 * @property string $qty
 * @property integer $stock_status
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $product
 * @property CataloginventoryStock $stock
 * @property CoreWebsite $website
 */
class Mage1StockStatusPeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{cataloginventory_stock_status}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, website_id, stock_id, stock_status', 'required'),
			array('website_id, stock_id, stock_status', 'numerical', 'integerOnly'=>true),
			array('product_id', 'length', 'max'=>10),
			array('qty', 'length', 'max'=>12),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, website_id, stock_id, qty, stock_status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'product_id' => 'Product',
			'website_id' => 'Website',
			'stock_id' => 'Stock',
			'qty' => 'Qty',
			'stock_status' => 'Stock Status',
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

		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('website_id',$this->website_id);
		$criteria->compare('stock_id',$this->stock_id);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('stock_status',$this->stock_status);

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
	 * @return Mage1CataloginventoryStockStatusPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
