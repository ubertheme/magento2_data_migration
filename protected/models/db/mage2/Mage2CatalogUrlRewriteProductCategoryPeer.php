<?php

/**
 * This is the model class for table "catalog_url_rewrite_product_category".
 *
 * The followings are the available columns in table 'catalog_url_rewrite_product_category':
 * @property string $url_rewrite_id
 * @property string $category_id
 * @property string $product_id
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $product
 * @property CatalogCategoryEntity $category
 * @property UrlRewrite $urlRewrite
 */
class Mage2CatalogUrlRewriteProductCategoryPeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_url_rewrite_product_category}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('url_rewrite_id, category_id, product_id', 'required'),
			array('url_rewrite_id, category_id, product_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('url_rewrite_id, category_id, product_id', 'safe', 'on'=>'search'),
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
			'category' => array(self::BELONGS_TO, 'CatalogCategoryEntity', 'category_id'),
			'urlRewrite' => array(self::BELONGS_TO, 'UrlRewrite', 'url_rewrite_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'url_rewrite_id' => 'Url Rewrite',
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
		return Yii::app()->mage2;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Mage2CatalogUrlRewriteProductCategoryPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
