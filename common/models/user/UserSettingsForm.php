<?php

namespace common\models\user;

use common\models\core\Language;
use common\models\core\UserStatus;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;

final class UserSettingsForm extends Model
{
    private ?User $user;

    public string $email;

    public string $language;

    public string $username;

    public function __construct($config = [])
    {
        $this->user = Yii::$app->user->identity;
        $this->language = $this->user->language;
        $this->email = $this->user->email;
        $this->username = $this->user->username;

        parent::__construct($config);
    }

    /**
     * @return array<string,string>
     */
    final public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'USER_EMAIL'),
            'language' => Yii::t('app', 'USER_LANGUAGE'),
            'username' => Yii::t('app', 'USER_USERNAME'),
        ];
    }

    public function rules(): array
    {
        return [
            ['language', 'in', 'range' => Language::supportedLanguages()],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            [
                'username',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('app', 'USER_CREATION_USERNAME_TAKEN'),
                'filter' => function (ActiveQuery $query) {
                    $query->andWhere(['=', 'status', UserStatus::Active->value]);
                    $query->andWhere(['<>', 'id', $this->user->id]);
                },
            ],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('app', 'USER_CREATION_EMAIL_TAKEN'),
                'filter' => function (ActiveQuery $query) {
                    $query->andWhere(['=', 'status', UserStatus::Active->value]);
                    $query->andWhere(['<>', 'id', $this->user->id]);
                },
            ],
        ];
    }

    public function save(): bool
    {
        if ($this->validate()) {
            $this->user->language = $this->language;
            $this->user->email = $this->email;
            $this->user->username = $this->username;

            $resultOfSave = $this->user->save();

            if (!$resultOfSave) {
                $userErrors = $this->user->getErrors();
                $this->addErrors($userErrors);
                return false;
            }

            return $resultOfSave;
        }
        return false;
    }
}
