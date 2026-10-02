<?php

namespace common\models\assignment;

use common\components\service\AssignmentService;
use common\models\Character;
use Override;
use Yii;
use yii\base\Model;

class CharacterStoryAssignmentModel extends Model
{
    private(set) string $key;

    public array|string $characterStoryAssignmentChoicesPublicVital = [];
    public array|string $characterStoryAssignmentChoicesPublicMajor = [];
    public array|string $characterStoryAssignmentChoicesPublicMinor = [];
    public array|string $characterStoryAssignmentChoicesPublicOther = [];
    public array|string $characterStoryAssignmentChoicesPrivateVital = [];
    public array|string $characterStoryAssignmentChoicesPrivateMajor = [];
    public array|string $characterStoryAssignmentChoicesPrivateMinor = [];
    public array|string $characterStoryAssignmentChoicesPrivateOther = [];

    public function __construct(Character $character)
    {
        $this->key = $character->key;

        $storyAssignments = AssignmentService::extractAssignmentsNarrativeIds($character->getStoryCharacterAssignments());

        $this->characterStoryAssignmentChoicesPublicVital = $storyAssignments->publicVital;
        $this->characterStoryAssignmentChoicesPublicMajor = $storyAssignments->publicMajor;
        $this->characterStoryAssignmentChoicesPublicMinor = $storyAssignments->publicMinor;
        $this->characterStoryAssignmentChoicesPublicOther = $storyAssignments->publicOther;
        $this->characterStoryAssignmentChoicesPrivateVital = $storyAssignments->privateVital;
        $this->characterStoryAssignmentChoicesPrivateMajor = $storyAssignments->privateMajor;
        $this->characterStoryAssignmentChoicesPrivateMinor = $storyAssignments->privateMinor;
        $this->characterStoryAssignmentChoicesPrivateOther = $storyAssignments->privateOther;

        parent::__construct();
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeLabels(): array
    {
        return [
            'characterStoryAssignmentChoicesPublicVital' => Yii::t(
                'app',
                'CHARACTER_STORY_ASSIGNMENT_CHOICES_PUBLIC_VITAL'
            ),
            'characterStoryAssignmentChoicesPublicMajor' => Yii::t(
                'app',
                'CHARACTER_STORY_ASSIGNMENT_CHOICES_PUBLIC_MAJOR'
            ),
            'characterStoryAssignmentChoicesPublicMinor' => Yii::t(
                'app',
                'CHARACTER_STORY_ASSIGNMENT_CHOICES_PUBLIC_MINOR'
            ),
            'characterStoryAssignmentChoicesPublicOther' => Yii::t(
                'app',
                'CHARACTER_STORY_ASSIGNMENT_CHOICES_PUBLIC_OTHER'
            ),
            'characterStoryAssignmentChoicesPrivateVital' => Yii::t(
                'app',
                'CHARACTER_STORY_ASSIGNMENT_CHOICES_PRIVATE_VITAL'
            ),
            'characterStoryAssignmentChoicesPrivateMajor' => Yii::t(
                'app',
                'CHARACTER_STORY_ASSIGNMENT_CHOICES_PRIVATE_MAJOR'
            ),
            'characterStoryAssignmentChoicesPrivateMinor' => Yii::t(
                'app',
                'CHARACTER_STORY_ASSIGNMENT_CHOICES_PRIVATE_MINOR'
            ),
            'characterStoryAssignmentChoicesPrivateOther' => Yii::t(
                'app',
                'CHARACTER_STORY_ASSIGNMENT_CHOICES_PRIVATE_OTHER'
            ),
        ];
    }
}
