<?php

namespace common\models\state;

use backend\models\dto\SimpleActionButton;
use Yii;

enum GameStatus: string
{
    case Proposed = 'proposed';       // game was entered on page; next: ANNOUNCED, PLANNED, UNPLANNED
    case Announced = 'announced';     // information was propagated; next: PLANNED, UNPLANNED
    case Unplanned = 'unplanned';     // game failed to achieve planning stage; next: none
    case Planned = 'planned';         // game is planned; next: PROGRESSING, CANCELLED
    case Cancelled = 'cancelled';     // plans cancelled; next: none
    case Progressing = 'progressing'; // game is in progress; next: COMPLETED, ABORTED
    case Aborted = 'aborted';         // game was started but aborted; next: none
    case Completed = 'completed';     // game was completed; next: CLOSED
    case Closed = 'closed';           // game was described; next: none

    public function getName(): string
    {
        return match ($this) {
            self::Proposed => Yii::t('app', 'GAME_STATUS_LABEL_PROPOSED'),
            self::Announced => Yii::t('app', 'GAME_STATUS_LABEL_ANNOUNCED'),
            self::Unplanned => Yii::t('app', 'GAME_STATUS_LABEL_UNPLANNED'),
            self::Planned => Yii::t('app', 'GAME_STATUS_LABEL_PLANNED'),
            self::Cancelled => Yii::t('app', 'GAME_STATUS_LABEL_CANCELLED'),
            self::Progressing => Yii::t('app', 'GAME_STATUS_LABEL_PROGRESSING'),
            self::Aborted => Yii::t('app', 'GAME_STATUS_LABEL_ABORTED'),
            self::Completed => Yii::t('app', 'GAME_STATUS_LABEL_COMPLETED'),
            self::Closed => Yii::t('app', 'GAME_STATUS_LABEL_CLOSED'),
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Proposed => Yii::t('app', 'GAME_STATUS_DESCRIPTION_PROPOSED'),
            self::Announced => Yii::t('app', 'GAME_STATUS_DESCRIPTION_ANNOUNCED'),
            self::Unplanned => Yii::t('app', 'GAME_STATUS_DESCRIPTION_UNPLANNED'),
            self::Planned => Yii::t('app', 'GAME_STATUS_DESCRIPTION_PLANNED'),
            self::Cancelled => Yii::t('app', 'GAME_STATUS_DESCRIPTION_CANCELLED'),
            self::Progressing => Yii::t('app', 'GAME_STATUS_DESCRIPTION_PROGRESSING'),
            self::Aborted => Yii::t('app', 'GAME_STATUS_DESCRIPTION_ABORTED'),
            self::Completed => Yii::t('app', 'GAME_STATUS_DESCRIPTION_COMPLETED'),
            self::Closed => Yii::t('app', 'GAME_STATUS_DESCRIPTION_CLOSED'),
        };
    }

    public function getSwitchToButtonText(): string
    {
        return match ($this) {
            self::Proposed => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_PROPOSED'),
            self::Announced => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_ANNOUNCED'),
            self::Unplanned => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_UNPLANNED'),
            self::Planned => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_PLANNED'),
            self::Cancelled => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_CANCELLED'),
            self::Progressing => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_PROGRESSING'),
            self::Aborted => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_ABORTED'),
            self::Completed => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_COMPLETED'),
            self::Closed => Yii::t('app', 'GAME_STATUS_SWITCH_TO_TEXT_CLOSED'),
        };
    }

    public function getClass(): string
    {
        return match ($this) {
            self::Proposed => 'game-status-proposed',
            self::Announced => 'game-status-announced',
            self::Unplanned => 'game-status-unplanned',
            self::Planned => 'game-status-planned',
            self::Cancelled => 'game-status-cancelled',
            self::Progressing => 'game-status-progressing',
            self::Aborted => 'game-status-aborted',
            self::Completed => 'game-status-completed',
            self::Closed => 'game-status-closed',
        };
    }

    public function getAllowedSuccessors(): array
    {
        return match ($this) {
            self::Proposed => [self::Announced, self::Planned, self::Proposed, self::Unplanned],
            self::Announced => [self::Announced, self::Planned, self::Unplanned],
            self::Unplanned => [self::Unplanned],
            self::Planned => [self::Planned, self::Progressing, self::Cancelled],
            self::Cancelled => [self::Cancelled],
            self::Progressing => [self::Progressing, self::Completed, self::Aborted],
            self::Aborted => [self::Aborted],
            self::Completed => [self::Completed, self::Closed],
            self::Closed => [self::Closed],
        };
    }

    /**
     * @return array<string,string>
     */
    public function allowedSuccessorsAsKeys(): array
    {
        return array_map(function (self $status) {
            return $status->value;
        }, $this->getAllowedSuccessors());
    }

    /**
     * @return array<string,string>
     */
    public function allowedSuccessorsAsStrings(): array
    {
        $allowed = [];
        foreach ($this->getAllowedSuccessors() as $successor) {
            $allowed[$successor->value] = $successor->getName();
        }

        return $allowed;
    }

    /**
     * @return array<SimpleActionButton>
     */
    public function allowedSuccessorsAsActionButtons(string $objectKey): array
    {
        $button = [];
        foreach ($this->getAllowedSuccessors() as $successor) {
            if ($successor !== $this) {
                $button[] = new SimpleActionButton(
                    text: $successor->getSwitchToButtonText(),
                    explanation: $successor->getDescription(),
                    confirmation: Yii::t(
                        'app',
                        'GAME_STATUS_CHANGE_CONFIRMATION {target}',
                        ['target' => strtolower($successor->getName())]
                    ),
                    controller: 'game',
                    action: 'switch-state',
                    command: $successor->value,
                    key: $objectKey
                );
            }
        }

        return $button;
    }

    /**
     * @return array<string,string>
     */
    public static function namesForDropdown(): array
    {
        return array_reduce(
            self::cases(),
            static function (array $names, self $type): array {
                $names[$type->value] = $type->getName();
                return $names;
            },
            []
        );
    }
}
