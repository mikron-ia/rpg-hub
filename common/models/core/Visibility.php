<?php

namespace common\models\core;

use common\models\Epic;
use common\models\Participant;
use common\models\ParticipantRole;
use Yii;

enum Visibility: string
{
    case None = 'none';
    case GameMaster = 'gm';
    case Designated = 'designated';
    case LoggedIn = 'logged';
    case Full = 'full';

    public const array allowedVisibilities = [
        Visibility::None,
        Visibility::GameMaster,
        Visibility::Designated,
        Visibility::LoggedIn,
        Visibility::Full,
    ];

    /**
     * Determines range of accessible objects for the user
     * This method is guaranteed to return only safe objects
     *
     * @return array<int,string>
     */
    public static function determineVisibilityVector(Epic $epic): array
    {
        return array_map(
            fn(Visibility $visibility): string => $visibility->value,
            self::determineVisibilityVectorWithObjects($epic)
        );
    }

    /**
     * Determines range of accessible objects for the user, allowing for unsafe (potentially secret) objects
     * The results should be filtered further to remove those
     *
     * @return array<int,string>
     */
    public static function determineUnsafeVisibilityVector(Epic $epic): array
    {
        return array_map(
            fn(Visibility $visibility): string => $visibility->value,
            self::determineUnsafeVisibilityVectorWithObjects($epic)
        );
    }

    /**
     * @return array<int,Visibility>
     */
    public static function determineVisibilityVectorWithObjects(Epic $epic): array
    {
        if (Yii::$app->user->isGuest) {
            /* No epic and no user makes bad business */
            $visibilityVector = [];
        } else {
            $visibilityVector = [
                Visibility::Full,
                Visibility::LoggedIn,
            ];

            if (Participant::participantHasRole(Yii::$app->user->identity, $epic, ParticipantRole::ROLE_GM)) {
                $visibilityVector[] = Visibility::GameMaster;
                $visibilityVector[] = Visibility::Designated;
            }
        }

        return $visibilityVector;
    }

    /**
     * @return array<int,string>
     */
    public static function determineUnsafeVisibilityVectorWithObjects(Epic $epic): array
    {
        if (Yii::$app->user->isGuest) {
            /* No epic and no user makes bad business */
            $visibilityVector = [];
        } else {
            $visibilityVector = [
                Visibility::Full,
                Visibility::LoggedIn,
                Visibility::Designated, // the source of unsafety
            ];

            if (Participant::participantHasRole(Yii::$app->user->identity, $epic, ParticipantRole::ROLE_GM)) {
                $visibilityVector[] = Visibility::GameMaster;
            }
        }

        return $visibilityVector;
    }

    public function getName(): ?string
    {
        return self::visibilityNames(self::allowedVisibilities)[$this->value] ?? null;
    }

    /**
     * Provides names for visibilities
     *
     * @param array<int,Visibility> $allowed
     *
     * @return array<string,string>
     */
    static public function visibilityNames(array $allowed): array
    {
        return self::filterNames($allowed, [
            Visibility::None->value => Yii::t('app', 'VISIBILITY_NONE'),
            Visibility::GameMaster->value => Yii::t('app', 'VISIBILITY_GM'),
            Visibility::Designated->value => Yii::t('app', 'VISIBILITY_DESIGNATED'),
            Visibility::LoggedIn->value => Yii::t('app', 'VISIBILITY_LOGGED'),
            Visibility::Full->value => Yii::t('app', 'VISIBILITY_FULL'),
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
     * Provides names for visibilities in lowercase
     *
     * @return array<string,string>
     */
    public static function visibilityNamesLowercase($allowed): array
    {
        return self::filterNames($allowed, [
            Visibility::None->value => Yii::t('app', 'VISIBILITY_NONE_LOWERCASE'),
            Visibility::GameMaster->value => Yii::t('app', 'VISIBILITY_GM_LOWERCASE'),
            Visibility::Designated->value => Yii::t('app', 'VISIBILITY_DESIGNATED_LOWERCASE'),
            Visibility::LoggedIn->value => Yii::t('app', 'VISIBILITY_LOGGED_LOWERCASE'),
            Visibility::Full->value => Yii::t('app', 'VISIBILITY_FULL_LOWERCASE'),
        ]);
    }

    private static function filterNames(array $allowed, array $names): array
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
