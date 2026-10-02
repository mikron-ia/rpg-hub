<?php

namespace common\models\assignment;

use common\components\service\AssignmentService;
use common\models\Story;
use Override;
use Yii;
use yii\base\Model;

class StoryCharacterAssignmentModel extends Model
{
    private(set) string $key;

    public array|string $storyCharacterAssignmentChoicesPublicVital = [];
    public array|string $storyCharacterAssignmentChoicesPublicMajor = [];
    public array|string $storyCharacterAssignmentChoicesPublicMinor = [];
    public array|string $storyCharacterAssignmentChoicesPublicOther = [];
    public array|string $storyCharacterAssignmentChoicesPrivateVital = [];
    public array|string $storyCharacterAssignmentChoicesPrivateMajor = [];
    public array|string $storyCharacterAssignmentChoicesPrivateMinor = [];
    public array|string $storyCharacterAssignmentChoicesPrivateOther = [];

    public function __construct(Story $story)
    {
        $characterAssignments = AssignmentService::extractAssignmentsActingIds($story->getStoryCharacterAssignments());

        $this->key = $story->key;

        $this->storyCharacterAssignmentChoicesPublicVital = $characterAssignments->publicVital;
        $this->storyCharacterAssignmentChoicesPublicMajor = $characterAssignments->publicMajor;
        $this->storyCharacterAssignmentChoicesPublicMinor = $characterAssignments->publicMinor;
        $this->storyCharacterAssignmentChoicesPublicOther = $characterAssignments->publicOther;
        $this->storyCharacterAssignmentChoicesPrivateVital = $characterAssignments->privateVital;
        $this->storyCharacterAssignmentChoicesPrivateMajor = $characterAssignments->privateMajor;
        $this->storyCharacterAssignmentChoicesPrivateMinor = $characterAssignments->privateMinor;
        $this->storyCharacterAssignmentChoicesPrivateOther = $characterAssignments->privateOther;

        parent::__construct();
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'storyCharacterAssignmentChoicesPublicVital' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PUBLIC_VITAL'),
            'storyCharacterAssignmentChoicesPublicMajor' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PUBLIC_MAJOR'),
            'storyCharacterAssignmentChoicesPublicMinor' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PUBLIC_MINOR'),
            'storyCharacterAssignmentChoicesPublicOther' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PUBLIC_OTHER'),
            'storyCharacterAssignmentChoicesPrivateVital' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PRIVATE_VITAL'),
            'storyCharacterAssignmentChoicesPrivateMajor' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PRIVATE_MAJOR'),
            'storyCharacterAssignmentChoicesPrivateMinor' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PRIVATE_MINOR'),
            'storyCharacterAssignmentChoicesPrivateOther' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PRIVATE_OTHER'),
        ];
    }
}
