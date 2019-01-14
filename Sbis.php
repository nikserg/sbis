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
        private static $authUrl = 'https://api.sbis.ru/auth/service/';
        private static $serviceUrl = 'https://api.sbis.ru/spp-rest-api/service/';
        private $authSid;

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
         * Отправка запроса в СБИС
         *
         *
         * @param string $url
         * @param string $method
         * @param array $params
         * @return mixed
         * @throws SbisException
         */
        private function send($url, $method, $params = [])
        {
            $data = [
                'jsonrpc' => '2.0',
                'method' => $method,
                'protocol' =>  4,
                'id' => 1,
            ];
            $curl = new curl\Curl();
            if ($this->authSid) {
                $curl->setHeader('Cookie', 'sid='.$this->authSid);
            }
            if (!empty($params)) {
                $data['params'] = $params;
            }
            $jsonData = json_encode($data);
            $curl->setHeader('Content-Type', 'application/json; charset=UTF-8');
            $curl->setHeader('User-Agent', 'PHP');
            $curl->setRawPostData($jsonData);
            $result = $curl->post(self::$authUrl, true);

            if (!$result) {
                throw new SbisException('Не получен ответ от СБИС по адресу '.self::$authUrl, 500);
            }

            $result = @json_decode($result, true);

            if (!$result) {
                throw new SbisException('Получен не JSON-ответ от СБИС по адресу '.self::$authUrl, 500);
            }

            if (isset($result['error']) && $result['error']) {
                print_r($curl);
                //Возникла ошибка СБИС
                $exception = new SbisException('СБИС ответил ошибкой по адресу '.$url.': '.$result['error']['details']. ' '.print_r($result, true), $result['error']['code']);
                throw $exception;
            }

            return $result;
        }

        public function getRequisites($inn, $ogrn = null)
        {
            $this->auth();
            if (!$inn && !$ogrn) {
                throw new SbisException('Нужно передать ИНН или ОГРН', 500);
            }
            $data = [];
            if ($inn) {
                $data['inn'] = $inn;
            }
            if ($ogrn) {
                $data['ogrn'] = $ogrn;
            }
            $result = $this->send(self::$serviceUrl,'SppAPI.Requisites', $data);
            print_r($result);exit;
        }
        /**
         * Авторизация в СБИС
         *
         *
         * @see https://online.sbis.ru/shared/disk/0f875088-6774-4c48-81e4-5c09e4189b93
         * @return string ID аутентифицированной сессии
         */
        public function auth()
        {
            if ($this->authSid) {
                return $this->authSid;
            }
            /**
             * "jsonrpc": "2.0", "method": "САП.Аутентифицировать", "protocol": 3, "params": {"login": "логин_учетной_записи","password": "пароль"}, "id": 1
             *
             */
            $result = $this->send(self::$authUrl,'САП.Аутентифицировать', [

                'login' => $this->login,
                'password' => $this->password,
            ]);
            print_r($result);
            $this->authSid = $result['result'];
            return $this->authSid;
        }
    }

    class SbisException extends \Exception {

    }