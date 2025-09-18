<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\IsEditablePack;
use common\models\core\Visibility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "parameter_pack".
 *
 * @property string $parameter_pack_id
 * @property string $class
 * @property int $created_at
 * @property int $updated_at
 * @property string $parameters_full
 * @property string $parameters_gm
 *
 * @property Epic[] $epics
 * @property Parameter[] $parameters
 * @property Parameter[] $parametersOrdered
 * @property Story[] $stories
 * @property Epic $epic
 */
class ParameterPack extends ActiveRecord implements IsEditablePack
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parameter_pack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK_ID'),
            'class' => Yii::t('app', 'PARAMETER_PACK_CLASS'),
        ];
    }

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::class],
        ];
    }

    /**
     * @param string $className
     * @return ParameterPack Generated pack
     */
    static public function create($className)
    {
        $pack = new ParameterPack();
        $pack->class = $className;

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    public function updateSearchableFields(): void
    {
        $parametersFull = [];
        $parametersGM = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter->visibility === Visibility::VISIBILITY_FULL->value) {
                $parametersFull[$parameter->code] = $parameter->content;
            }

            $parametersGM[$parameter->code] = $parameter->content;
        }

        $this->parameters_full = json_encode($parametersFull);
        $this->parameters_gm = json_encode($parametersGM);

        $this->save();
    }

    /**
     * @return ActiveQuery
     */
    public function getEpics()
    {
        return $this->hasMany(Epic::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParameters()
    {
        return $this->hasMany(Parameter::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParametersOrdered()
    {
        return $this
            ->hasMany(Parameter::class, ['parameter_pack_id' => 'parameter_pack_id'])
            ->orderBy(['position' => SORT_ASC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    /**
     * @return HasEpicControl
     */
    public function getControllingObject()
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        return ($className)::findOne(['parameter_pack_id' => $this->parameter_pack_id]);
    }

    /**
     * @return Epic
     */
    public function getEpic(): Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    /**
     * @param string $code
     * @return null|Parameter
     */
    public function getParameterByCode($code)
    {
        return Parameter::findOne([
            'parameter_pack_id' => $this->parameter_pack_id,
            'code' => $code
        ]);
    }

    /**
     * @param $code
     * @return null|string
     */
    public function getParameterValueByCode($code)
    {
        $parameter = $this->getParameterByCode($code);

        if ($parameter) {
            return $parameter->content;
        } else {
            return null;
        }
    }

    public function canUserReadYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['parameter_pack_id' => $this->parameter_pack_id]);
        return $object->canUserViewYou();
    }

    public function canUserControlYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['parameter_pack_id' => $this->parameter_pack_id]);
        return $object->canUserControlYou();
    }
}
