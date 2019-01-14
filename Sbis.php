<?php
    /**
     * Поддержка API СБИС
     *
     *
     * User: n.zarubin
     * Date: 14.01.2019
     * Time: 12:31
     */

    namespace nikserg\sbis;

    use linslin\yii2\curl;

    class Sbis
    {
        private $login;
        private $password;
        private static $serviceUrl = 'https://api.sbis.ru/auth/service/';

        /**
         * Sbis constructor.
         *
         * @param string $login Логин в системе СБИС
         * @param string $password Пароль в системе СБИС
         */
        public function __construct($login, $password)
        {
            $this->login = $login;
            $this->password = $password;
        }

        /**
         * @see https://online.sbis.ru/shared/disk/0f875088-6774-4c48-81e4-5c09e4189b93
         */
        public function auth()
        {
            /**
             * "jsonrpc": "2.0", "method": "САП.Аутентифицировать", "protocol": 3, "params": {"login": "логин_учетной_записи","password": "пароль"}, "id": 1
             *
             */
            $data = [
                'jsonrpc' => '2.0',
                'method' => 'САП.Аутентифицировать',
                'protocol' =>  3,
                'params' => [
                    'login' => $this->login,
                    'password' => $this->password,
                ],
                'id' => 1
            ];
            $jsonData = json_encode($data);

            $curl = new curl\Curl();
            $curl->setHeader('content-type', 'application/json; charset=UTF-8');
            $curl->setRequestBody($jsonData);
            $result = $curl->get(self::$serviceUrl, true);
            print_r($result);exit;
        }
    }