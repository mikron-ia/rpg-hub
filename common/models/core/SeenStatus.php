<?php

namespace common\models\core;

enum SeenStatus: string
{
    case STATUS_NEW = 'new';
    case STATUS_UPDATED = 'updated';
    case STATUS_SEEN = 'seen';

    public function statusCSS(): string
    {
        return match ($this) {
            self::STATUS_NEW => 'seen-tag-new',
            self::STATUS_UPDATED => 'seen-tag-updated',
            self::STATUS_SEEN => 'seen-tag-seen',
        };
    }

    public function isNewerThanMe(self $newcomer): bool
    {
        return match ($this) {
            self::STATUS_NEW => false,
            self::STATUS_UPDATED => $newcomer === self::STATUS_NEW,
            self::STATUS_SEEN => $newcomer === self::STATUS_UPDATED || $newcomer === self::STATUS_NEW,
        };
    }

    public function isOlderThanMe(self $newcomer): bool
    {
        return match ($this) {
            self::STATUS_NEW => $newcomer === self::STATUS_SEEN || $newcomer === self::STATUS_UPDATED,
            self::STATUS_UPDATED => $newcomer === self::STATUS_SEEN,
            self::STATUS_SEEN => false,
        };
    }
}
