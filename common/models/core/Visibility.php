<?php

namespace common\models\core;

use common\models\Epic;
use common\models\Participant;
use common\models\ParticipantRole;
use Yii;

enum Visibility: string
{
    case VISIBILITY_NONE = 'none';
    case VISIBILITY_GM = 'gm';
    case VISIBILITY_DESIGNATED = 'designated';
    case VISIBILITY_LOGGED = 'logged';
    case VISIBILITY_FULL = 'full';

    public const allowedVisibilities =  [
        //Visibility::VISIBILITY_NONE,
        Visibility::VISIBILITY_GM,
        //Visibility::VISIBILITY_DESIGNATED,
        //Visibility::VISIBILITY_LOGGED,
        Visibility::VISIBILITY_FULL
    ];

    /**
     * Determines range of accessible objects for the user
     *
     * @return array<int,string>
     */
    static public function determineVisibilityVector(Epic $epic): array
    {
        if (Yii::$app->user->isGuest) {
            /* No epic and no user makes bad business */
            $visibilityVector = [];
        } else {
            $visibilityVector = [Visibility::VISIBILITY_FULL->value, Visibility::VISIBILITY_LOGGED->value];

            if (Participant::participantHasRole(Yii::$app->user->identity, $epic, ParticipantRole::ROLE_GM)) {
                $visibilityVector[] = Visibility::VISIBILITY_GM->value;
                $visibilityVector[] = Visibility::VISIBILITY_DESIGNATED->value;
            }
        }

        return $visibilityVector;
    }

    public function getName(): ?string
    {
        return self::visibilityNames(self::allowedVisibilities)[$this->value] ?? null;
    }

    /**
     * Provides visibilities names
     *
     * @param array<int,Visibility> $allowed
     *
     * @return array<string,string>
     */
    static public function visibilityNames(array $allowed): array
    {
        return self::filterNames($allowed, [
            Visibility::VISIBILITY_NONE->value => Yii::t('app', 'VISIBILITY_NONE'),
            Visibility::VISIBILITY_GM->value => Yii::t('app', 'VISIBILITY_GM'),
            Visibility::VISIBILITY_DESIGNATED->value => Yii::t('app', 'VISIBILITY_DESIGNATED'),
            Visibility::VISIBILITY_LOGGED->value => Yii::t('app', 'VISIBILITY_LOGGED'),
            Visibility::VISIBILITY_FULL->value => Yii::t('app', 'VISIBILITY_FULL'),
        ]);
    }


    /**
     * Provides visibility name in lowercase
     *
     * @return string|null
     */
    public function getNameLowercase(): ?string
    {
        return self::visibilityNamesLowercase(self::allowedVisibilities)[$this->value] ?? null;
    }

    /**
     * Provides visibilities names in lowercase
     *
     * @return array<string,string>
     */
    static public function visibilityNamesLowercase($allowed): array
    {
        return self::filterNames($allowed, [
            Visibility::VISIBILITY_NONE->value => Yii::t('app', 'VISIBILITY_NONE_LOWERCASE'),
            Visibility::VISIBILITY_GM->value => Yii::t('app', 'VISIBILITY_GM_LOWERCASE'),
            Visibility::VISIBILITY_DESIGNATED->value => Yii::t('app', 'VISIBILITY_DESIGNATED_LOWERCASE'),
            Visibility::VISIBILITY_LOGGED->value => Yii::t('app', 'VISIBILITY_LOGGED_LOWERCASE'),
            Visibility::VISIBILITY_FULL->value => Yii::t('app', 'VISIBILITY_FULL_LOWERCASE'),
        ]);
    }

    static private function filterNames(array $allowed, array $names): array
    {
        $allowedArray = array_map(function (Visibility $visibility) {
            return $visibility->value;
        }, $allowed);

        foreach ($names as $key => $name) {
            if (!in_array($key, $allowedArray)) {
                unset($names[$key]);
            }
        }

        return $names;
    }
}
