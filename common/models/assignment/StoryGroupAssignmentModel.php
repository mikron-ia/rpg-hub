<?php

namespace common\models\assignment;

use common\components\service\AssignmentService;
use common\models\Story;
use Override;
use Yii;
use yii\base\Model;

class StoryGroupAssignmentModel extends Model
{
    private(set) string $key;

    public array|string $storyGroupAssignmentChoicesPublicVital = [];
    public array|string $storyGroupAssignmentChoicesPublicMajor = [];
    public array|string $storyGroupAssignmentChoicesPublicMinor = [];
    public array|string $storyGroupAssignmentChoicesPublicOther = [];
    public array|string $storyGroupAssignmentChoicesPrivateVital = [];
    public array|string $storyGroupAssignmentChoicesPrivateMajor = [];
    public array|string $storyGroupAssignmentChoicesPrivateMinor = [];
    public array|string $storyGroupAssignmentChoicesPrivateOther = [];

    public function __construct(Story $story)
    {
        $groupAssignments = AssignmentService::extractAssignmentsActingIds($story->getStoryGroupAssignments());

        $this->key = $story->key;

        $this->storyGroupAssignmentChoicesPublicVital = $groupAssignments->publicVital;
        $this->storyGroupAssignmentChoicesPublicMajor = $groupAssignments->publicMajor;
        $this->storyGroupAssignmentChoicesPublicMinor = $groupAssignments->publicMinor;
        $this->storyGroupAssignmentChoicesPublicOther = $groupAssignments->publicOther;
        $this->storyGroupAssignmentChoicesPrivateVital = $groupAssignments->privateVital;
        $this->storyGroupAssignmentChoicesPrivateMajor = $groupAssignments->privateMajor;
        $this->storyGroupAssignmentChoicesPrivateMinor = $groupAssignments->privateMinor;
        $this->storyGroupAssignmentChoicesPrivateOther = $groupAssignments->privateOther;

        return parent::__construct();
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'storyGroupAssignmentChoicesPublicVital' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PUBLIC_VITAL'),
            'storyGroupAssignmentChoicesPublicMajor' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PUBLIC_MAJOR'),
            'storyGroupAssignmentChoicesPublicMinor' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PUBLIC_MINOR'),
            'storyGroupAssignmentChoicesPublicOther' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PUBLIC_OTHER'),
            'storyGroupAssignmentChoicesPrivateVital' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PRIVATE_VITAL'),
            'storyGroupAssignmentChoicesPrivateMajor' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PRIVATE_MAJOR'),
            'storyGroupAssignmentChoicesPrivateMinor' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PRIVATE_MINOR'),
            'storyGroupAssignmentChoicesPrivateOther' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PRIVATE_OTHER'),
        ];
    }
}
