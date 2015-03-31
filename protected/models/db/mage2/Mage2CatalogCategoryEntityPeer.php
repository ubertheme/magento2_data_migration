<?php

/**
 * This is the model class for table "catalog_category_entity".
 *
 * The followings are the available columns in table 'catalog_category_entity':
 * @property string $entity_id
 * @property integer $attribute_set_id
 * @property string $parent_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $path
 * @property integer $position
 * @property integer $level
 * @property integer $children_count
 *
 * The followings are the available model relations:
 * @property CatalogCategoryEntityDatetime[] $catalogCategoryEntityDatetimes
 * @property CatalogCategoryEntityDecimal[] $catalogCategoryEntityDecimals
 * @property CatalogCategoryEntityInt[] $catalogCategoryEntityInts
 * @property CatalogCategoryEntityText[] $catalogCategoryEntityTexts
 * @property CatalogCategoryEntityVarchar[] $catalogCategoryEntityVarchars
 * @property CatalogProductEntity[] $catalogProductEntities
 * @property CatalogUrlRewriteProductCategory[] $catalogUrlRewriteProductCategories
 */
class Mage2CatalogCategoryEntityPeer extends Mage2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_category_entity}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('path, position, children_count', 'required'),
			array('attribute_set_id, position, level, children_count', 'numerical', 'integerOnly'=>true),
			array('parent_id', 'length', 'max'=>10),
			array('path', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entity_id, attribute_set_id, parent_id, created_at, updated_at, path, position, level, children_count', 'safe', 'on'=>'search'),
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
			'catalogCategoryEntityDatetimes' => array(self::HAS_MANY, 'CatalogCategoryEntityDatetime', 'entity_id'),
			'catalogCategoryEntityDecimals' => array(self::HAS_MANY, 'CatalogCategoryEntityDecimal', 'entity_id'),
			'catalogCategoryEntityInts' => array(self::HAS_MANY, 'CatalogCategoryEntityInt', 'entity_id'),
			'catalogCategoryEntityTexts' => array(self::HAS_MANY, 'CatalogCategoryEntityText', 'entity_id'),
			'catalogCategoryEntityVarchars' => array(self::HAS_MANY, 'CatalogCategoryEntityVarchar', 'entity_id'),
			'catalogProductEntities' => array(self::MANY_MANY, 'CatalogProductEntity', 'catalog_category_product(category_id, product_id)'),
			'catalogUrlRewriteProductCategories' => array(self::HAS_MANY, 'CatalogUrlRewriteProductCategory', 'category_id'),
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
			'parent_id' => 'Parent',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'path' => 'Path',
			'position' => 'Position',
			'level' => 'Level',
			'children_count' => 'Children Count',
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
		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('position',$this->position);
		$criteria->compare('level',$this->level);
		$criteria->compare('children_count',$this->children_count);

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
	 * @return Mage2CatalogCategoryEntityPeer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
