<?php

namespace common\models;

use common\models\core\HasDescriptions;
use common\models\core\HasEpicControl;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasDescriptions;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scenario".
 *
 * @property string $scenario_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $status
 * @property string $tag_line
 * @property string $description_pack_id
 *
 * @property DescriptionPack $descriptionPack
 * @property Epic $epic
 */
class Scenario extends ActiveRecord implements HasDescriptions, HasEpicControl
{
    use ToolsForEntity;
    use ToolsForHasDescriptions;

    const STATUS_NEW = 'new';
    const STATUS_REJECTED = 'rejected';
    const STATUS_USED = 'used';

    public static function tableName(): string
    {
        return 'scenario';
    }

    public function rules(): array
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id', 'description_pack_id'], 'integer'],
            [['name', 'status'], 'string', 'max' => 120],
            [
                ['status'],
                'in',
                'range' => function () {
                    return $this->allowedStatuses();
                }
            ],
            [['tag_line'], 'string', 'max' => 255],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::class,
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey('scenario');
        }

        if (empty($this->description_pack_id)) {
            $pack = DescriptionPack::create('Scenario');
            $this->description_pack_id = $pack->description_pack_id;
        }

        return parent::beforeSave($insert);
    }

    public function attributeLabels(): array
    {
        return [
            'scenario_id' => Yii::t('app', 'SCENARIO_ID'),
            'key' => Yii::t('app', 'SCENARIO_KEY'),
            'epic_id' => Yii::t('app', 'EPIC_ID'),
            'name' => Yii::t('app', 'SCENARIO_NAME'),
            'tag_line' => Yii::t('app', 'SCENARIO_TAGLINE'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK_ID'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDescriptionPack(): ActiveQuery
    {
        return $this->hasOne(DescriptionPack::class, ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    static public function allowedDescriptionTypes(): array
    {
        return [
            Description::TYPE_PREMISE,
            Description::TYPE_PLAN,
            Description::TYPE_ASPECTS,
            Description::TYPE_ACTORS,
            Description::TYPE_SCENE,
            Description::TYPE_ACT,
            Description::TYPE_BRIEFING,
            Description::TYPE_PRELUDE,
            Description::TYPE_INTERLUDE,
            Description::TYPE_POSTLUDE,
            Description::TYPE_DEBRIEFING,
            Description::TYPE_THREADS,
            Description::TYPE_BACKGROUND,
            Description::TYPE_COMMENTARY,
        ];
    }

    static public function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    static public function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    static function throwExceptionAboutCreate()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_SCENARIO'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_SCENARIO'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_SCENARIO'));
    }

    static function throwExceptionAboutView()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_SCENARIO'));
    }

    static public function allowedStatuses(): array
    {
        return [self::STATUS_NEW, self::STATUS_REJECTED, self::STATUS_USED];
    }

    /**
     * @return string[]
     */
    static public function statusNames(): array
    {
        return [
            self::STATUS_NEW => Yii::t('app', 'SCENARIO_STATUS_NEW'),
            self::STATUS_REJECTED => Yii::t('app', 'SCENARIO_STATUS_DISCARDED'),
            self::STATUS_USED => Yii::t('app', 'SCENARIO_STATUS_USED'),
        ];
    }

    /**
     * @return string[]
     */
    static public function statusClasses(): array
    {
        return [
            self::STATUS_NEW => 'scenario-status-proposed',
            self::STATUS_REJECTED => 'scenario-status-discarded',
            self::STATUS_USED => 'scenario-status-used',
        ];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '?';
    }

    /**
     * @return string
     */
    public function getStatusClass(): string
    {
        $names = self::statusClasses();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }
}
