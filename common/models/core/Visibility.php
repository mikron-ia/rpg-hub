<?php

namespace common\models\core;

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

    public $visibility;

    /**
     * @return string[]
     */
    static public function visibilityNames():array
    {
        $names = [
            Visibility::VISIBILITY_NONE => Yii::t('app', 'VISIBILITY_NONE'),
            Visibility::VISIBILITY_GM => Yii::t('app', 'VISIBILITY_GM'),
            Visibility::VISIBILITY_DESIGNATED => Yii::t('app', 'VISIBILITY_DESIGNATED'),
            Visibility::VISIBILITY_LOGGED => Yii::t('app', 'VISIBILITY_LOGGED'),
            Visibility::VISIBILITY_FULL => Yii::t('app', 'VISIBILITY_FULL'),
        ];

        $allowed = self::allowedVisibilities();

        foreach ($names as $key => $name) {
            if (!in_array($key, $allowed)) {
                unset($names[$key]);
            }
        }

        return $names;
    }

    /**
     * @return string[]
     */
    static public function visibilityNamesLowercase():array
    {
        return [
            Visibility::VISIBILITY_NONE => Yii::t('app', 'VISIBILITY_NONE_LOWERCASE'),
            Visibility::VISIBILITY_GM => Yii::t('app', 'VISIBILITY_GM_LOWERCASE'),
            Visibility::VISIBILITY_DESIGNATED => Yii::t('app', 'VISIBILITY_DESIGNATED_LOWERCASE'),
            Visibility::VISIBILITY_LOGGED => Yii::t('app', 'VISIBILITY_LOGGED_LOWERCASE'),
            Visibility::VISIBILITY_FULL => Yii::t('app', 'VISIBILITY_FULL_LOWERCASE'),
        ];
    }

    /**
     * @return string[]
     */
    static public function allowedVisibilities():array
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
     * @param $code
     * @return Visibility
     */
    static public function create($code):Visibility
    {
        $visibility = new Visibility();
        $visibility->visibility = $code;
        return $visibility;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        $names = self::visibilityNames();
        return isset($names[$this->visibility]) ? $names[$this->visibility] : null;
    }

    /**
     * @return string|null
     */
    public function getNameLowercase()
    {
        $names = self::visibilityNamesLowercase();
        return isset($names[$this->visibility]) ? $names[$this->visibility] : null;
    }

    /**
     * Determines range of accessible objects for the user
     * @return string[]
     */
    static public function determineVisibilityVector():array
    {
        if (empty(Yii::$app->params['activeEpic']) || Yii::$app->user->isGuest) {
            /* No epic and no user makes bad business */
            $visibilityVector = [];
        } else {
            $visibilityVector = [Visibility::VISIBILITY_FULL, Visibility::VISIBILITY_LOGGED];

            if (Participant::participantHasRole(
                Yii::$app->user->identity,
                Yii::$app->params['activeEpic'],
                ParticipantRole::ROLE_GM
            )
            ) {
                $visibilityVector[] = Visibility::VISIBILITY_GM;
                $visibilityVector[] = Visibility::VISIBILITY_DESIGNATED;
            }
        }

        return $visibilityVector;
    }
}
