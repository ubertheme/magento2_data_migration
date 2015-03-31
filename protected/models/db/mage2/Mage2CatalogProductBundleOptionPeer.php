<?php

/**
 * This is the model class for table "catalog_product_bundle_option".
 *
 * The followings are the available columns in table 'catalog_product_bundle_option':
 * @property string $option_id
 * @property string $parent_id
 * @property integer $required
 * @property string $position
 * @property string $type
 *
 * The followings are the available model relations:
 * @property CatalogProductEntity $parent
 * @property CatalogProductBundleOptionValue[] $catalogProductBundleOptionValues
 * @property CatalogProductBundleSelection[] $catalogProductBundleSelections
 */
class Mage2CatalogProductBundleOptionPeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_product_bundle_option}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id', 'required'),
			array('required', 'numerical', 'integerOnly'=>true),
			array('parent_id, position', 'length', 'max'=>10),
			array('type', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('option_id, parent_id, required, position, type', 'safe', 'on'=>'search'),
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
			'parent' => array(self::BELONGS_TO, 'CatalogProductEntity', 'parent_id'),
			'catalogProductBundleOptionValues' => array(self::HAS_MANY, 'CatalogProductBundleOptionValue', 'option_id'),
			'catalogProductBundleSelections' => array(self::HAS_MANY, 'CatalogProductBundleSelection', 'option_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'option_id' => 'Option',
			'parent_id' => 'Parent',
			'required' => 'Required',
			'position' => 'Position',
			'type' => 'Type',
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

		$criteria->compare('option_id',$this->option_id,true);
		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('required',$this->required);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('type',$this->type,true);

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
	 * @return Mage2CatalogProductBundleOptionPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
