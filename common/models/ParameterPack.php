<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\IsEditablePack;
use common\models\core\Visibility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

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
    public static function tableName(): string
    {
        return 'parameter_pack';
    }

    public function rules(): array
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK_ID'),
            'class' => Yii::t('app', 'PARAMETER_PACK_CLASS'),
        ];
    }

    public function behaviors(): array
    {
        return [
            ['class' => TimestampBehavior::class],
        ];
    }

    /**
     * @throws Exception
     */
    static public function create(string $className): ParameterPack
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

    public function getEpics(): ActiveQuery
    {
        return $this->hasMany(Epic::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    public function getParameters(): ActiveQuery
    {
        return $this->hasMany(Parameter::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    public function getParametersOrdered(): ActiveQuery
    {
        return $this
            ->hasMany(Parameter::class, ['parameter_pack_id' => 'parameter_pack_id'])
            ->orderBy(['position' => SORT_ASC]);
    }

    public function getStories(): ActiveQuery
    {
        return $this->hasMany(Story::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    public function getControllingObject(): HasEpicControl
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        return ($className)::findOne(['parameter_pack_id' => $this->parameter_pack_id]);
    }

    public function getEpic(): Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    public function getParameterByCode(string $code): ?Parameter
    {
        return Parameter::findOne([
            'parameter_pack_id' => $this->parameter_pack_id,
            'code' => $code
        ]);
    }

    public function getParameterValueByCode(string $code): ?string
    {
        return $this->getParameterByCode($code)?->content;
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
