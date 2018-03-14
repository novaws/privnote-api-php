<?php

/**
 * Created by Nova.
 * Date: 14.03.2018
 * URL: https://nova.ws
 */

class Privnote {

    public function note($message){
        $password = $this->gen_pwd();
        return $this->send_privnote($this->encrypt_data($message,$password),$password);
    }

    private function gen_pwd()
    {
        return substr(base64_encode(openssl_random_pseudo_bytes('32')), 0, 9);
    }

    private function encrypt_data($message, $passphrase)
    {
        $salt = openssl_random_pseudo_bytes(8);
        $key = $dx = openssl_digest($passphrase . $salt, 'md5', true);
        $dx = openssl_digest($dx . $passphrase . $salt, 'md5', true);
        $key .= $dx;
        $iv = openssl_digest($dx . $passphrase . $salt, 'md5', true);
        return base64_encode('Salted__' . $salt . openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv));
    }

    private function send_privnote($message, $passphrase)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://privnote.com/");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: privnote.com',
            'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0',
            'Accept: */*',
            'Accept-Language: en-us,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br',
            'Referer: https://privnote.com',
            'Content-type: application/x-www-form-urlencoded',
            'X-Requested-With: XMLHttpRequest',
            'DNT: 1',
            'Connection: keep-alive'
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'data' => $message,
            'has_manual_pass' => 'false',
            'duration_hours' => '0',
            'dont_ask' => 'false',
            'data_type' => 'T',
            'notify_email' => '',
            'notify_ref' => ''
        )));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($server_output, true);
        return $json['note_link'] . '#' . $passphrase;
    }
}