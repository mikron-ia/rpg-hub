<?php

namespace common\models\core;

use common\models\Epic;
use common\models\Participant;
use common\models\ParticipantRole;
use Yii;

/**
 * Class Visibility
 * @package common\models\tools
 */
final class Visibility
{
    const VISIBILITY_NONE = 'none';
    const VISIBILITY_GM = 'gm';
    const VISIBILITY_DESIGNATED = 'designated';
    const VISIBILITY_LOGGED = 'logged';
    const VISIBILITY_FULL = 'full';

    /**
     * @var string
     */
    public $visibility;

    /**
     * @param $code
     * @return Visibility
     */
    static public function create($code): Visibility
    {
        $visibility = new Visibility();
        $visibility->visibility = $code;
        return $visibility;
    }

    /**
     * Determines range of accessible objects for the user
     * @param Epic $epic
     * @return array|\string[]
     */
    static public function determineVisibilityVector(Epic $epic): array
    {
        if (empty($epic) || Yii::$app->user->isGuest) {
            /* No epic and no user makes bad business */
            $visibilityVector = [];
        } else {
            $visibilityVector = [Visibility::VISIBILITY_FULL, Visibility::VISIBILITY_LOGGED];

            if (Participant::participantHasRole(
                Yii::$app->user->identity,
                $epic,
                ParticipantRole::ROLE_GM
            )
            ) {
                $visibilityVector[] = Visibility::VISIBILITY_GM;
                $visibilityVector[] = Visibility::VISIBILITY_DESIGNATED;
            }
        }

        return $visibilityVector;
    }

    /**
     * Provides visibility name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        $names = self::visibilityNames(self::allowedVisibilities());
        return $names[$this->visibility] ?? null;
    }

    /**
     * Provides visibilities names
     *
     * @param array $allowed
     *
     * @return string[]
     */
    static public function visibilityNames(array $allowed): array
    {
        $names = [
            Visibility::VISIBILITY_NONE => Yii::t('app', 'VISIBILITY_NONE'),
            Visibility::VISIBILITY_GM => Yii::t('app', 'VISIBILITY_GM'),
            Visibility::VISIBILITY_DESIGNATED => Yii::t('app', 'VISIBILITY_DESIGNATED'),
            Visibility::VISIBILITY_LOGGED => Yii::t('app', 'VISIBILITY_LOGGED'),
            Visibility::VISIBILITY_FULL => Yii::t('app', 'VISIBILITY_FULL'),
        ];

        foreach ($names as $key => $name) {
            if (!in_array($key, $allowed)) {
                unset($names[$key]);
            }
        }

        return $names;
    }

    /**
     * Lists allowed visibilities
     *
     * @return string[]
     */
    static public function allowedVisibilities(): array
    {
        return [
            //Visibility::VISIBILITY_NONE,
            Visibility::VISIBILITY_GM,
            //Visibility::VISIBILITY_DESIGNATED,
            //Visibility::VISIBILITY_LOGGED,
            Visibility::VISIBILITY_FULL
        ];
    }

    /**
     * Provides visibility name in lowercase
     *
     * @return string|null
     */
    public function getNameLowercase(): ?string
    {
        $names = self::visibilityNamesLowercase();
        return $names[$this->visibility] ?? null;
    }

    /**
     * Provides visibilities names in lowercase
     * @return string[]
     */
    static public function visibilityNamesLowercase(): array
    {
        return [
            Visibility::VISIBILITY_NONE => Yii::t('app', 'VISIBILITY_NONE_LOWERCASE'),
            Visibility::VISIBILITY_GM => Yii::t('app', 'VISIBILITY_GM_LOWERCASE'),
            Visibility::VISIBILITY_DESIGNATED => Yii::t('app', 'VISIBILITY_DESIGNATED_LOWERCASE'),
            Visibility::VISIBILITY_LOGGED => Yii::t('app', 'VISIBILITY_LOGGED_LOWERCASE'),
            Visibility::VISIBILITY_FULL => Yii::t('app', 'VISIBILITY_FULL_LOWERCASE'),
        ];
    }
}
