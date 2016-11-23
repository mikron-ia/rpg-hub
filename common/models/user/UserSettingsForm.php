<?php

namespace common\models\user;

use common\models\core\Language;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;

final class UserSettingsForm extends Model
{
    /**
     * @var \common\models\User
     */
    private $user;

    public $email;
    public $language;
    public $username;

    public function __construct($config = [])
    {
        $this->user = Yii::$app->user->identity;
        $this->language = $this->user->language;
        $this->email = $this->user->email;
        $this->username = $this->user->username;

        parent::__construct($config);
    }

    final public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'USER_EMAIL'),
            'language' => Yii::t('app', 'USER_LANGUAGE'),
            'username' => Yii::t('app', 'USER_USERNAME'),
        ];
    }

    public function rules()
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
                    $query->andWhere(['=', 'status', User::STATUS_ACTIVE]);
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
                    $query->andWhere(['=', 'status', User::STATUS_ACTIVE]);
                    $query->andWhere(['<>', 'id', $this->user->id]);
                },
            ],
        ];
    }

    /**
     * @return bool
     */
    public function save()
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
