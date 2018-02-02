<?php

namespace rest\models\search;

use common\models\Dish;
use yii\data\ActiveDataProvider;
use yii\base\Model;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class DishSearch extends Dish
{
    public $minPrice;
    public $maxPrice;
    public $username;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chefId', 'name', 'price', 'minPrice', 'maxPrice', 'username'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Dish::find();
        $query->joinWith(['chef']);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        if (!($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }

        if (isset($this->chefId)) {
            $query->andWhere(['chefId' => $this->chefId]);
        }
        if (isset($this->name)) {
            $query->andWhere(['name' => $this->name]);
        }
        if (isset($this->price)) {
            $query->andWhere(['price' => $this->price]);
        }
        if (isset($this->minPrice, $this->maxPrice)) {
            $query->andWhere(['between', 'price', $this->minPrice, $this->maxPrice]);
        }
        if (isset($this->username)) {
            $query->andWhere(['username' => $this->username]);
        }

        return $dataProvider;
    }

}
