<?php

namespace common\models;

use common\models\core\HasVisibility;
use common\models\core\Visibility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;

/**
 * This is the model class for table "description_history".
 *
 * @property string $description_history_id
 * @property string $description_id
 * @property string $created_at
 * @property string $public_text
 * @property string $protected_text
 * @property string $private_text
 * @property string $public_text_expanded
 * @property string $protected_text_expanded
 * @property string $private_text_expanded
 * @property string $visibility
 *
 * @property Description $description
 */
class DescriptionHistory extends ActiveRecord implements HasVisibility
{
    public static function tableName()
    {
        return 'description_history';
    }

    public function rules()
    {
        return [
            [['description_id'], 'integer'],
            [['public_text'], 'required'],
            [['public_text', 'protected_text', 'private_text'], 'string'],
            [['time_ic'], 'string', 'max' => 255],
            [['visibility'], 'string', 'max' => 20],
            [
                ['description_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Description::className(),
                'targetAttribute' => ['description_id' => 'description_id']
            ],
        ];
    }

    public function beforeSave($insert)
    {
        $this->public_text_expanded = $this->formatText($this->public_text);
        $this->protected_text_expanded = $this->formatText($this->protected_text);
        $this->private_text_expanded = $this->formatText($this->private_text);

        return parent::beforeSave($insert);
    }

    public function attributeLabels()
    {
        return [
            'description_history_id' => Yii::t('app', 'DESCRIPTION_HISTORY_ID'),
            'description_id' => Yii::t('app', 'DESCRIPTION_HISTORY_DESCRIPTION_ID'),
            'created_at' => Yii::t('app', 'DESCRIPTION_HISTORY_CREATED'),
            'time_ic' => Yii::t('app', 'LABEL_TIME_IC'),
            'public_text' => Yii::t('app', 'DESCRIPTION_HISTORY_TEXT_PUBLIC'),
            'protected_text' => Yii::t('app', 'DESCRIPTION_HISTORY_TEXT_PROTECTED'),
            'private_text' => Yii::t('app', 'DESCRIPTION_HISTORY_TEXT_PRIVATE'),
            'public_text_expanded' => Yii::t('app', 'DESCRIPTION_HISTORY_TEXT_PUBLIC_EXPANDED'),
            'protected_text_expanded' => Yii::t('app', 'DESCRIPTION_HISTORY_TEXT_PROTECTED_EXPANDED'),
            'private_text_expanded' => Yii::t('app', 'DESCRIPTION_HISTORY_TEXT_PRIVATE_EXPANDED'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
        ];
    }

    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ],
        ];
    }

    static public function allowedVisibilities(): array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_FULL
        ];
    }

    public function getVisibility(): string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }

    public function getVisibilityLowercase(): string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getNameLowercase();
    }

    /**
     * @param Description $description
     * @return DescriptionHistory
     */
    static public function createFromDescription(Description $description)
    {
        $history = new DescriptionHistory();

        $history->description_id = $description->description_id;
        $history->public_text = $description->public_text;
        $history->private_text = $description->private_text;
        $history->visibility = $description->visibility;

        if ($history->save()) {
            $history->refresh();
            return $history;
        } else {
            return null;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescription()
    {
        return $this->hasOne(Description::className(), ['description_id' => 'description_id']);
    }

    /**
     * @return string|null
     */
    public function getPublicFormatted()
    {
        return Markdown::process(Html::encode($this->public_text_expanded), 'gfm');
    }

    /**
     * @return string|null
     */
    public function getProtectedFormatted()
    {
        return Markdown::process(Html::encode($this->protected_text_expanded), 'gfm');
    }

    /**
     * @return string|null
     */
    public function getPrivateFormatted()
    {
        return Markdown::process(Html::encode($this->private_text_expanded), 'gfm');
    }

    /**
     * @param string $text Text to format
     * @return string
     */
    private function formatText(string $text): string
    {
        return $text;
    }
}
