<?php

/**
 * This is the model class for table "core_url_rewrite".
 *
 * The followings are the available columns in table 'core_url_rewrite':
 * @property string $url_rewrite_id
 * @property integer $store_id
 * @property string $id_path
 * @property string $request_path
 * @property string $target_path
 * @property integer $is_system
 * @property string $options
 * @property string $description
 * @property string $category_id
 * @property string $product_id
 *
 * The followings are the available model relations:
 * @property CatalogCategoryEntity $category
 * @property CatalogProductEntity $product
 * @property CoreStore $store
 */
class Mage1UrlRewritePeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{core_url_rewrite}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, is_system', 'numerical', 'integerOnly'=>true),
			array('id_path, request_path, target_path, options, description', 'length', 'max'=>255),
			array('category_id, product_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('url_rewrite_id, store_id, id_path, request_path, target_path, is_system, options, description, category_id, product_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'url_rewrite_id' => 'Url Rewrite',
			'store_id' => 'Store',
			'id_path' => 'Id Path',
			'request_path' => 'Request Path',
			'target_path' => 'Target Path',
			'is_system' => 'Is System',
			'options' => 'Options',
			'description' => 'Description',
			'category_id' => 'Category',
			'product_id' => 'Product',
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

		$criteria->compare('url_rewrite_id',$this->url_rewrite_id,true);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('id_path',$this->id_path,true);
		$criteria->compare('request_path',$this->request_path,true);
		$criteria->compare('target_path',$this->target_path,true);
		$criteria->compare('is_system',$this->is_system);
		$criteria->compare('options',$this->options,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('product_id',$this->product_id,true);

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
	 * @return Mage1UrlRewritePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
