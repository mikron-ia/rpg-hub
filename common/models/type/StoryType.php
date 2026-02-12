<?php

namespace common\models\type;

use Yii;

enum StoryType: string
{
    case None = '';                   // No type set

    case Story = 'story';             // Default story; interactive
    case Chapter = 'chapter';         // Part of a larger story; interactive
    case Episode = 'episode';         // Self-standing episode; interactive
    case Mission = 'mission';         // Self-standing episode; interactive
    case Prologue = 'prologue';       // Prologue to an arc; interactive or not
    case Interlude = 'interlude';     // Interlude in an arc; rarely interactive
    case Epilogue = 'epilogue';       // Epilogue to an arc; interactive or not
    case Part = 'part';               // Part of a larger story; interactive
    case Reading = 'reading';         // Context-providing text, generic; not interactive
    case Report = 'report';           // Context-providing text, in-character and modern; not interactive
    case Summary = 'summary';         // Context-providing text, mostly for catch-up purposes after a time-skip; not interactive

    public function name(): string
    {
        return match ($this) {
            self::None => Yii::t('app', 'STORY_TYPE_NONE'),
            self::Story => Yii::t('app', 'STORY_TYPE_STORY'),
            self::Chapter => Yii::t('app', 'STORY_TYPE_CHAPTER'),
            self::Part => Yii::t('app', 'STORY_TYPE_PART'),
            self::Prologue => Yii::t('app', 'STORY_TYPE_PROLOGUE'),
            self::Interlude => Yii::t('app', 'STORY_TYPE_INTERLUDE'),
            self::Epilogue => Yii::t('app', 'STORY_TYPE_EPILOGUE'),
            self::Episode => Yii::t('app', 'STORY_TYPE_EPISODE'),
            self::Mission => Yii::t('app', 'STORY_TYPE_MISSION'),
            self::Reading => Yii::t('app', 'STORY_TYPE_READING'),
            self::Report => Yii::t('app', 'STORY_TYPE_REPORT'),
            self::Summary => Yii::t('app', 'STORY_TYPE_SUMMARY'),
        };
    }

    /**
     * @return array<string>
     */
    public static function allowedCodes(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }

    /**
     * @return array<string,string>
     */
    public static function namesForDropdown(): array
    {
        return array_reduce(
            self::cases(),
            static function (array $names, self $storyType): array {
                $names[$storyType->value] = $storyType->name();
                return $names;
            },
            []
        );
    }
}
