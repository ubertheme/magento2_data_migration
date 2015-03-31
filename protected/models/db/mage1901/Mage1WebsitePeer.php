<?php

/**
 * This is the model class for table "core_website".
 *
 * The followings are the available columns in table 'core_website':
 * @property integer $website_id
 * @property string $code
 * @property string $name
 * @property integer $sort_order
 * @property integer $default_group_id
 * @property integer $is_default
 * @property integer $is_staging
 * @property string $master_login
 * @property string $master_password
 * @property string $visibility
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
 * @property CataloginventoryStockStatus[] $cataloginventoryStockStatuses
 * @property CatalogruleGroupWebsite[] $catalogruleGroupWebsites
 * @property CatalogruleProduct[] $catalogruleProducts
 * @property CatalogruleProductPrice[] $catalogruleProductPrices
 * @property Catalogrule[] $catalogrules
 * @property CoreStore[] $coreStores
 * @property CoreStoreGroup[] $coreStoreGroups
 * @property EavAttribute[] $eavAttributes
 * @property CustomerEntity[] $customerEntities
 * @property DownloadableLinkPrice[] $downloadableLinkPrices
 * @property PaypalCert[] $paypalCerts
 * @property PersistentSession[] $persistentSessions
 * @property ProductAlertPrice[] $productAlertPrices
 * @property ProductAlertStock[] $productAlertStocks
 * @property SalesruleProductAttribute[] $salesruleProductAttributes
 * @property Salesrule[] $salesrules
 * @property WeeeDiscount[] $weeeDiscounts
 * @property WeeeTax[] $weeeTaxes
 */
class Mage1WebsitePeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{core_website}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sort_order, default_group_id, is_default, is_staging', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>32),
			array('name', 'length', 'max'=>64),
			array('master_login, visibility', 'length', 'max'=>40),
			array('master_password', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('website_id, code, name, sort_order, default_group_id, is_default, is_staging, master_login, master_password, visibility', 'safe', 'on'=>'search'),
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
			'is_staging' => 'Is Staging',
			'master_login' => 'Master Login',
			'master_password' => 'Master Password',
			'visibility' => 'Visibility',
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
		$criteria->compare('is_staging',$this->is_staging);
		$criteria->compare('master_login',$this->master_login,true);
		$criteria->compare('master_password',$this->master_password,true);
		$criteria->compare('visibility',$this->visibility,true);

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
	 * @return Mage1Website the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
