<?php

namespace common\models\tools;

use common\models\PerformedAction;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ip".
 *
 * @property integer $id
 * @property string $content
 *
 * @property PerformedAction[] $performedActions
 */
class IP extends ActiveRecord
{
    public static function tableName()
    {
        return 'ip';
    }

    /**
     * {@inheritdoc}
     * @return IpQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new IpQuery(get_called_class());
    }

    public function rules()
    {
        return [
            [['content'], 'required'],
            [['content'], 'string', 'max' => 40],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'IP_ID'),
            'content' => Yii::t('app', 'IP_CONTENT'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPerformedActions()
    {
        return $this->hasMany(PerformedAction::class, ['ip_id' => 'id']);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
