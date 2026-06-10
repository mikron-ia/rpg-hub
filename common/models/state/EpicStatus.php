<?php

namespace common\models\state;

use Yii;

enum EpicStatus: string
{
    use StatusCommons;

    case Proposed = 'proposed';       // idea is created; next: PREPARED, SCRAPPED
    case Planned = 'planning';        // epic is being planned; next: PREPARED, SCRAPPED
    case Prepared = 'preparation';    // epic is being prepared; next: READY, SCRAPPED
    case Ready = 'ready';             // epic is ready to run; next: PLAYED, SCRAPPED
    case Scrapped = 'scrapped';       // epic failed to achieve readiness; next: PLANNED, CLOSED
    case Cancelled = 'cancelled';     // epic ran but failed to complete; next: RESUMING, CLOSED
    case Played = 'played';           // in progress; next: LAPSED, ON HOLD, CANCELLED, FINISHED
    case Lapsed = 'lapsed';           // sessions stopped, but nothing was said yet; next: ON HOLD, CANCELLED, RESUMING
    case OnHold = 'on hold';         // epic was officially suspended; next: RESUMING, CANCELLED
    case Resuming = 'resuming';       // resuming after some trouble; next: PLAYED, ON HOLD
    case Finished = 'finished';       // epic was completed; next: CLOSED
    case Closed = 'closed';           // epic is documented and done; next: none

    public function getName(): string
    {
        return match ($this) {
            self::Cancelled => Yii::t('app', 'EPIC_STATUS_NAME_CANCELLED'),
            self::Closed => Yii::t('app', 'EPIC_STATUS_NAME_CLOSED'),
            self::Finished => Yii::t('app', 'EPIC_STATUS_NAME_FINISHED'),
            self::Lapsed => Yii::t('app', 'EPIC_STATUS_NAME_LAPSED'),
            self::OnHold => Yii::t('app', 'EPIC_STATUS_NAME_ON_HOLD'),
            self::Planned => Yii::t('app', 'EPIC_STATUS_NAME_PLANNED'),
            self::Prepared => Yii::t('app', 'EPIC_STATUS_NAME_PREPARED'),
            self::Played => Yii::t('app', 'EPIC_STATUS_NAME_PLAYED'),
            self::Proposed => Yii::t('app', 'EPIC_STATUS_NAME_PROPOSED'),
            self::Ready => Yii::t('app', 'EPIC_STATUS_NAME_READY'),
            self::Resuming => Yii::t('app', 'EPIC_STATUS_NAME_RESUMING'),
            self::Scrapped => Yii::t('app', 'EPIC_STATUS_NAME_SCRAPPED'),
        };
    }

    public function getClass(): string
    {
        return match ($this) {
            self::Cancelled => 'epic-status-cancelled',
            self::Closed => 'epic-status-closed',
            self::Finished => 'epic-status-finished',
            self::Lapsed => 'epic-status-lapsed',
            self::OnHold => 'epic-status-on-hold',
            self::Planned => 'epic-status-planned',
            self::Prepared => 'epic-status-prepared',
            self::Played => 'epic-status-played',
            self::Proposed => 'epic-status-proposed',
            self::Ready => 'epic-status-ready',
            self::Resuming => 'epic-status-resuming',
            self::Scrapped => 'epic-status-scrapped',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Cancelled => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_CANCELLED'),
            self::Closed => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_CLOSED'),
            self::Finished => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_FINISHED'),
            self::Lapsed => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_LAPSED'),
            self::OnHold => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_ON_HOLD'),
            self::Planned => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_PLANNED'),
            self::Prepared => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_PREPARED'),
            self::Played => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_PLAYED'),
            self::Proposed => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_PROPOSED'),
            self::Ready => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_READY'),
            self::Resuming => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_RESUMING'),
            self::Scrapped => Yii::t('app', 'EPIC_STATUS_DESCRIPTION_SCRAPPED'),
        };
    }

    public function getAllowedSuccessors(): array
    {
        return match ($this) {
            self::Cancelled => [self::Resuming, self::Cancelled, self::Closed],
            self::Closed => [self::Closed],
            self::Finished => [self::Closed, self::Finished],
            self::Lapsed => [
                self::OnHold,
                self::Cancelled,
                self::Lapsed,
                self::Resuming,
            ],
            self::OnHold => [self::Resuming, self::Cancelled, self::OnHold],
            self::Planned => [self::Prepared, self::Scrapped, self::Planned],
            self::Prepared => [self::Ready, self::Scrapped, self::Prepared],
            self::Played => [
                self::Played,
                self::Lapsed,
                self::OnHold,
                self::Cancelled,
                self::Finished,
            ],
            self::Proposed => [self::Proposed, self::Planned, self::Scrapped],
            self::Ready => [self::Ready, self::Played, self::Scrapped],
            self::Resuming => [self::Resuming, self::Played, self::OnHold],
            self::Scrapped => [self::Scrapped, self::Planned, self::Closed],
        };
    }

    /**
     * Provides sorting priorities based on status
     * Note: most important statuses have the lowest numbers
     *
     * @return int
     */
    public function getSortPriority(): int
    {
        return match ($this) {
            self::Played, self::Ready, self::Resuming => 0,
            self::Lapsed, self::Planned, self::Prepared => 1,
            self::OnHold, self::Proposed => 2,
            self::Cancelled, self::Finished, self::Scrapped => 3,
            self::Closed => 4,
        };
    }
}
