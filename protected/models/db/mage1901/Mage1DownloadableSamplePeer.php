<?php

/**
 * This is the model class for table "downloadable_sample".
 *
 * The followings are the available columns in table 'downloadable_sample':
 * @property string $sample_id
 * @property string $product_id
 * @property string $sample_url
 * @property string $sample_file
 * @property string $sample_type
 * @property string $sort_order
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $product
 * @property DownloadableSampleTitle[] $downloadableSampleTitles
 */
class Mage1DownloadableSamplePeer extends Mage1ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{downloadable_sample}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, sort_order', 'length', 'max'=>10),
			array('sample_url, sample_file', 'length', 'max'=>255),
			array('sample_type', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('sample_id, product_id, sample_url, sample_file, sample_type, sort_order', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'sample_id' => 'Sample',
			'product_id' => 'Product',
			'sample_url' => 'Sample Url',
			'sample_file' => 'Sample File',
			'sample_type' => 'Sample Type',
			'sort_order' => 'Sort Order',
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

		$criteria->compare('sample_id',$this->sample_id,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('sample_url',$this->sample_url,true);
		$criteria->compare('sample_file',$this->sample_file,true);
		$criteria->compare('sample_type',$this->sample_type,true);
		$criteria->compare('sort_order',$this->sort_order,true);

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
	 * @return Mage1DownloadableSamplePeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
