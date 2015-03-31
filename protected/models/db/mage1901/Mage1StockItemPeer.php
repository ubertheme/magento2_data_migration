<?php

/**
 * This is the model class for table "cataloginventory_stock_item".
 *
 * The followings are the available columns in table 'cataloginventory_stock_item':
 * @property string $item_id
 * @property string $product_id
 * @property integer $stock_id
 * @property string $qty
 * @property string $min_qty
 * @property integer $use_config_min_qty
 * @property integer $is_qty_decimal
 * @property integer $backorders
 * @property integer $use_config_backorders
 * @property string $min_sale_qty
 * @property integer $use_config_min_sale_qty
 * @property string $max_sale_qty
 * @property integer $use_config_max_sale_qty
 * @property integer $is_in_stock
 * @property string $low_stock_date
 * @property string $notify_stock_qty
 * @property integer $use_config_notify_stock_qty
 * @property integer $manage_stock
 * @property integer $use_config_manage_stock
 * @property integer $stock_status_changed_auto
 * @property integer $use_config_qty_increments
 * @property string $qty_increments
 * @property integer $use_config_enable_qty_inc
 * @property integer $enable_qty_increments
 * @property integer $is_decimal_divided
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $product
 * @property CataloginventoryStock $stock
 */
class Mage1StockItemPeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{cataloginventory_stock_item}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('stock_id, use_config_min_qty, is_qty_decimal, backorders, use_config_backorders, use_config_min_sale_qty, use_config_max_sale_qty, is_in_stock, use_config_notify_stock_qty, manage_stock, use_config_manage_stock, stock_status_changed_auto, use_config_qty_increments, use_config_enable_qty_inc, enable_qty_increments, is_decimal_divided', 'numerical', 'integerOnly'=>true),
			array('product_id', 'length', 'max'=>10),
			array('qty, min_qty, min_sale_qty, max_sale_qty, notify_stock_qty, qty_increments', 'length', 'max'=>12),
			array('low_stock_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('item_id, product_id, stock_id, qty, min_qty, use_config_min_qty, is_qty_decimal, backorders, use_config_backorders, min_sale_qty, use_config_min_sale_qty, max_sale_qty, use_config_max_sale_qty, is_in_stock, low_stock_date, notify_stock_qty, use_config_notify_stock_qty, manage_stock, use_config_manage_stock, stock_status_changed_auto, use_config_qty_increments, qty_increments, use_config_enable_qty_inc, enable_qty_increments, is_decimal_divided', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'item_id' => 'Item',
			'product_id' => 'Product',
			'stock_id' => 'Stock',
			'qty' => 'Qty',
			'min_qty' => 'Min Qty',
			'use_config_min_qty' => 'Use Config Min Qty',
			'is_qty_decimal' => 'Is Qty Decimal',
			'backorders' => 'Backorders',
			'use_config_backorders' => 'Use Config Backorders',
			'min_sale_qty' => 'Min Sale Qty',
			'use_config_min_sale_qty' => 'Use Config Min Sale Qty',
			'max_sale_qty' => 'Max Sale Qty',
			'use_config_max_sale_qty' => 'Use Config Max Sale Qty',
			'is_in_stock' => 'Is In Stock',
			'low_stock_date' => 'Low Stock Date',
			'notify_stock_qty' => 'Notify Stock Qty',
			'use_config_notify_stock_qty' => 'Use Config Notify Stock Qty',
			'manage_stock' => 'Manage Stock',
			'use_config_manage_stock' => 'Use Config Manage Stock',
			'stock_status_changed_auto' => 'Stock Status Changed Auto',
			'use_config_qty_increments' => 'Use Config Qty Increments',
			'qty_increments' => 'Qty Increments',
			'use_config_enable_qty_inc' => 'Use Config Enable Qty Inc',
			'enable_qty_increments' => 'Enable Qty Increments',
			'is_decimal_divided' => 'Is Decimal Divided',
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

		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('stock_id',$this->stock_id);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('min_qty',$this->min_qty,true);
		$criteria->compare('use_config_min_qty',$this->use_config_min_qty);
		$criteria->compare('is_qty_decimal',$this->is_qty_decimal);
		$criteria->compare('backorders',$this->backorders);
		$criteria->compare('use_config_backorders',$this->use_config_backorders);
		$criteria->compare('min_sale_qty',$this->min_sale_qty,true);
		$criteria->compare('use_config_min_sale_qty',$this->use_config_min_sale_qty);
		$criteria->compare('max_sale_qty',$this->max_sale_qty,true);
		$criteria->compare('use_config_max_sale_qty',$this->use_config_max_sale_qty);
		$criteria->compare('is_in_stock',$this->is_in_stock);
		$criteria->compare('low_stock_date',$this->low_stock_date,true);
		$criteria->compare('notify_stock_qty',$this->notify_stock_qty,true);
		$criteria->compare('use_config_notify_stock_qty',$this->use_config_notify_stock_qty);
		$criteria->compare('manage_stock',$this->manage_stock);
		$criteria->compare('use_config_manage_stock',$this->use_config_manage_stock);
		$criteria->compare('stock_status_changed_auto',$this->stock_status_changed_auto);
		$criteria->compare('use_config_qty_increments',$this->use_config_qty_increments);
		$criteria->compare('qty_increments',$this->qty_increments,true);
		$criteria->compare('use_config_enable_qty_inc',$this->use_config_enable_qty_inc);
		$criteria->compare('enable_qty_increments',$this->enable_qty_increments);
		$criteria->compare('is_decimal_divided',$this->is_decimal_divided);

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
	 * @return Mage1StockItemPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
