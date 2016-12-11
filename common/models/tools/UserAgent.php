<?php

namespace common\models\tools;

use common\models\PerformedAction;
use Yii;

/**
 * This is the model class for table "user_agent".
 *
 * @property integer $id
 * @property string $content
 *
 * @property PerformedAction[] $performedActions
 */
class UserAgent extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_agent';
    }

    public function rules()
    {
        return [
            [['content'], 'required'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'USER_AGENT_ID'),
            'content' => Yii::t('app', 'USER_AGENT_CONTENT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerformedActions()
    {
        return $this->hasMany(PerformedAction::className(), ['user_agent_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return UserAgentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAgentQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function __toString():string
    {
        return $this->content;
    }
}
