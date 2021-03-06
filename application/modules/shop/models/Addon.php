<?php

namespace app\modules\shop\models;

use app\traits\FindById;
use devgroup\TagDependencyHelper\ActiveRecordHelper;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%addon}}".
 *
 * @property integer $id
 * @property string $name
 * @property double $price
 * @property integer $currency_id
 * @property integer $price_is_multiplier
 * @property integer $is_product_id
 * @property integer $add_to_order
 * @property integer $addon_category_id
 * @property integer $can_change_quantity
 * @property integer $measure_id
 * @property integer $sort_order
 */
class Addon extends \yii\db\ActiveRecord
{
    use FindById;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => ActiveRecordHelper::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%addon}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'addon_category_id'], 'required'],
            [['name'], 'string'],
            [['price'], 'number'],
            [['currency_id', 'price_is_multiplier', 'is_product_id', 'add_to_order', 'addon_category_id', 'can_change_quantity', 'measure_id', 'sort_order'], 'integer'],
            [['is_product_id',], 'default', 'value' => 0,],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'price' => Yii::t('app', 'Price'),
            'currency_id' => Yii::t('app', 'Currency ID'),
            'price_is_multiplier' => Yii::t('app', 'Price Is Multiplier'),
            'is_product_id' => Yii::t('app', 'Is Product ID'),
            'add_to_order' => Yii::t('app', 'Add To Order'),
            'addon_category_id' => Yii::t('app', 'Addon Category ID'),
            'can_change_quantity' => Yii::t('app', 'Can Change Quantity'),
            'measure_id' => Yii::t('app', 'Measure ID'),
            'sort_order' => Yii::t('app', 'Sort order'),
        ];
    }

    /**
     * Search tasks
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params, $addon_category_id = null)
    {
        /** @var $query \yii\db\ActiveQuery */
        $query = self::find();

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]
        );

        if ($addon_category_id !== null) {
            $this->addon_category_id = $addon_category_id;
            $query->andFilterWhere(['addon_category_id' => $this->addon_category_id]);
        }

        if (!($this->load($params))) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        $query->andFilterWhere(['add_to_order' => $this->add_to_order]);
        $query->andFilterWhere(['is_product_id' => $this->is_product_id]);
        $query->andFilterWhere(['can_change_quantity' => $this->can_change_quantity]);


        return $dataProvider;
    }
}
