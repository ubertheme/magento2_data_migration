<?php

/**
 * This is the model class for table "catalog_product_entity".
 *
 * The followings are the available columns in table 'catalog_product_entity':
 * @property string $entity_id
 * @property integer $attribute_set_id
 * @property string $type_id
 * @property string $sku
 * @property integer $has_options
 * @property integer $required_options
 * @property string $created_at
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property CatalogCategoryEntity[] $catalogCategoryEntities
 * @property CatalogCompareItem[] $catalogCompareItems
 * @property CatalogProductBundleOption[] $catalogProductBundleOptions
 * @property CatalogProductBundlePriceIndex[] $catalogProductBundlePriceIndexes
 * @property CatalogProductBundleSelection[] $catalogProductBundleSelections
 * @property EavAttributeSet $attributeSet
 * @property CatalogProductEntityDatetime[] $catalogProductEntityDatetimes
 * @property CatalogProductEntityDecimal[] $catalogProductEntityDecimals
 * @property CatalogProductEntityGallery[] $catalogProductEntityGalleries
 * @property CatalogProductEntityGroupPrice[] $catalogProductEntityGroupPrices
 * @property CatalogProductEntityInt[] $catalogProductEntityInts
 * @property CatalogProductEntityMediaGallery[] $catalogProductEntityMediaGalleries
 * @property CatalogProductEntityText[] $catalogProductEntityTexts
 * @property CatalogProductEntityTierPrice[] $catalogProductEntityTierPrices
 * @property CatalogProductEntityVarchar[] $catalogProductEntityVarchars
 * @property CatalogProductIndexGroupPrice[] $catalogProductIndexGroupPrices
 * @property CatalogProductIndexPrice[] $catalogProductIndexPrices
 * @property CatalogProductIndexTierPrice[] $catalogProductIndexTierPrices
 * @property CatalogProductLink[] $catalogProductLinks
 * @property CatalogProductLink[] $catalogProductLinks1
 * @property CatalogProductOption[] $catalogProductOptions
 * @property CatalogProductRelation[] $catalogProductRelations
 * @property CatalogProductRelation[] $catalogProductRelations1
 * @property CatalogProductSuperAttribute[] $catalogProductSuperAttributes
 * @property CatalogProductSuperLink[] $catalogProductSuperLinks
 * @property CatalogProductSuperLink[] $catalogProductSuperLinks1
 * @property StoreWebsite[] $storeWebsites
 * @property CatalogUrlRewriteProductCategory[] $catalogUrlRewriteProductCategories
 * @property CataloginventoryStockItem[] $cataloginventoryStockItems
 * @property DownloadableLink[] $downloadableLinks
 * @property DownloadableSample[] $downloadableSamples
 * @property GoogleshoppingItems[] $googleshoppingItems
 * @property ProductAlertPrice[] $productAlertPrices
 * @property ProductAlertStock[] $productAlertStocks
 * @property QuoteItem[] $quoteItems
 * @property ReportComparedProductIndex[] $reportComparedProductIndexes
 * @property ReportViewedProductAggregatedDaily[] $reportViewedProductAggregatedDailies
 * @property ReportViewedProductAggregatedMonthly[] $reportViewedProductAggregatedMonthlies
 * @property ReportViewedProductAggregatedYearly[] $reportViewedProductAggregatedYearlies
 * @property ReportViewedProductIndex[] $reportViewedProductIndexes
 * @property SalesBestsellersAggregatedDaily[] $salesBestsellersAggregatedDailies
 * @property SalesBestsellersAggregatedMonthly[] $salesBestsellersAggregatedMonthlies
 * @property SalesBestsellersAggregatedYearly[] $salesBestsellersAggregatedYearlies
 * @property WeeeTax[] $weeeTaxes
 * @property WishlistItem[] $wishlistItems
 */
class Mage2CatalogProductEntityPeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_entity}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('attribute_set_id, has_options, required_options', 'numerical', 'integerOnly'=>true),
			array('type_id', 'length', 'max'=>32),
			array('sku', 'length', 'max'=>64),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entity_id, attribute_set_id, type_id, sku, has_options, required_options, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'catalogCategoryEntities' => array(self::MANY_MANY, 'CatalogCategoryEntity', 'catalog_category_product(product_id, category_id)'),
			'catalogCompareItems' => array(self::HAS_MANY, 'CatalogCompareItem', 'product_id'),
			'catalogProductBundleOptions' => array(self::HAS_MANY, 'CatalogProductBundleOption', 'parent_id'),
			'catalogProductBundlePriceIndexes' => array(self::HAS_MANY, 'CatalogProductBundlePriceIndex', 'entity_id'),
			'catalogProductBundleSelections' => array(self::HAS_MANY, 'CatalogProductBundleSelection', 'product_id'),
			'attributeSet' => array(self::BELONGS_TO, 'EavAttributeSet', 'attribute_set_id'),
			'catalogProductEntityDatetimes' => array(self::HAS_MANY, 'CatalogProductEntityDatetime', 'entity_id'),
			'catalogProductEntityDecimals' => array(self::HAS_MANY, 'CatalogProductEntityDecimal', 'entity_id'),
			'catalogProductEntityGalleries' => array(self::HAS_MANY, 'CatalogProductEntityGallery', 'entity_id'),
			'catalogProductEntityGroupPrices' => array(self::HAS_MANY, 'CatalogProductEntityGroupPrice', 'entity_id'),
			'catalogProductEntityInts' => array(self::HAS_MANY, 'CatalogProductEntityInt', 'entity_id'),
			'catalogProductEntityMediaGalleries' => array(self::HAS_MANY, 'CatalogProductEntityMediaGallery', 'entity_id'),
			'catalogProductEntityTexts' => array(self::HAS_MANY, 'CatalogProductEntityText', 'entity_id'),
			'catalogProductEntityTierPrices' => array(self::HAS_MANY, 'CatalogProductEntityTierPrice', 'entity_id'),
			'catalogProductEntityVarchars' => array(self::HAS_MANY, 'CatalogProductEntityVarchar', 'entity_id'),
			'catalogProductIndexGroupPrices' => array(self::HAS_MANY, 'CatalogProductIndexGroupPrice', 'entity_id'),
			'catalogProductIndexPrices' => array(self::HAS_MANY, 'CatalogProductIndexPrice', 'entity_id'),
			'catalogProductIndexTierPrices' => array(self::HAS_MANY, 'CatalogProductIndexTierPrice', 'entity_id'),
			'catalogProductLinks' => array(self::HAS_MANY, 'CatalogProductLink', 'linked_product_id'),
			'catalogProductLinks1' => array(self::HAS_MANY, 'CatalogProductLink', 'product_id'),
			'catalogProductOptions' => array(self::HAS_MANY, 'CatalogProductOption', 'product_id'),
			'catalogProductRelations' => array(self::HAS_MANY, 'CatalogProductRelation', 'child_id'),
			'catalogProductRelations1' => array(self::HAS_MANY, 'CatalogProductRelation', 'parent_id'),
			'catalogProductSuperAttributes' => array(self::HAS_MANY, 'CatalogProductSuperAttribute', 'product_id'),
			'catalogProductSuperLinks' => array(self::HAS_MANY, 'CatalogProductSuperLink', 'product_id'),
			'catalogProductSuperLinks1' => array(self::HAS_MANY, 'CatalogProductSuperLink', 'parent_id'),
			'storeWebsites' => array(self::MANY_MANY, 'StoreWebsite', 'catalog_product_website(product_id, website_id)'),
			'catalogUrlRewriteProductCategories' => array(self::HAS_MANY, 'CatalogUrlRewriteProductCategory', 'product_id'),
			'cataloginventoryStockItems' => array(self::HAS_MANY, 'CataloginventoryStockItem', 'product_id'),
			'downloadableLinks' => array(self::HAS_MANY, 'DownloadableLink', 'product_id'),
			'downloadableSamples' => array(self::HAS_MANY, 'DownloadableSample', 'product_id'),
			'googleshoppingItems' => array(self::HAS_MANY, 'GoogleshoppingItems', 'product_id'),
			'productAlertPrices' => array(self::HAS_MANY, 'ProductAlertPrice', 'product_id'),
			'productAlertStocks' => array(self::HAS_MANY, 'ProductAlertStock', 'product_id'),
			'quoteItems' => array(self::HAS_MANY, 'QuoteItem', 'product_id'),
			'reportComparedProductIndexes' => array(self::HAS_MANY, 'ReportComparedProductIndex', 'product_id'),
			'reportViewedProductAggregatedDailies' => array(self::HAS_MANY, 'ReportViewedProductAggregatedDaily', 'product_id'),
			'reportViewedProductAggregatedMonthlies' => array(self::HAS_MANY, 'ReportViewedProductAggregatedMonthly', 'product_id'),
			'reportViewedProductAggregatedYearlies' => array(self::HAS_MANY, 'ReportViewedProductAggregatedYearly', 'product_id'),
			'reportViewedProductIndexes' => array(self::HAS_MANY, 'ReportViewedProductIndex', 'product_id'),
			'salesBestsellersAggregatedDailies' => array(self::HAS_MANY, 'SalesBestsellersAggregatedDaily', 'product_id'),
			'salesBestsellersAggregatedMonthlies' => array(self::HAS_MANY, 'SalesBestsellersAggregatedMonthly', 'product_id'),
			'salesBestsellersAggregatedYearlies' => array(self::HAS_MANY, 'SalesBestsellersAggregatedYearly', 'product_id'),
			'weeeTaxes' => array(self::HAS_MANY, 'WeeeTax', 'entity_id'),
			'wishlistItems' => array(self::HAS_MANY, 'WishlistItem', 'product_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'entity_id' => 'Entity',
			'attribute_set_id' => 'Attribute Set',
			'type_id' => 'Type',
			'sku' => 'Sku',
			'has_options' => 'Has Options',
			'required_options' => 'Required Options',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
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

		$criteria->compare('entity_id',$this->entity_id,true);
		$criteria->compare('attribute_set_id',$this->attribute_set_id);
		$criteria->compare('type_id',$this->type_id,true);
		$criteria->compare('sku',$this->sku,true);
		$criteria->compare('has_options',$this->has_options);
		$criteria->compare('required_options',$this->required_options);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

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
	 * @return Mage2CatalogProductEntityPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
