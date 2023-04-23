<?php


namespace common\models\tools;


use yii\db\ActiveQuery;

/**
 * Trait ToolsForHasDescription, intended to provided some methods required by common\models\core\HasDescriptions
 *
 * NOTE: this trait does not have any safeties for a case when the using class does not have the fields; use responsibly
 *
 * @package common\models\tools
 */
trait ToolsForHasDescription
{
    public function getDescriptionPackId(): int
    {
        return $this->description_pack_id;
    }

    public function getDescriptionsVisible(): ActiveQuery
    {
        return $this->descriptionPack->getDescriptionsVisible();
    }
}
