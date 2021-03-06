<?php

namespace console\controllers;

use common\models\Character;
use common\models\tools\Retriever;
use Yii;
use yii\base\Exception;
use yii\console\Controller;

class LoaderController extends Controller
{
    /**
     * Loads data from an external source
     * @return mixed
     */
    public function actionPerson()
    {
        /* @var $models Character[] */
        $models = Character::find()->all();

        foreach ($models as $model) {
            $baseUrl = Yii::$app->params['reputationAccessUri'];
            $authKey = Yii::$app->params['reputationAccessKey'];

            $placeholders = ['{modelKey}', '{authKey}'];
            $data = [$model->key, $authKey];

            $url = str_replace($placeholders, $data, $baseUrl);

            try {
                $retriever = new Retriever($url);
                $data = $retriever->getDataAsArray();

                if (!isset($data['content'])) {
                    throw new Exception('EXTERNAL_DATA_MALFORMED_ARRAY');
                }

                $model->data = json_encode($data['content']);

                if ($model->save()) {
                    echo Yii::t('app', 'EXTERNAL_DATA_LOAD_SUCCESS') . ': ' . $model->key . PHP_EOL;
                } else {
                    $errors = [];

                    foreach ($model->getErrors() as $error) {
                        $errors[] = implode(', ', $error);
                    }

                    echo $model->key . ': ' .
                        Yii::t('app', 'EXTERNAL_DATA_LOAD_ERROR_SAVE') .
                        ': ' . implode(', ', $errors)
                        . PHP_EOL;
                }

            } catch (Exception $e) {
                echo Yii::t('app', 'EXTERNAL_DATA_LOAD_ERROR_JSON') . ': ' . $e->getMessage() . PHP_EOL;
            }
        }

        return 0;
    }
}
