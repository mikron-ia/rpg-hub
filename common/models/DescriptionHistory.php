<?php

namespace common\models;

use common\models\core\HasVisibility;
use common\models\tools\ToolsForHasVisibility;
use common\models\tools\ToolsForLinkTags;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

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
 * @property int|null $point_in_time_start_id
 * @property int|null $point_in_time_end_id
 * @property int|null $point_in_time_still_valid_id
 *
 * @property Description $description
 * @property PointInTime $pointInTimeStart
 * @property PointInTime $pointInTimeEnd
 */
class DescriptionHistory extends ActiveRecord implements HasVisibility
{
    use ToolsForLinkTags;
    use ToolsForHasVisibility;

    public static function tableName(): string
    {
        return 'description_history';
    }

    public function rules(): array
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
                'targetClass' => Description::class,
                'targetAttribute' => ['description_id' => 'description_id'],
            ],
            [
                ['point_in_time_start_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_start_id' => 'point_in_time_id'],
            ],
            [
                ['point_in_time_end_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_end_id' => 'point_in_time_id'],
            ],
            [
                ['point_in_time_still_valid_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_still_valid_id' => 'point_in_time_id'],
            ],
        ];
    }

    public function beforeSave($insert): bool
    {
        $this->public_text_expanded = $this->expandText($this->public_text);
        $this->protected_text_expanded = $this->expandText($this->protected_text);
        $this->private_text_expanded = $this->expandText($this->private_text);

        return parent::beforeSave($insert);
    }

    /**
     * @return array<string,string>
     */
    public function attributeLabels(): array
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
            'point_in_time_start_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_START'),
            'point_in_time_end_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_END'),
            'point_in_time_still_valid_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_STILL_VALID'),
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * @throws Exception
     */
    static public function createFromDescription(Description $description): ?DescriptionHistory
    {
        $history = new DescriptionHistory();

        $history->description_id = $description->description_id;
        $history->public_text = $description->public_text;
        $history->private_text = $description->private_text;
        $history->visibility = $description->visibility;
        $history->point_in_time_start_id = $description->point_in_time_start_id;
        $history->point_in_time_end_id = $description->point_in_time_end_id;
        $history->point_in_time_still_valid_id = $description->point_in_time_still_valid_id;

        if ($history->save()) {
            $history->refresh();
            return $history;
        }

        return null;
    }

    public function getDescription(): ActiveQuery
    {
        return $this->hasOne(Description::class, ['description_id' => 'description_id']);
    }

    public function getPointInTimeStart(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_start_id']);
    }

    public function getPointInTimeEnd(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_end_id']);
    }

    public function getPointInTimeStillValid(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_still_valid_id']);
    }

    public function getPublicFormatted(): string
    {
        return $this->formatText($this->public_text_expanded);
    }

    public function getProtectedFormatted(): string
    {
        return $this->formatText($this->protected_text_expanded);
    }

    public function getPrivateFormatted(): string
    {
        return $this->formatText($this->private_text_expanded);
    }
}
