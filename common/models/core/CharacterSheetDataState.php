<?php

namespace common\models\core;

use Yii;

enum CharacterSheetDataState: string
{
    case Incomplete = 'incomplete'; // has to be finished and should not be relayed on; used mostly during creation stage
    case Ready = 'ready'; // finished, up to date, and can be relied on
    case NeedsConfirmation = 'confirm'; // perhaps up to date, perhaps not; should be checked
    case NeedsFix = 'fix'; // incorrect, needs to be corrected, once corrected, may need update or is ready
    case NeedsUpdate = 'change'; // a known change happened and needs to be applied, but is otherwise correct
    case Unknown = 'unknown'; // error state for cases that have been neglected or damaged
    case Frozen = 'frozen'; // indicating it will not be changed for a while
    case Closed = 'closed'; // indicating it will not change anymore

    public function getName(): string
    {
        return match ($this) {
            self::Incomplete => Yii::t('app', 'CHARACTER_SHEET_STATUS_INCOMPLETE'),
            self::Ready => Yii::t('app', 'CHARACTER_SHEET_STATUS_READY'),
            self::NeedsConfirmation => Yii::t('app', 'CHARACTER_SHEET_STATUS_NEEDS_CONFIRMATION'),
            self::NeedsFix => Yii::t('app', 'CHARACTER_SHEET_STATUS_NEEDS_FIX'),
            self::NeedsUpdate => Yii::t('app', 'CHARACTER_SHEET_STATUS_NEEDS_UPDATE'),
            self::Unknown => Yii::t('app', 'CHARACTER_SHEET_STATUS_UNKNOWN'),
            self::Frozen => Yii::t('app', 'CHARACTER_SHEET_STATUS_FROZEN'),
            self::Closed => Yii::t('app', 'CHARACTER_SHEET_STATUS_CLOSED'),
        };
    }

    public function getClass(): string
    {
        return match ($this) {
            self::Incomplete => 'view-state-tag-incomplete',
            self::Ready => 'view-state-tag-ready',
            self::NeedsConfirmation => 'view-state-tag-confirm',
            self::NeedsFix => 'view-state-tag-fix',
            self::NeedsUpdate => 'view-state-tag-update',
            self::Unknown => 'view-state-tag-unknown',
            self::Frozen => 'view-state-tag-frozen',
            self::Closed => 'view-state-tag-closed',
        };
    }

    /**
     * @return array<int,CharacterSheetDataState>
     */
    public function allowedSuccessors(): array
    {
        return match ($this) {
            self::Incomplete => [
                self::Incomplete,
                self::Ready,
                self::Unknown,
                self::Closed,
            ],
            self::Ready,
            self::NeedsConfirmation => [
                self::Ready,
                self::NeedsConfirmation,
                self::NeedsFix,
                self::NeedsUpdate,
                self::Unknown,
                self::Frozen,
            ],
            self::NeedsFix => [
                self::Ready,
                self::NeedsFix,
                self::NeedsUpdate,
                self::Unknown,
                self::Frozen,
            ],
            self::NeedsUpdate => [
                self::Ready,
                self::NeedsUpdate,
                self::Unknown,
                self::Frozen,
            ],
            self::Unknown => [
                self::Ready,
                self::NeedsConfirmation,
                self::NeedsFix,
                self::NeedsUpdate,
                self::Unknown,
                self::Frozen,
                self::Closed,
            ],
            self::Frozen => [
                self::NeedsFix,
                self::NeedsUpdate,
                self::Unknown,
                self::Frozen,
                self::Closed,
            ],
            self::Closed => [
                self::Unknown,
                self::Closed,
            ],
        };
    }

    /**
     * @return array<string,string>
     */
    public function allowedSuccessorsAsKeys(): array
    {
        return array_map(function (self $status) {
            return $status->value;
        }, $this->allowedSuccessors());
    }

    /**
     * @return array<string,string>
     */
    public function allowedSuccessorsAsStrings(): array
    {
        $allowed = [];
        foreach ($this->allowedSuccessors() as $successor) {
            $allowed[$successor->value] = $successor->getName();
        }

        return $allowed;
    }
}
