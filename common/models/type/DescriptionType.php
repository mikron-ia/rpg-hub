<?php

namespace common\models\type;

use Yii;

enum DescriptionType: string
{
    case Appearance = 'appearance';       // For Character, Location; The looks
    case Aspects = 'aspects';             // For Character, Group, Location, Scenario, Story; Aspects (for FATE-like games) and Moves (for Powered by Apocalypse games)
    case Attitude = 'attitude';           // For Character, Group; Attitude towards different people / groups and connections with them
    case Background = 'background';       // For Character, Group, Story; Origin, education, the like
    case Commentary = 'commentary';       // For Character, Group, Location, Story; GM commentary
    case Domain = 'domain';               // For Character, Group; Places where the person reigns, dominates, or frequents
    case Factions = 'factions';           // For Character, Group, Location; Factions associated with; this includes nations
    case History = 'history';             // For Character, Group, Location; History of the character/group/location
    case Interactions = 'interactions';   // For Character, Group, Location; Interactions / encounters with the group or person NAMES
    case Personality = 'personality';     // For Character; Personality, character behaviour, mental issues
    case Resources = 'resources';         // For Character, Group, Location; Resources the person/group/location wields, flaunts, or can offer
    case Reputation = 'reputation';       // For Character, Group, Location; Reputation of the character/group/location
    case Retinue = 'retinue';             // For Character, Group, Location; Friends, allies, etc.; for Location, it's mostly about personnel
    case Rumours = 'rumours';             // For Character, Group, Location; Unproven rumours collected about character/group/location
    case Stories = 'stories';             // For Character, Group, Location; Stories participated in
    case Threads = 'threads';             // For Character, Group, Location, Scenario, Story; Threads attached
    case Who = 'who';                     // For Character, Group, Location; Who/what is this?

    case Structure = 'structure';         // For Group: what is the structure and basic workings?

    case Location = 'location';           // For Location: where is it?

    case Premise = 'premise';             // For Scenario, Story; what is the main concept?
    case Actors = 'actors';               // For Scenario, Story; who is going to participate?
    case Plan = 'plan';                   // For Scenario; what is going to happen?
    case Scene = 'scene';                 // For Scenario, Story; a particular scene
    case Act = 'act';                     // For Scenario, Story; a particular act
    case Briefing = 'briefing';           // For Scenario, Story; briefing / introduction scene
    case Debriefing = 'debriefing';       // For Scenario, Story; debriefing / aftermath scene
    case Prelude = 'prelude';             // For Scenario, Story; events leading to or introducing
    case Interlude = 'interlude';         // For Scenario, Story; events in-between
    case Postlude = 'postlude';           // For Scenario, Story; events following

    public function name(): string
    {
        return match ($this) {
            self::Appearance => Yii::t('app', 'DESCRIPTION_TYPE_APPEARANCE'),
            self::Aspects => Yii::t('app', 'DESCRIPTION_TYPE_ASPECTS'),
            self::Attitude => Yii::t('app', 'DESCRIPTION_TYPE_ATTITUDE'),
            self::Background => Yii::t('app', 'DESCRIPTION_TYPE_BACKGROUND'),
            self::Commentary => Yii::t('app', 'DESCRIPTION_TYPE_COMMENTARY'),
            self::Domain => Yii::t('app', 'DESCRIPTION_TYPE_DOMAIN'),
            self::Factions => Yii::t('app', 'DESCRIPTION_TYPE_FACTIONS'),
            self::Interactions => Yii::t('app', 'DESCRIPTION_TYPE_INTERACTIONS'),
            self::History => Yii::t('app', 'DESCRIPTION_TYPE_HISTORY'),
            self::Personality => Yii::t('app', 'DESCRIPTION_TYPE_PERSONALITY'),
            self::Resources => Yii::t('app', 'DESCRIPTION_TYPE_RESOURCES'),
            self::Reputation => Yii::t('app', 'DESCRIPTION_TYPE_REPUTATION'),
            self::Retinue => Yii::t('app', 'DESCRIPTION_TYPE_RETINUE'),
            self::Rumours => Yii::t('app', 'DESCRIPTION_TYPE_RUMOURS'),
            self::Stories => Yii::t('app', 'DESCRIPTION_TYPE_STORIES'),
            self::Threads => Yii::t('app', 'DESCRIPTION_TYPE_THREADS'),
            self::Who => Yii::t('app', 'DESCRIPTION_TYPE_WHO'),
            self::Structure => Yii::t('app', 'DESCRIPTION_TYPE_STRUCTURE'),
            self::Location => Yii::t('app', 'DESCRIPTION_TYPE_LOCATION'),
            self::Premise => Yii::t('app', 'DESCRIPTION_TYPE_PREMISE'),
            self::Actors => Yii::t('app', 'DESCRIPTION_TYPE_ACTORS'),
            self::Plan => Yii::t('app', 'DESCRIPTION_TYPE_PLAN'),
            self::Scene => Yii::t('app', 'DESCRIPTION_TYPE_SCENE'),
            self::Act => Yii::t('app', 'DESCRIPTION_TYPE_ACT'),
            self::Briefing => Yii::t('app', 'DESCRIPTION_TYPE_BRIEFING'),
            self::Debriefing => Yii::t('app', 'DESCRIPTION_TYPE_DEBRIEFING'),
            self::Prelude => Yii::t('app', 'DESCRIPTION_TYPE_PRELUDE'),
            self::Interlude => Yii::t('app', 'DESCRIPTION_TYPE_INTERLUDE'),
            self::Postlude => Yii::t('app', 'DESCRIPTION_TYPE_POSTLUDE'),
        };
    }

    /**
     * @return array<string>
     *
     * This method intentionally allows all possible types without limiting them by controlling object's class, because
     * if somebody is crafty and determined enough to successfully use a disallowed type, we should let them.
     * Especially that it does not break anything important. It only limits to existing types in order not to break
     * the display.
     */
    public static function allowedTypesForValidator(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }

    /**
     * @param array<DescriptionType> $typesAllowed
     *
     * @return array<string,string>
     */
    public static function typeNames(array $typesAllowed): array
    {
        $typeNamesAccepted = [];
        foreach ($typesAllowed as $type) {
            $typeNamesAccepted[$type->value] = $type->name();
        }

        return $typeNamesAccepted;
    }

    public static function namesForDropdown(): array
    {
        return self::typeNames(self::cases());
    }
}
