<?php

namespace common\models\tools;

use Yii;
use yii\base\Exception;

/**
 * Class Retriever - retrieves data from a given source
 */
class Retriever
{
    /**
     * @var string Data source
     */
    private $uri;

    /**
     * @var string Retrieved data in JSON
     */
    private $json;

    /**
     * @var string Retrieved data in array
     */
    private $data;

    /**
     * Retriever constructor.
     * @param $uri
     * @throws Exception
     */
    public function __construct($uri)
    {
        $this->uri = $uri;

        $this->json = $this->retrieve();
        $this->data = json_decode($this->json, true);

        if (empty($this->data)) {
            throw new Exception(Yii::t('app', 'JSON_INVALID'));
        }
    }

    /**
     * @return string JSON from the source
     * @throws Exception
     */
    private function retrieve()
    {
        $curl = curl_init($this->uri);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        $result = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if (!empty($error)) {
            throw new Exception(Yii::t('app', 'CURL_ERROR') . ': ' . $error);
        }

        return $this->formatInput($result);
    }

    /**
     * Formats JSON string - at the moment, does nothing
     *
     * @param string $input JSON string to format
     * @return string Formatted JSON string
     */
    private function formatInput($input)
    {
        return $input;
    }

    /**
     * @return string
     */
    public function getDataAsJSON()
    {
        return $this->json;
    }

    /**
     * @return array
     */
    public function getDataAsArray()
    {
        return $this->data;
    }
}
