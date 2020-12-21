<?php

namespace Iprbooks\Rki\Sdk\Core;

class Curl
{

    const HOST = 'https://api.ros-edu.ru';

    const X_API_KEY = 'QVAckUqIT49LRQQb';

    const POST = "POST";

    const GET = "GET";

    /**
     * Отправка запроса
     * @param $apiMethod
     * @param $token
     * @param $requestType
     * @param array $params
     * @return array|mixed
     */
    public static function exec($apiMethod, $token, $requestType, array $params)
    {

        if (!empty($params)) {
            $apiMethod = sprintf("%s?%s", $apiMethod, http_build_query($params, '', '&'));
        };

        $headers = array(
            'Authorization: Bearer ' . $token,
            'X-APIKey: ' . self::X_API_KEY,
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Accept: application/json'
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($curl, CURLOPT_URL, self::HOST . $apiMethod);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));

        $curlResult = curl_exec($curl);


        if (curl_errno($curl)) {
            return Curl::getError('Curl error ' . curl_errno($curl) . ': ' . curl_error($curl), 500);
        }

        $response = json_decode($curlResult, true);
        return $response;
    }


    /**
     * Вормирование сообщения в случае ошибки
     * @param $message
     * @param $code
     * @return array
     */
    private static function getError($message, $code)
    {
        return array(
            'success' => false,
            'message' => $message,
            'total' => 0,
            'status' => $code,
            'data' => null,
        );
    }
}
