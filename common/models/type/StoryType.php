<?php

namespace common\models\type;

use Yii;

enum StoryType: string
{
    case Story = 'story';             // Default story; interactive
    case Chapter = 'chapter';         // Part of a larger story; interactive
    case Episode = 'episode';         // Self-standing episode; interactive
    case Mission = 'mission';         // Self-standing episode; interactive
    case Prologue = 'prologue';       // Prologue to an arc; interactive or not
    case Interlude = 'interlude';     // Interlude in an arc; rarely interactive
    case Epilogue = 'epilogue';       // Epilogue to an arc; interactive or not
    case Part = 'part';               // Part of a larger story; interactive
    case Reading = 'reading';         // Narration to give context; not interactive

    public function name(): string
    {
        return match ($this) {
            self::Story => Yii::t('app', 'STORY_TYPE_STORY'),
            self::Chapter => Yii::t('app', 'STORY_TYPE_CHAPTER'),
            self::Part => Yii::t('app', 'STORY_TYPE_PART'),
            self::Prologue => Yii::t('app', 'STORY_TYPE_PROLOGUE'),
            self::Interlude => Yii::t('app', 'STORY_TYPE_INTERLUDE'),
            self::Epilogue => Yii::t('app', 'STORY_TYPE_EPILOGUE'),
            self::Episode => Yii::t('app', 'STORY_TYPE_EPISODE'),
            self::Mission => Yii::t('app', 'STORY_TYPE_MISSION'),
            self::Reading => Yii::t('app', 'STORY_TYPE_READING'),
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
