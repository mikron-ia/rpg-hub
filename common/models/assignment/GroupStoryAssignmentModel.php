<?php

namespace common\models\assignment;

use common\components\service\AssignmentService;
use common\models\Group;
use Override;
use Yii;
use yii\base\Model;

class GroupStoryAssignmentModel extends Model
{
    private(set) string $key;

    public array|string $groupStoryAssignmentChoicesPublicVital = [];
    public array|string $groupStoryAssignmentChoicesPublicMajor = [];
    public array|string $groupStoryAssignmentChoicesPublicMinor = [];
    public array|string $groupStoryAssignmentChoicesPublicOther = [];

    public array|string $groupStoryAssignmentChoicesPrivateVital = [];
    public array|string $groupStoryAssignmentChoicesPrivateMajor = [];
    public array|string $groupStoryAssignmentChoicesPrivateMinor = [];
    public array|string $groupStoryAssignmentChoicesPrivateOther = [];

    public function __construct(Group $group)
    {
        $this->key = $group->key;

        $storyAssignments = AssignmentService::extractAssignmentsNarrativeIds($group->getStoryGroupAssignments());

        $this->groupStoryAssignmentChoicesPublicVital = $storyAssignments->publicVital;
        $this->groupStoryAssignmentChoicesPublicMajor = $storyAssignments->publicMajor;
        $this->groupStoryAssignmentChoicesPublicMinor = $storyAssignments->publicMinor;
        $this->groupStoryAssignmentChoicesPublicOther = $storyAssignments->publicOther;
        $this->groupStoryAssignmentChoicesPrivateVital = $storyAssignments->privateVital;
        $this->groupStoryAssignmentChoicesPrivateMajor = $storyAssignments->privateMajor;
        $this->groupStoryAssignmentChoicesPrivateMinor = $storyAssignments->privateMinor;
        $this->groupStoryAssignmentChoicesPrivateOther = $storyAssignments->privateOther;

        parent::__construct();
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'groupStoryAssignmentChoicesPublicVital' => Yii::t('app', 'GROUP_STORY_ASSIGNMENT_CHOICES_PUBLIC_VITAL'),
            'groupStoryAssignmentChoicesPublicMajor' => Yii::t('app', 'GROUP_STORY_ASSIGNMENT_CHOICES_PUBLIC_MAJOR'),
            'groupStoryAssignmentChoicesPublicMinor' => Yii::t('app', 'GROUP_STORY_ASSIGNMENT_CHOICES_PUBLIC_MINOR'),
            'groupStoryAssignmentChoicesPublicOther' => Yii::t('app', 'GROUP_STORY_ASSIGNMENT_CHOICES_PUBLIC_OTHER'),
            'groupStoryAssignmentChoicesPrivateVital' => Yii::t('app', 'GROUP_STORY_ASSIGNMENT_CHOICES_PRIVATE_VITAL'),
            'groupStoryAssignmentChoicesPrivateMajor' => Yii::t('app', 'GROUP_STORY_ASSIGNMENT_CHOICES_PRIVATE_MAJOR'),
            'groupStoryAssignmentChoicesPrivateMinor' => Yii::t('app', 'GROUP_STORY_ASSIGNMENT_CHOICES_PRIVATE_MINOR'),
            'groupStoryAssignmentChoicesPrivateOther' => Yii::t('app', 'GROUP_STORY_ASSIGNMENT_CHOICES_PRIVATE_OTHER'),
        ];
    }
}
