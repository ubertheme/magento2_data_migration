<?php

/**
 * This is the model class for table "catalog_product_entity".
 *
 * The followings are the available columns in table 'catalog_product_entity':
 * @property string $entity_id
 * @property integer $entity_type_id
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
 * @property CatalogCategoryProductIndex[] $catalogCategoryProductIndexes
 * @property CatalogCompareItem[] $catalogCompareItems
 * @property CatalogProductBundleOption[] $catalogProductBundleOptions
 * @property CatalogProductBundlePriceIndex[] $catalogProductBundlePriceIndexes
 * @property CatalogProductBundleSelection[] $catalogProductBundleSelections
 * @property CoreStore[] $coreStores
 * @property EavAttributeSet $attributeSet
 * @property EavEntityType $entityType
 * @property CatalogProductEntityDatetime[] $catalogProductEntityDatetimes
 * @property CatalogProductEntityDecimal[] $catalogProductEntityDecimals
 * @property CatalogProductEntityGallery[] $catalogProductEntityGalleries
 * @property CatalogProductEntityGroupPrice[] $catalogProductEntityGroupPrices
 * @property CatalogProductEntityInt[] $catalogProductEntityInts
 * @property CatalogProductEntityMediaGallery[] $catalogProductEntityMediaGalleries
 * @property CatalogProductEntityText[] $catalogProductEntityTexts
 * @property CatalogProductEntityTierPrice[] $catalogProductEntityTierPrices
 * @property CatalogProductEntityVarchar[] $catalogProductEntityVarchars
 * @property CatalogProductFlat1 $catalogProductFlat1
 * @property CatalogProductFlat2 $catalogProductFlat2
 * @property CatalogProductFlat3 $catalogProductFlat3
 * @property CatalogProductFlat4 $catalogProductFlat4
 * @property CatalogProductIndexEav[] $catalogProductIndexEavs
 * @property CatalogProductIndexEavDecimal[] $catalogProductIndexEavDecimals
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
 * @property CoreWebsite[] $coreWebsites
 * @property CataloginventoryStockItem[] $cataloginventoryStockItems
 * @property CataloginventoryStockStatus[] $cataloginventoryStockStatuses
 * @property CatalogruleProduct[] $catalogruleProducts
 * @property CatalogruleProductPrice[] $catalogruleProductPrices
 * @property CatalogsearchQuery[] $catalogsearchQueries
 * @property CoreUrlRewrite[] $coreUrlRewrites
 * @property DownloadableLink[] $downloadableLinks
 * @property DownloadableSample[] $downloadableSamples
 * @property GoogleshoppingItems[] $googleshoppingItems
 * @property ProductAlertPrice[] $productAlertPrices
 * @property ProductAlertStock[] $productAlertStocks
 * @property ReportComparedProductIndex[] $reportComparedProductIndexes
 * @property ReportViewedProductAggregatedDaily[] $reportViewedProductAggregatedDailies
 * @property ReportViewedProductAggregatedMonthly[] $reportViewedProductAggregatedMonthlies
 * @property ReportViewedProductAggregatedYearly[] $reportViewedProductAggregatedYearlies
 * @property ReportViewedProductIndex[] $reportViewedProductIndexes
 * @property SalesBestsellersAggregatedDaily[] $salesBestsellersAggregatedDailies
 * @property SalesBestsellersAggregatedMonthly[] $salesBestsellersAggregatedMonthlies
 * @property SalesBestsellersAggregatedYearly[] $salesBestsellersAggregatedYearlies
 * @property SalesFlatQuoteItem[] $salesFlatQuoteItems
 * @property TagRelation[] $tagRelations
 * @property WeeeDiscount[] $weeeDiscounts
 * @property WeeeTax[] $weeeTaxes
 * @property WishlistItem[] $wishlistItems
 */
class Mage1CatalogProductEntityPeer extends Mage1ActiveRecord
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
			array('entity_type_id, attribute_set_id, has_options, required_options', 'numerical', 'integerOnly'=>true),
			array('type_id', 'length', 'max'=>32),
			array('sku', 'length', 'max'=>64),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entity_id, entity_type_id, attribute_set_id, type_id, sku, has_options, required_options, created_at, updated_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'entity_id' => 'Entity',
			'entity_type_id' => 'Entity Type',
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
		$criteria->compare('entity_type_id',$this->entity_type_id);
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
		return Yii::app()->mage1;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Mage1CatalogProductEntityPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
