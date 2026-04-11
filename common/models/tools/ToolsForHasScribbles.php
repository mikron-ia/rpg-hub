<?php

namespace common\models\tools;

use common\models\Scribble;
use Yii;
use yii\db\Exception;

trait ToolsForHasScribbles
{
    private Scribble $currentUserScribble;

    public function getScribbleForCurrentUser(): ?Scribble
    {
        if (empty($this->currentUserScribble)) {
            $userId = Yii::$app->user->getId();

            if (empty($userId)) {
                // casus of a guest user, they are not allowed to use scribbles
                return null;
            }

            try {
                $this->currentUserScribble = $this->scribblePack?->getScribbleByUserId($userId);
            } catch (Exception) {
                return null;
            }
        }

        return $this->currentUserScribble;
    }
}
