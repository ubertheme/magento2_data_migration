<?php

/**
 * This is the model class for table "core_store".
 *
 * The followings are the available columns in table 'core_store':
 * @property integer $store_id
 * @property string $code
 * @property integer $website_id
 * @property integer $group_id
 * @property string $name
 * @property integer $sort_order
 * @property integer $is_active
 *
 * The followings are the available model relations:
 * @property CatalogCategoryEntityDatetime[] $catalogCategoryEntityDatetimes
 * @property CatalogCategoryEntityDecimal[] $catalogCategoryEntityDecimals
 * @property CatalogCategoryEntityInt[] $catalogCategoryEntityInts
 * @property CatalogCategoryEntityText[] $catalogCategoryEntityTexts
 * @property CatalogCategoryEntityVarchar[] $catalogCategoryEntityVarchars
 * @property CatalogCategoryFlatStore1[] $catalogCategoryFlatStore1s
 * @property CatalogCategoryFlatStore2[] $catalogCategoryFlatStore2s
 * @property CatalogCategoryFlatStore3[] $catalogCategoryFlatStore3s
 * @property CatalogCategoryFlatStore4[] $catalogCategoryFlatStore4s
 * @property CatalogCategoryProductIndex[] $catalogCategoryProductIndexes
 * @property CatalogCompareItem[] $catalogCompareItems
 * @property CatalogProductEntity[] $catalogProductEntities
 * @property CatalogProductEntityDatetime[] $catalogProductEntityDatetimes
 * @property CatalogProductEntityDecimal[] $catalogProductEntityDecimals
 * @property CatalogProductEntityGallery[] $catalogProductEntityGalleries
 * @property CatalogProductEntityInt[] $catalogProductEntityInts
 * @property CatalogProductEntityMediaGallery[] $catalogProductEntityMediaGalleries
 * @property CatalogProductEntityText[] $catalogProductEntityTexts
 * @property CatalogProductEntityVarchar[] $catalogProductEntityVarchars
 * @property CatalogProductIndexEav[] $catalogProductIndexEavs
 * @property CatalogProductIndexEavDecimal[] $catalogProductIndexEavDecimals
 * @property CatalogProductOptionPrice[] $catalogProductOptionPrices
 * @property CatalogProductOptionTitle[] $catalogProductOptionTitles
 * @property CatalogProductOptionTypePrice[] $catalogProductOptionTypePrices
 * @property CatalogProductOptionTypeTitle[] $catalogProductOptionTypeTitles
 * @property CatalogProductSuperAttributeLabel[] $catalogProductSuperAttributeLabels
 * @property CatalogsearchQuery[] $catalogsearchQueries
 * @property CheckoutAgreement[] $checkoutAgreements
 * @property CmsBlock[] $cmsBlocks
 * @property CmsPage[] $cmsPages
 * @property CoreLayoutLink[] $coreLayoutLinks
 * @property CoreStoreGroup $group
 * @property CoreWebsite $website
 * @property CoreTranslate[] $coreTranslates
 * @property CoreUrlRewrite[] $coreUrlRewrites
 * @property CoreVariableValue[] $coreVariableValues
 * @property CouponAggregated[] $couponAggregateds
 * @property CouponAggregatedOrder[] $couponAggregatedOrders
 * @property CouponAggregatedUpdated[] $couponAggregatedUpdateds
 * @property CustomerEntity[] $customerEntities
 * @property DataflowBatch[] $dataflowBatches
 * @property DesignChange[] $designChanges
 * @property DownloadableLinkTitle[] $downloadableLinkTitles
 * @property DownloadableSampleTitle[] $downloadableSampleTitles
 * @property EavAttributeLabel[] $eavAttributeLabels
 * @property EavAttributeOptionValue[] $eavAttributeOptionValues
 * @property EavEntity[] $eavEntities
 * @property EavEntityDatetime[] $eavEntityDatetimes
 * @property EavEntityDecimal[] $eavEntityDecimals
 * @property EavEntityInt[] $eavEntityInts
 * @property EavEntityStore[] $eavEntityStores
 * @property EavEntityText[] $eavEntityTexts
 * @property EavEntityVarchar[] $eavEntityVarchars
 * @property EavFormFieldset[] $eavFormFieldsets
 * @property EavFormType[] $eavFormTypes
 * @property GoogleoptimizerCode[] $googleoptimizerCodes
 * @property GoogleshoppingItems[] $googleshoppingItems
 * @property NewsletterQueue[] $newsletterQueues
 * @property NewsletterSubscriber[] $newsletterSubscribers
 * @property Poll[] $polls
 * @property Poll[] $polls1
 * @property RatingOptionVoteAggregated[] $ratingOptionVoteAggregateds
 * @property Rating[] $ratings
 * @property Rating[] $ratings1
 * @property ReportComparedProductIndex[] $reportComparedProductIndexes
 * @property ReportEvent[] $reportEvents
 * @property ReportViewedProductAggregatedDaily[] $reportViewedProductAggregatedDailies
 * @property ReportViewedProductAggregatedMonthly[] $reportViewedProductAggregatedMonthlies
 * @property ReportViewedProductAggregatedYearly[] $reportViewedProductAggregatedYearlies
 * @property ReportViewedProductIndex[] $reportViewedProductIndexes
 * @property ReviewDetail[] $reviewDetails
 * @property ReviewEntitySummary[] $reviewEntitySummaries
 * @property Review[] $reviews
 * @property SalesBestsellersAggregatedDaily[] $salesBestsellersAggregatedDailies
 * @property SalesBestsellersAggregatedMonthly[] $salesBestsellersAggregatedMonthlies
 * @property SalesBestsellersAggregatedYearly[] $salesBestsellersAggregatedYearlies
 * @property SalesBillingAgreement[] $salesBillingAgreements
 * @property SalesFlatCreditmemo[] $salesFlatCreditmemos
 * @property SalesFlatCreditmemoGrid[] $salesFlatCreditmemoGrs
 * @property SalesFlatInvoice[] $salesFlatInvoices
 * @property SalesFlatInvoiceGrid[] $salesFlatInvoiceGrs
 * @property SalesFlatOrder[] $salesFlatOrders
 * @property SalesFlatOrderGrid[] $salesFlatOrderGrs
 * @property SalesFlatOrderItem[] $salesFlatOrderItems
 * @property SalesFlatQuote[] $salesFlatQuotes
 * @property SalesFlatQuoteItem[] $salesFlatQuoteItems
 * @property SalesFlatShipment[] $salesFlatShipments
 * @property SalesFlatShipmentGrid[] $salesFlatShipmentGrs
 * @property SalesInvoicedAggregated[] $salesInvoicedAggregateds
 * @property SalesInvoicedAggregatedOrder[] $salesInvoicedAggregatedOrders
 * @property SalesOrderAggregatedCreated[] $salesOrderAggregatedCreateds
 * @property SalesOrderAggregatedUpdated[] $salesOrderAggregatedUpdateds
 * @property SalesOrderStatus[] $salesOrderStatuses
 * @property SalesRecurringProfile[] $salesRecurringProfiles
 * @property SalesRefundedAggregated[] $salesRefundedAggregateds
 * @property SalesRefundedAggregatedOrder[] $salesRefundedAggregatedOrders
 * @property SalesShippingAggregated[] $salesShippingAggregateds
 * @property SalesShippingAggregatedOrder[] $salesShippingAggregatedOrders
 * @property SalesruleLabel[] $salesruleLabels
 * @property Sitemap[] $sitemaps
 * @property Tag[] $tags
 * @property Tag[] $tags1
 * @property TagRelation[] $tagRelations
 * @property Tag[] $tags2
 * @property TaxCalculationRateTitle[] $taxCalculationRateTitles
 * @property TaxOrderAggregatedCreated[] $taxOrderAggregatedCreateds
 * @property TaxOrderAggregatedUpdated[] $taxOrderAggregatedUpdateds
 * @property WishlistItem[] $wishlistItems
 * @property WordpressAssociation[] $wordpressAssociations
 * @property XmlconnectApplication[] $xmlconnectApplications
 * @property ZeonLandingpageStore[] $zeonLandingpageStores
 * @property ZeonNews[] $zeonNews
 */
class Mage1StorePeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{core_store}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('website_id, group_id, sort_order, is_active', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>32),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('store_id, code, website_id, group_id, name, sort_order, is_active', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'store_id' => 'Store',
			'code' => 'Code',
			'website_id' => 'Website',
			'group_id' => 'Group',
			'name' => 'Name',
			'sort_order' => 'Sort Order',
			'is_active' => 'Is Active',
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

		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('website_id',$this->website_id);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('is_active',$this->is_active);

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
	 * @return Mage1StorePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
