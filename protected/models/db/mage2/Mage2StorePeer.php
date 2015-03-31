<?php

/**
 * This is the model class for table "store".
 *
 * The followings are the available columns in table 'store':
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
 * @property CatalogCompareItem[] $catalogCompareItems
 * @property CatalogProductEntityDatetime[] $catalogProductEntityDatetimes
 * @property CatalogProductEntityDecimal[] $catalogProductEntityDecimals
 * @property CatalogProductEntityGallery[] $catalogProductEntityGalleries
 * @property CatalogProductEntityInt[] $catalogProductEntityInts
 * @property CatalogProductEntityMediaGallery[] $catalogProductEntityMediaGalleries
 * @property CatalogProductEntityText[] $catalogProductEntityTexts
 * @property CatalogProductEntityVarchar[] $catalogProductEntityVarchars
 * @property CatalogProductOptionPrice[] $catalogProductOptionPrices
 * @property CatalogProductOptionTitle[] $catalogProductOptionTitles
 * @property CatalogProductOptionTypePrice[] $catalogProductOptionTypePrices
 * @property CatalogProductOptionTypeTitle[] $catalogProductOptionTypeTitles
 * @property CatalogProductSuperAttributeLabel[] $catalogProductSuperAttributeLabels
 * @property CheckoutAgreement[] $checkoutAgreements
 * @property CmsBlock[] $cmsBlocks
 * @property CmsPage[] $cmsPages
 * @property CoreLayoutLink[] $coreLayoutLinks
 * @property CoreVariableValue[] $coreVariableValues
 * @property CustomerEntity[] $customerEntities
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
 * @property SalesCreditmemo[] $salesCreditmemos
 * @property SalesCreditmemoGrid[] $salesCreditmemoGrs
 * @property SalesInvoice[] $salesInvoices
 * @property SalesInvoiceGrid[] $salesInvoiceGrs
 * @property SalesInvoicedAggregated[] $salesInvoicedAggregateds
 * @property SalesInvoicedAggregatedOrder[] $salesInvoicedAggregatedOrders
 * @property SalesOrder[] $salesOrders
 * @property SalesOrderAggregatedCreated[] $salesOrderAggregatedCreateds
 * @property SalesOrderAggregatedUpdated[] $salesOrderAggregatedUpdateds
 * @property SalesOrderGrid[] $salesOrderGrs
 * @property SalesOrderItem[] $salesOrderItems
 * @property SalesOrderStatus[] $salesOrderStatuses
 * @property SalesQuote[] $salesQuotes
 * @property SalesQuoteItem[] $salesQuoteItems
 * @property SalesRefundedAggregated[] $salesRefundedAggregateds
 * @property SalesRefundedAggregatedOrder[] $salesRefundedAggregatedOrders
 * @property SalesShipment[] $salesShipments
 * @property SalesShipmentGrid[] $salesShipmentGrs
 * @property SalesShippingAggregated[] $salesShippingAggregateds
 * @property SalesShippingAggregatedOrder[] $salesShippingAggregatedOrders
 * @property SalesruleCouponAggregated[] $salesruleCouponAggregateds
 * @property SalesruleCouponAggregatedOrder[] $salesruleCouponAggregatedOrders
 * @property SalesruleCouponAggregatedUpdated[] $salesruleCouponAggregatedUpdateds
 * @property SalesruleLabel[] $salesruleLabels
 * @property SearchQuery[] $searchQueries
 * @property Sitemap[] $sitemaps
 * @property StoreGroup $group
 * @property StoreWebsite $website
 * @property TaxCalculationRateTitle[] $taxCalculationRateTitles
 * @property TaxOrderAggregatedCreated[] $taxOrderAggregatedCreateds
 * @property TaxOrderAggregatedUpdated[] $taxOrderAggregatedUpdateds
 * @property Translation[] $translations
 * @property WishlistItem[] $wishlistItems
 */
class Mage2StorePeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{store}}';
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
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'catalogCategoryEntityDatetimes' => array(self::HAS_MANY, 'CatalogCategoryEntityDatetime', 'store_id'),
			'catalogCategoryEntityDecimals' => array(self::HAS_MANY, 'CatalogCategoryEntityDecimal', 'store_id'),
			'catalogCategoryEntityInts' => array(self::HAS_MANY, 'CatalogCategoryEntityInt', 'store_id'),
			'catalogCategoryEntityTexts' => array(self::HAS_MANY, 'CatalogCategoryEntityText', 'store_id'),
			'catalogCategoryEntityVarchars' => array(self::HAS_MANY, 'CatalogCategoryEntityVarchar', 'store_id'),
			'catalogCompareItems' => array(self::HAS_MANY, 'CatalogCompareItem', 'store_id'),
			'catalogProductEntityDatetimes' => array(self::HAS_MANY, 'CatalogProductEntityDatetime', 'store_id'),
			'catalogProductEntityDecimals' => array(self::HAS_MANY, 'CatalogProductEntityDecimal', 'store_id'),
			'catalogProductEntityGalleries' => array(self::HAS_MANY, 'CatalogProductEntityGallery', 'store_id'),
			'catalogProductEntityInts' => array(self::HAS_MANY, 'CatalogProductEntityInt', 'store_id'),
			'catalogProductEntityMediaGalleries' => array(self::MANY_MANY, 'CatalogProductEntityMediaGallery', 'catalog_product_entity_media_gallery_value(store_id, value_id)'),
			'catalogProductEntityTexts' => array(self::HAS_MANY, 'CatalogProductEntityText', 'store_id'),
			'catalogProductEntityVarchars' => array(self::HAS_MANY, 'CatalogProductEntityVarchar', 'store_id'),
			'catalogProductOptionPrices' => array(self::HAS_MANY, 'CatalogProductOptionPrice', 'store_id'),
			'catalogProductOptionTitles' => array(self::HAS_MANY, 'CatalogProductOptionTitle', 'store_id'),
			'catalogProductOptionTypePrices' => array(self::HAS_MANY, 'CatalogProductOptionTypePrice', 'store_id'),
			'catalogProductOptionTypeTitles' => array(self::HAS_MANY, 'CatalogProductOptionTypeTitle', 'store_id'),
			'catalogProductSuperAttributeLabels' => array(self::HAS_MANY, 'CatalogProductSuperAttributeLabel', 'store_id'),
			'checkoutAgreements' => array(self::MANY_MANY, 'CheckoutAgreement', 'checkout_agreement_store(store_id, agreement_id)'),
			'cmsBlocks' => array(self::MANY_MANY, 'CmsBlock', 'cms_block_store(store_id, block_id)'),
			'cmsPages' => array(self::MANY_MANY, 'CmsPage', 'cms_page_store(store_id, page_id)'),
			'coreLayoutLinks' => array(self::HAS_MANY, 'CoreLayoutLink', 'store_id'),
			'coreVariableValues' => array(self::HAS_MANY, 'CoreVariableValue', 'store_id'),
			'customerEntities' => array(self::HAS_MANY, 'CustomerEntity', 'store_id'),
			'designChanges' => array(self::HAS_MANY, 'DesignChange', 'store_id'),
			'downloadableLinkTitles' => array(self::HAS_MANY, 'DownloadableLinkTitle', 'store_id'),
			'downloadableSampleTitles' => array(self::HAS_MANY, 'DownloadableSampleTitle', 'store_id'),
			'eavAttributeLabels' => array(self::HAS_MANY, 'EavAttributeLabel', 'store_id'),
			'eavAttributeOptionValues' => array(self::HAS_MANY, 'EavAttributeOptionValue', 'store_id'),
			'eavEntities' => array(self::HAS_MANY, 'EavEntity', 'store_id'),
			'eavEntityDatetimes' => array(self::HAS_MANY, 'EavEntityDatetime', 'store_id'),
			'eavEntityDecimals' => array(self::HAS_MANY, 'EavEntityDecimal', 'store_id'),
			'eavEntityInts' => array(self::HAS_MANY, 'EavEntityInt', 'store_id'),
			'eavEntityStores' => array(self::HAS_MANY, 'EavEntityStore', 'store_id'),
			'eavEntityTexts' => array(self::HAS_MANY, 'EavEntityText', 'store_id'),
			'eavEntityVarchars' => array(self::HAS_MANY, 'EavEntityVarchar', 'store_id'),
			'eavFormFieldsets' => array(self::MANY_MANY, 'EavFormFieldset', 'eav_form_fieldset_label(store_id, fieldset_id)'),
			'eavFormTypes' => array(self::HAS_MANY, 'EavFormType', 'store_id'),
			'googleoptimizerCodes' => array(self::HAS_MANY, 'GoogleoptimizerCode', 'store_id'),
			'googleshoppingItems' => array(self::HAS_MANY, 'GoogleshoppingItems', 'store_id'),
			'newsletterQueues' => array(self::MANY_MANY, 'NewsletterQueue', 'newsletter_queue_store_link(store_id, queue_id)'),
			'newsletterSubscribers' => array(self::HAS_MANY, 'NewsletterSubscriber', 'store_id'),
			'ratingOptionVoteAggregateds' => array(self::HAS_MANY, 'RatingOptionVoteAggregated', 'store_id'),
			'ratings' => array(self::MANY_MANY, 'Rating', 'rating_store(store_id, rating_id)'),
			'ratings1' => array(self::MANY_MANY, 'Rating', 'rating_title(store_id, rating_id)'),
			'reportComparedProductIndexes' => array(self::HAS_MANY, 'ReportComparedProductIndex', 'store_id'),
			'reportEvents' => array(self::HAS_MANY, 'ReportEvent', 'store_id'),
			'reportViewedProductAggregatedDailies' => array(self::HAS_MANY, 'ReportViewedProductAggregatedDaily', 'store_id'),
			'reportViewedProductAggregatedMonthlies' => array(self::HAS_MANY, 'ReportViewedProductAggregatedMonthly', 'store_id'),
			'reportViewedProductAggregatedYearlies' => array(self::HAS_MANY, 'ReportViewedProductAggregatedYearly', 'store_id'),
			'reportViewedProductIndexes' => array(self::HAS_MANY, 'ReportViewedProductIndex', 'store_id'),
			'reviewDetails' => array(self::HAS_MANY, 'ReviewDetail', 'store_id'),
			'reviewEntitySummaries' => array(self::HAS_MANY, 'ReviewEntitySummary', 'store_id'),
			'reviews' => array(self::MANY_MANY, 'Review', 'review_store(store_id, review_id)'),
			'salesBestsellersAggregatedDailies' => array(self::HAS_MANY, 'SalesBestsellersAggregatedDaily', 'store_id'),
			'salesBestsellersAggregatedMonthlies' => array(self::HAS_MANY, 'SalesBestsellersAggregatedMonthly', 'store_id'),
			'salesBestsellersAggregatedYearlies' => array(self::HAS_MANY, 'SalesBestsellersAggregatedYearly', 'store_id'),
			'salesCreditmemos' => array(self::HAS_MANY, 'SalesCreditmemo', 'store_id'),
			'salesCreditmemoGrs' => array(self::HAS_MANY, 'SalesCreditmemoGrid', 'store_id'),
			'salesInvoices' => array(self::HAS_MANY, 'SalesInvoice', 'store_id'),
			'salesInvoiceGrs' => array(self::HAS_MANY, 'SalesInvoiceGrid', 'store_id'),
			'salesInvoicedAggregateds' => array(self::HAS_MANY, 'SalesInvoicedAggregated', 'store_id'),
			'salesInvoicedAggregatedOrders' => array(self::HAS_MANY, 'SalesInvoicedAggregatedOrder', 'store_id'),
			'salesOrders' => array(self::HAS_MANY, 'SalesOrder', 'store_id'),
			'salesOrderAggregatedCreateds' => array(self::HAS_MANY, 'SalesOrderAggregatedCreated', 'store_id'),
			'salesOrderAggregatedUpdateds' => array(self::HAS_MANY, 'SalesOrderAggregatedUpdated', 'store_id'),
			'salesOrderGrs' => array(self::HAS_MANY, 'SalesOrderGrid', 'store_id'),
			'salesOrderItems' => array(self::HAS_MANY, 'SalesOrderItem', 'store_id'),
			'salesOrderStatuses' => array(self::MANY_MANY, 'SalesOrderStatus', 'sales_order_status_label(store_id, status)'),
			'salesQuotes' => array(self::HAS_MANY, 'SalesQuote', 'store_id'),
			'salesQuoteItems' => array(self::HAS_MANY, 'SalesQuoteItem', 'store_id'),
			'salesRefundedAggregateds' => array(self::HAS_MANY, 'SalesRefundedAggregated', 'store_id'),
			'salesRefundedAggregatedOrders' => array(self::HAS_MANY, 'SalesRefundedAggregatedOrder', 'store_id'),
			'salesShipments' => array(self::HAS_MANY, 'SalesShipment', 'store_id'),
			'salesShipmentGrs' => array(self::HAS_MANY, 'SalesShipmentGrid', 'store_id'),
			'salesShippingAggregateds' => array(self::HAS_MANY, 'SalesShippingAggregated', 'store_id'),
			'salesShippingAggregatedOrders' => array(self::HAS_MANY, 'SalesShippingAggregatedOrder', 'store_id'),
			'salesruleCouponAggregateds' => array(self::HAS_MANY, 'SalesruleCouponAggregated', 'store_id'),
			'salesruleCouponAggregatedOrders' => array(self::HAS_MANY, 'SalesruleCouponAggregatedOrder', 'store_id'),
			'salesruleCouponAggregatedUpdateds' => array(self::HAS_MANY, 'SalesruleCouponAggregatedUpdated', 'store_id'),
			'salesruleLabels' => array(self::HAS_MANY, 'SalesruleLabel', 'store_id'),
			'searchQueries' => array(self::HAS_MANY, 'SearchQuery', 'store_id'),
			'sitemaps' => array(self::HAS_MANY, 'Sitemap', 'store_id'),
			'group' => array(self::BELONGS_TO, 'StoreGroup', 'group_id'),
			'website' => array(self::BELONGS_TO, 'StoreWebsite', 'website_id'),
			'taxCalculationRateTitles' => array(self::HAS_MANY, 'TaxCalculationRateTitle', 'store_id'),
			'taxOrderAggregatedCreateds' => array(self::HAS_MANY, 'TaxOrderAggregatedCreated', 'store_id'),
			'taxOrderAggregatedUpdateds' => array(self::HAS_MANY, 'TaxOrderAggregatedUpdated', 'store_id'),
			'translations' => array(self::HAS_MANY, 'Translation', 'store_id'),
			'wishlistItems' => array(self::HAS_MANY, 'WishlistItem', 'store_id'),
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
		return Yii::app()->mage2;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Mage2Store the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
