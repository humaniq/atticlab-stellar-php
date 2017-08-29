<?php

namespace Smartmoney\Stellar;

class Payment {


    private static function validate($config){

        if(empty($config->emission->username)){
            throw new \Exception(Error::BAD_CONFIG . ' (emission->username)');
        }

        if(empty($config->emission->password)){
            throw new \Exception(Error::BAD_CONFIG . ' (emission->password)');
        }

        if(empty($config->emission->url)){
            throw new \Exception(Error::BAD_CONFIG . ' (emission->url)');
        }

    }
    /**
     * @param $account - string
     * @param $amount  - float
     * @param $asset   - string
     * @param $config  - object of settings
     * @return json string
     * @description send request to operator host, return response or error
     */
    public static function sendPaymentByEmission($account, $amount, $asset, $config)
    {

        self::validate($config);

        if (!empty($amount) && !empty($asset) && !empty($account)) {

            $amount  = (float)number_format($amount, 2, '.', '');
            $account = mb_strtoupper($account);

            if (Account::isValidAccountId($account)) {

                $data = [
                    'accountId' => $account,
                    'amount'    => $amount,
                    'asset'     => $asset,
                ];

                $data = json_encode($data);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $config->emission->url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_USERPWD => $config->emission->username . ":" . $config->emission->password,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",
                    ),
                ));

                $response   = curl_exec($curl);
                $err        = curl_error($curl);
                $curl_info  = curl_getinfo($curl);

                curl_close($curl);

                if ($err) {
                    $answer = ['request_error' => base64_encode($err)];

                    return json_encode($answer);

                } elseif (!empty($curl_info['http_code']) && $curl_info['http_code'] != 200) {
                    $answer = ['request_error' => base64_encode($response)];

                    return json_encode($answer);

                } else {

                    return $response;
                }
            } else {
                $answer = ['error' => Error::INV_ACC];

                return json_encode($answer);
            }

        } else {
            $answer = ['error' => Error::BAD_PARAMS];

            return json_encode($answer);
        }
    }

}