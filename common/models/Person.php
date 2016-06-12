<?php

namespace common\models;

use common\models\core\Visibility;
use common\models\tools\Tools;
use Yii;

/**
 * This is the model class for table "person".
 *
 * @property string $person_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $tagline
 * @property string $data
 * @property string $visibility
 * @property string $character_id
 * @property string $description_pack_id
 *
 * @property Epic $epic
 * @property Character $character
 * @property DescriptionPack $descriptionPack
 */
class Person extends \yii\db\ActiveRecord implements Displayable
{
    use Tools;

    const VISIBILITY_NONE = 'none';
    const VISIBILITY_LOGGED = 'logged';
    const VISIBILITY_GM = 'gm';
    const VISIBILITY_FULL = 'full';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'person';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epic_id', 'name', 'tagline', 'visibility'], 'required'],
            [['epic_id', 'character_id', 'description_pack_id'], 'integer'],
            [['data', 'visibility'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name', 'tagline'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['character_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Character::className(),
                'targetAttribute' => ['character_id' => 'character_id']
            ],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::className(),
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
            ],
            [
                ['visibility'],
                'in',
                'range' => function () {
                    return $this->allowedVisibilities();
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'person_id' => Yii::t('app', 'PERSON_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'PERSON_KEY'),
            'name' => Yii::t('app', 'PERSON_NAME'),
            'tagline' => Yii::t('app', 'PERSON_TAGLINE'),
            'data' => Yii::t('app', 'PERSON_DATA'),
            'visibility' => Yii::t('app', 'PERSON_VISIBILITY'),
            'character_id' => Yii::t('app', 'LABEL_CHARACTER'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
            $this->data = json_encode([]);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return string[]
     */
    static public function visibilityNames()
    {
        return [
            self::VISIBILITY_NONE => Yii::t('app', 'PERSON_VISIBILITY_NONE'),
            self::VISIBILITY_LOGGED => Yii::t('app', 'PERSON_VISIBILITY_LOGGED'),
            self::VISIBILITY_GM => Yii::t('app', 'PERSON_VISIBILITY_GM'),
            self::VISIBILITY_FULL => Yii::t('app', 'PERSON_VISIBILITY_FULL'),
        ];
    }

    public function allowedVisibilities()
    {
        return array_keys(self::visibilityNames());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacter()
    {
        return $this->hasOne(Character::className(), ['character_id' => 'character_id']);
    }

    public function getDescriptionPack()
    {
        return $this->hasOne(DescriptionPack::className(), ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @inheritdoc
     */
    public function getSimpleDataForApi()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            'tagline' => $this->tagline,
            'tags' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCompleteDataForApi()
    {
        $decodedData = json_decode($this->data, true);

        $decodedData['name'] = $this->name;
        $decodedData['key'] = $this->key;
        $decodedData['tagline'] = $this->tagline;

        if ($this->description_pack_id) {
            $descriptions = $this->descriptionPack->getCompleteDataForApi();
            $decodedData['descriptions'] = [];
            foreach ($descriptions as $description) {
                $decodedData['descriptions'][] = $description;
            }
        }

        return $decodedData;
    }

    /**
     * @return string|null
     */
    public function getVisibilityName()
    {
        $list = self::visibilityNames();
        if (isset($list[$this->visibility])) {
            return $list[$this->visibility];
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function isVisibleInApi()
    {
        return ($this->visibility === self::VISIBILITY_FULL);
    }
}
