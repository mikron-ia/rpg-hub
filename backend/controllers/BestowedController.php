<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\BestowedList;
use common\models\core\HasEpicControl;
use common\models\Epic;
use Error;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Override;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

final class BestowedController extends CmsController
{
    use EpicAssistance;

    private const string CONTROLLING_CLASS_PREFIX = 'common\models\\';

    #[Override]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'get',
                            'set',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set' => ['PUT'],
                ],
            ],
        ];
    }

    /**
     * @throws HttpException
     */
    public function actionGet(string $listKey, string $class): string
    {
        $model = $this->findBestowedList($listKey, $class);

        return $this->renderAjax('_view_bestowed_list', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->getBestowed(),
                'pagination' => false,
            ]),
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionSet(): Response
    {
        $listKey = Yii::$app->request->post('listKey');
        $objectClass = Yii::$app->request->post('objectClass');
        $keys = Yii::$app->request->post('keys', []);

        $model = $this->findBestowedList($listKey, $objectClass);

        $model->updateList($keys);

        return new Response();
    }


    /**
     * @throws HttpException
     */
    protected function findBestowedList(string $key, string $class): BestowedList
    {
        $model = BestowedList::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'BESTOWED_LIST_NOT_AVAILABLE'));
        }

        $controllingObject = $this->getControllingClass($class, $model->bestowed_list_id);

        $epic = $controllingObject->getEpic()->one();

        if (!($epic instanceof Epic)) {
            throw new HttpException(500, Yii::t('app', 'ERROR_EPIC_IS_NOT_AN_EPIC'));
        }

        $this->selectEpic($epic->key, $epic->epic_id, $epic->name);

        $this->checkAccess($controllingObject);

        return $model;
    }

    /**
     * @throws HttpException
     */
    private function getControllingClass(string $class, int $listId): HasEpicControl
    {
        try {
            $className = self::CONTROLLING_CLASS_PREFIX . $class;
            $object = ($className)::findOne(['bestowed_list_id' => $listId]);
            if ($object instanceof HasEpicControl) {
                return $object;
            }
        } catch (Error $e) {
            // todo Add logging for invalid class
            throw new HttpException(500, Yii::t('app', 'ERROR_INVALID_CONTROL_CLASS'));
        }

        throw new HttpException(500, Yii::t('app', 'ERROR_INVALID_CONTROL_CLASS'));
    }

    /**
     * @throws HttpException
     */
    private function checkAccess(HasEpicControl $object): void
    {
        $object->canUserControlYou();
    }
}
