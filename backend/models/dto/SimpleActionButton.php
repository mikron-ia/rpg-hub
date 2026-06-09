<?php

namespace backend\models\dto;

use yii\helpers\Html;

final readonly class SimpleActionButton
{
    public function __construct(
        public string $text,
        public string $explanation,
        public string $confirmation,
        public string $controller,
        public string $action,
        public string $command,
        public string $key,
    ) {
    }

    public function __toString(): string
    {
        return Html::a(
            $this->text,
            [
                sprintf('%s/%s', $this->controller, $this->action),
                'key' => $this->key,
                'command' => $this->command,
            ],
            [
                'title' => $this->explanation,
                'class' => 'btn btn-default',
                'data-confirm' => $this->confirmation,
                'data-method' => 'post',
            ]);
    }
}
