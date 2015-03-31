<?php

/**
 * This is the model class for table "store_website".
 *
 * The followings are the available columns in table 'store_website':
 * @property integer $website_id
 * @property string $code
 * @property string $name
 * @property integer $sort_order
 * @property integer $default_group_id
 * @property integer $is_default
 *
 * The followings are the available model relations:
 * @property CatalogProductBundlePriceIndex[] $catalogProductBundlePriceIndexes
 * @property CatalogProductBundleSelection[] $catalogProductBundleSelections
 * @property CatalogProductEntityGroupPrice[] $catalogProductEntityGroupPrices
 * @property CatalogProductEntityTierPrice[] $catalogProductEntityTierPrices
 * @property CatalogProductIndexGroupPrice[] $catalogProductIndexGroupPrices
 * @property CatalogProductIndexPrice[] $catalogProductIndexPrices
 * @property CatalogProductIndexTierPrice[] $catalogProductIndexTierPrices
 * @property CatalogProductIndexWebsite $catalogProductIndexWebsite
 * @property CatalogProductSuperAttributePricing[] $catalogProductSuperAttributePricings
 * @property CatalogProductEntity[] $catalogProductEntities
 * @property CatalogruleGroupWebsite[] $catalogruleGroupWebsites
 * @property Catalogrule[] $catalogrules
 * @property EavAttribute[] $eavAttributes
 * @property CustomerEntity[] $customerEntities
 * @property DownloadableLinkPrice[] $downloadableLinkPrices
 * @property PersistentSession[] $persistentSessions
 * @property ProductAlertPrice[] $productAlertPrices
 * @property ProductAlertStock[] $productAlertStocks
 * @property SalesruleProductAttribute[] $salesruleProductAttributes
 * @property Salesrule[] $salesrules
 * @property Store[] $stores
 * @property StoreGroup[] $storeGroups
 * @property WeeeTax[] $weeeTaxes
 */
class Mage2WebsitePeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{store_website}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sort_order, default_group_id, is_default', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>32),
			array('name', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('website_id, code, name, sort_order, default_group_id, is_default', 'safe', 'on'=>'search'),
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
			'catalogProductBundlePriceIndexes' => array(self::HAS_MANY, 'CatalogProductBundlePriceIndex', 'website_id'),
			'catalogProductBundleSelections' => array(self::MANY_MANY, 'CatalogProductBundleSelection', 'catalog_product_bundle_selection_price(website_id, selection_id)'),
			'catalogProductEntityGroupPrices' => array(self::HAS_MANY, 'CatalogProductEntityGroupPrice', 'website_id'),
			'catalogProductEntityTierPrices' => array(self::HAS_MANY, 'CatalogProductEntityTierPrice', 'website_id'),
			'catalogProductIndexGroupPrices' => array(self::HAS_MANY, 'CatalogProductIndexGroupPrice', 'website_id'),
			'catalogProductIndexPrices' => array(self::HAS_MANY, 'CatalogProductIndexPrice', 'website_id'),
			'catalogProductIndexTierPrices' => array(self::HAS_MANY, 'CatalogProductIndexTierPrice', 'website_id'),
			'catalogProductIndexWebsite' => array(self::HAS_ONE, 'CatalogProductIndexWebsite', 'website_id'),
			'catalogProductSuperAttributePricings' => array(self::HAS_MANY, 'CatalogProductSuperAttributePricing', 'website_id'),
			'catalogProductEntities' => array(self::MANY_MANY, 'CatalogProductEntity', 'catalog_product_website(website_id, product_id)'),
			'catalogruleGroupWebsites' => array(self::HAS_MANY, 'CatalogruleGroupWebsite', 'website_id'),
			'catalogrules' => array(self::MANY_MANY, 'Catalogrule', 'catalogrule_website(website_id, rule_id)'),
			'eavAttributes' => array(self::MANY_MANY, 'EavAttribute', 'customer_eav_attribute_website(website_id, attribute_id)'),
			'customerEntities' => array(self::HAS_MANY, 'CustomerEntity', 'website_id'),
			'downloadableLinkPrices' => array(self::HAS_MANY, 'DownloadableLinkPrice', 'website_id'),
			'persistentSessions' => array(self::HAS_MANY, 'PersistentSession', 'website_id'),
			'productAlertPrices' => array(self::HAS_MANY, 'ProductAlertPrice', 'website_id'),
			'productAlertStocks' => array(self::HAS_MANY, 'ProductAlertStock', 'website_id'),
			'salesruleProductAttributes' => array(self::HAS_MANY, 'SalesruleProductAttribute', 'website_id'),
			'salesrules' => array(self::MANY_MANY, 'Salesrule', 'salesrule_website(website_id, rule_id)'),
			'stores' => array(self::HAS_MANY, 'Store', 'website_id'),
			'storeGroups' => array(self::HAS_MANY, 'StoreGroup', 'website_id'),
			'weeeTaxes' => array(self::HAS_MANY, 'WeeeTax', 'website_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'website_id' => 'Website',
			'code' => 'Code',
			'name' => 'Name',
			'sort_order' => 'Sort Order',
			'default_group_id' => 'Default Group',
			'is_default' => 'Is Default',
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

		$criteria->compare('website_id',$this->website_id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('default_group_id',$this->default_group_id);
		$criteria->compare('is_default',$this->is_default);

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
	 * @return Mage2StoreWebsite the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
