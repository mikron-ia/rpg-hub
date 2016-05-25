<?php

namespace api\models\security;


use yii\web\HttpException;

class Authenticator
{
    /**
     * @param array $config Configuration segment responsible for authentication references and constants
     * @param array $parameters Configuration segment responsible for authentication data for local deployment
     * @param string $authenticationMethod
     * @param string $authenticationKey
     * @throws HttpException
     */
    public static function checkAuthentication($config, $parameters, $authenticationMethod, $authenticationKey)
    {
        $authentication = new Authentication(
            $config,
            $parameters,
            'front',
            $authenticationMethod
        );

        /* Check credentials */
        if (!$authentication->isAuthenticated($authenticationKey)) {
            throw new HttpException(
                403,
                "Authentication code $authenticationKey for method $authenticationMethod does not check out"
            );
        }
    }
}
