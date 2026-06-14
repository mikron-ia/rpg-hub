<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\core\HasKey;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForLinkTags;
use Override;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\web\HttpException;

/**
 * @property int $secret_id
 * @property int $epic_id
 * @property string $key
 * @property string $title
 * @property string $content
 * @property string|null $notes
 * @property string|null $content_expanded
 * @property string|null $notes_expanded
 * @property int $bestowed_list_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property BestowedList $bestowedList
 * @property Epic $epic
 */
class Secret extends ActiveRecord implements HasEpicControl, HasKey
{
    use ToolsForEntity;
    use ToolsForLinkTags;

    public array|string $bestowedAccessIds = [];

    #[Override]
    public static function tableName(): string
    {
        return 'secret';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'secret';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['title', 'notes'], 'default', 'value' => null],
            [['epic_id', 'content'], 'required'],
            [['epic_id'], 'integer'],
            [['content', 'notes'], 'string'],
            [['title'], 'string', 'max' => 120],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'secret_id' => Yii::t('app', 'SECRET_FIELD_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'SECRET_FIELD_KEY'),
            'title' => Yii::t('app', 'SECRET_FIELD_TITLE'),
            'content' => Yii::t('app', 'SECRET_FIELD_CONTENT'),
            'notes' => Yii::t('app', 'SECRET_FIELD_NOTES'),
            'content_expanded' => Yii::t('app', 'SECRET_FIELD_CONTENT'),
            'notes_expanded' => Yii::t('app', 'SECRET_FIELD_NOTES'),
            'created_at' => Yii::t('app', 'SECRET_FIELD_CREATED_AT'),
            'updated_at' => Yii::t('app', 'SECRET_FIELD_UPDATED_AT'),
            'bestowedAccessIds' => Yii::t('app', 'SECRET_FIELD_BESTOWED_ACCESS_IDS')
        ];
    }

    #[Override]
    public function attributeHints(): array
    {
        return [
            'content' => Yii::t('app', 'SECRET_HINT_CONTENT'),
            'notes' => Yii::t('app', 'SECRET_HINT_NOTES'),
        ];
    }

    #[Override]
    public function afterFind(): void
    {
        $this->bestowedAccessIds = $this->bestowedList->getBestowedUserIds();

        parent::afterFind();
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        if (empty($this->bestowed_list_id)) {
            $list = BestowedList::createList();
            $this->bestowed_list_id = $list->bestowed_list_id;
        }

        $this->content_expanded = $this->expandText($this->content);
        $this->notes_expanded = $this->expandText($this->notes);

        return parent::beforeSave($insert);
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'secret_id',
                'className' => 'Secret',
            ],
            'timestampBehavior' => ['class' => TimestampBehavior::class],
        ];
    }

    #[Override]
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getBestowedList(): ActiveQuery
    {
        return $this->hasOne(BestowedList::class, ['bestowed_list_id' => 'bestowed_list_id']);
    }

    public function getBestowedListAsUsernames(bool $useFormatting): array
    {
        $template = $useFormatting ? '<span class="bestowed-username">%s</span>' : '%s';

        return array_map(
            fn(Bestowed $bestowed) => sprintf($template, $bestowed->user->username),
            $this->bestowedList->bestowed
        );
    }

    public function getContentFormatted(): string
    {
        return Markdown::process(Html::encode($this->content_expanded ?? $this->content), 'gfm');
    }

    public function getNotesFormatted(): string
    {
        return Markdown::process(Html::encode($this->notes_expanded ?? $this->notes), 'gfm');
    }

    #[Override]
    public static function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    #[Override]
    public static function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    #[Override]
    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    #[Override]
    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    #[Override]
    public static function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_SECRET'));
    }

    #[Override]
    public static function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_SECRET'));
    }

    #[Override]
    public static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_SECRET'));
    }

    #[Override]
    public static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_SECRET'));
    }
}
