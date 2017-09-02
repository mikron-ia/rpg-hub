<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "importance".
 *
 * @property string $importance_id
 * @property string $importance_pack_id
 * @property string $user_id
 * @property integer $importance
 *
 * @property ImportancePack $importancePack
 * @property User $user
 */
class Importance extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'importance';
    }

    public function rules()
    {
        return [
            [['importance_pack_id', 'user_id', 'importance'], 'integer'],
            [['importance_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => ImportancePack::className(), 'targetAttribute' => ['importance_pack_id' => 'importance_pack_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'importance_id' => Yii::t('app', 'IMPORTANCE_ID'),
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK'),
            'user_id' => Yii::t('app', 'IMPORTANCE_USER'),
            'importance' => Yii::t('app', 'IMPORTANCE_VALUE'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImportancePack()
    {
        return $this->hasOne(ImportancePack::className(), ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Recalculates the importance object
     * @return bool
     */
    public function recalculate():bool
    {
        return true;
    }
}
