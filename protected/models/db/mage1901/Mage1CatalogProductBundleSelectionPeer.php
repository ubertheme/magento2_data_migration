<?php

/**
 * This is the model class for table "catalog_product_bundle_selection".
 *
 * The followings are the available columns in table 'catalog_product_bundle_selection':
 * @property string $selection_id
 * @property string $option_id
 * @property string $parent_product_id
 * @property string $product_id
 * @property string $position
 * @property integer $is_default
 * @property integer $selection_price_type
 * @property string $selection_price_value
 * @property string $selection_qty
 * @property integer $selection_can_change_qty
 *
 * The followings are the available model relations:
 * @property CatalogProductBundleOption $option
 * @property CatalogProductEntity $product
 * @property CoreWebsite[] $coreWebsites
 */
class Mage1CatalogProductBundleSelectionPeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_bundle_selection}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('option_id, parent_product_id, product_id', 'required'),
			array('is_default, selection_price_type, selection_can_change_qty', 'numerical', 'integerOnly'=>true),
			array('option_id, parent_product_id, product_id, position', 'length', 'max'=>10),
			array('selection_price_value, selection_qty', 'length', 'max'=>12),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('selection_id, option_id, parent_product_id, product_id, position, is_default, selection_price_type, selection_price_value, selection_qty, selection_can_change_qty', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'selection_id' => 'Selection',
			'option_id' => 'Option',
			'parent_product_id' => 'Parent Product',
			'product_id' => 'Product',
			'position' => 'Position',
			'is_default' => 'Is Default',
			'selection_price_type' => 'Selection Price Type',
			'selection_price_value' => 'Selection Price Value',
			'selection_qty' => 'Selection Qty',
			'selection_can_change_qty' => 'Selection Can Change Qty',
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

		$criteria->compare('selection_id',$this->selection_id,true);
		$criteria->compare('option_id',$this->option_id,true);
		$criteria->compare('parent_product_id',$this->parent_product_id,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('is_default',$this->is_default);
		$criteria->compare('selection_price_type',$this->selection_price_type);
		$criteria->compare('selection_price_value',$this->selection_price_value,true);
		$criteria->compare('selection_qty',$this->selection_qty,true);
		$criteria->compare('selection_can_change_qty',$this->selection_can_change_qty);

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
	 * @return Mage1CatalogProductBundleSelectionPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
