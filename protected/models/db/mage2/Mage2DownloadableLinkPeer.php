<?php

/**
 * This is the model class for table "downloadable_link".
 *
 * The followings are the available columns in table 'downloadable_link':
 * @property string $link_id
 * @property string $product_id
 * @property string $sort_order
 * @property integer $number_of_downloads
 * @property integer $is_shareable
 * @property string $link_url
 * @property string $link_file
 * @property string $link_type
 * @property string $sample_url
 * @property string $sample_file
 * @property string $sample_type
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $product
 * @property DownloadableLinkPrice[] $downloadableLinkPrices
 * @property DownloadableLinkTitle[] $downloadableLinkTitles
 */
class Mage2DownloadableLinkPeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{downloadable_link}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('number_of_downloads, is_shareable', 'numerical', 'integerOnly'=>true),
			array('product_id, sort_order', 'length', 'max'=>10),
			array('link_url, link_file, sample_url, sample_file', 'length', 'max'=>255),
			array('link_type, sample_type', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('link_id, product_id, sort_order, number_of_downloads, is_shareable, link_url, link_file, link_type, sample_url, sample_file, sample_type', 'safe', 'on'=>'search'),
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
			'downloadableLinkPrices' => array(self::HAS_MANY, 'DownloadableLinkPrice', 'link_id'),
			'downloadableLinkTitles' => array(self::HAS_MANY, 'DownloadableLinkTitle', 'link_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'link_id' => 'Link',
			'product_id' => 'Product',
			'sort_order' => 'Sort Order',
			'number_of_downloads' => 'Number Of Downloads',
			'is_shareable' => 'Is Shareable',
			'link_url' => 'Link Url',
			'link_file' => 'Link File',
			'link_type' => 'Link Type',
			'sample_url' => 'Sample Url',
			'sample_file' => 'Sample File',
			'sample_type' => 'Sample Type',
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

		$criteria->compare('link_id',$this->link_id,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('sort_order',$this->sort_order,true);
		$criteria->compare('number_of_downloads',$this->number_of_downloads);
		$criteria->compare('is_shareable',$this->is_shareable);
		$criteria->compare('link_url',$this->link_url,true);
		$criteria->compare('link_file',$this->link_file,true);
		$criteria->compare('link_type',$this->link_type,true);
		$criteria->compare('sample_url',$this->sample_url,true);
		$criteria->compare('sample_file',$this->sample_file,true);
		$criteria->compare('sample_type',$this->sample_type,true);

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
	 * @return Mage2DownloadableLinkPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
