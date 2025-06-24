<?php

namespace common\models;

use yii\data\ActiveDataProvider;

class UserQuery extends User
{
    private const PREFERRED_POSITIONS_PER_PAGE = 8;

    private int $pageCount;

    public function __construct(int $pagination = self::PREFERRED_POSITIONS_PER_PAGE, array $config = [])
    {
        $this->pageCount = $pagination;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['username', 'email'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => $this->pageCount]]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'username', $this->username])->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
