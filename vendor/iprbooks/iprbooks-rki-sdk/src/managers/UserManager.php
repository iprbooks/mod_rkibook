<?php

namespace Iprbooks\Rki\Sdk\Managers;

use Exception;
use Iprbooks\Rki\Sdk\Client;
use Iprbooks\Rki\Sdk\Core\Curl;
use Iprbooks\Rki\Sdk\Core\Response;
use Iprbooks\Rki\Sdk\Models\User;

class UserManager extends Response
{

    /**
     * Конструктор UserManager
     * @param $client
     * @return UserManager
     * @throws Exception
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
        if (!$client) {
            throw new Exception('client is not init');
        }
        return $this;
    }


    /**
     * Создает нового пользователя в ЭБС
     * @param $email
     * @param $fullname
     * @param $pass
     * @param $userType
     * @return User
     * @throws Exception
     */
    public function registerNewUser($email, $fullname, $pass, $userType = User::OTHER)
    {
        $apiMethod = '/security/users/add';
        $params = array(
            'email' => $email,
            'fullname' => $fullname,
            'password' => $pass,
            'user_type' => $userType
        );

        $this->response = $this->getClient()->makeRequest($apiMethod, Curl::GET, $params);
        $this->data = $this->response['data'];
        $user = new User($this->getClient(), $this->response);
        return $user;
    }

    /**
     * Блокировка пользователя
     * @param $id
     * @return bool|mixed
     */
    public function deleteUser($id)
    {
        if (!$id) {
            return false;
        }

        $apiMethod = '/security/users/delete/{id}';
        $apiMethod = str_replace('{id}', $id, $apiMethod);

        $this->response = $this->getClient()->makeRequest($apiMethod, Curl::GET, array());
        $this->data = $this->response['data'];
        return $this->getSuccess();
    }

    /**
     * Восстановление пользователя
     * @param $id
     * @return bool|mixed
     */
    public function restoreUser($id)
    {
        if (!$id) {
            return false;
        }

        $apiMethod = '/security/users/restore/{id}';
        $apiMethod = str_replace('{id}', $id, $apiMethod);

        $this->response = $this->getClient()->makeRequest($apiMethod, Curl::GET, array());
        $this->data = $this->response['data'];
        return $this->getSuccess();
    }

}