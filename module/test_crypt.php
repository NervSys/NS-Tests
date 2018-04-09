<?php

/**
 * Crypt Tests
 *
 * Copyright 2018 秋水之冰 <27206617@qq.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace tests\module;

use ext\crypt;
use tests\start;

class test_crypt extends start
{
    /**
     * Crypt tests
     */
    public static function go(): void
    {
        echo 'Crypt Tests:';
        echo PHP_EOL;
        echo '========================================';
        echo PHP_EOL;
        echo 'You can provide your own "keygen" class for "crypt::$keygen"';
        echo PHP_EOL;
        echo 'Make sure to set the right path of "openssl.cnf" for "crypt::$ssl_conf"';
        echo PHP_EOL;
        echo '========================================';
        echo PHP_EOL;

        //Set "openssl.cnf" path
        crypt::$ssl_conf = 'D:/Programs/WebServer/Programs/PHP/extras/ssl/openssl.cnf';

        //Build rand data string
        $string = hash('sha256', uniqid(mt_rand(), true));

        //Generate AES key
        $aes_key = forward_static_call([crypt::$keygen, 'create']);

        //Test hash_pwd/check_pwd
        $hash = crypt::hash_pwd($string, $aes_key);
        $pwd_chk = crypt::check_pwd($string, $aes_key, $hash);
        self::chk_eq('Crypt hash_pwd/check_pwd', [$pwd_chk, true]);

        //Test encrypt/decrypt
        $enc = crypt::encrypt($string, $aes_key);
        $dec = crypt::decrypt($enc, $aes_key);
        self::chk_eq('Crypt encrypt/decrypt', [$string, $dec]);

        //Generate RSA keys
        $rsa_key = crypt::rsa_keys();

        //Test rsa_encrypt(public key)/rsa_decrypt(private key)
        $enc = crypt::rsa_encrypt($string, $rsa_key['public']);
        $dec = crypt::rsa_decrypt($enc, $rsa_key['private']);
        self::chk_eq('Crypt encrypt(RSA pub)/decrypt(RSA pri)', [$string, $dec]);

        //Test rsa_encrypt(private key)/rsa_decrypt(public key)
        $enc = crypt::rsa_encrypt($string, $rsa_key['private']);
        $dec = crypt::rsa_decrypt($enc, $rsa_key['public']);
        self::chk_eq('Crypt encrypt(RSA pri)/decrypt(RSA pub)', [$string, $dec]);

        //Test sign/verify
        $enc = crypt::sign($string);
        $dec = crypt::verify($enc);
        self::chk_eq('Crypt sign/verify', [$string, $dec]);

        //Test sign(public key)/verify(private key)
        $enc = crypt::sign($string, $rsa_key['public']);
        $dec = crypt::verify($enc, $rsa_key['private']);
        self::chk_eq('Crypt sign(RSA pub)/verify(RSA pri)', [$string, $dec]);

        //Test sign(private key)/verify(public key)
        $enc = crypt::sign($string, $rsa_key['private']);
        $dec = crypt::verify($enc, $rsa_key['public']);
        self::chk_eq('Crypt sign(RSA pri)/verify(RSA pub)', [$string, $dec]);
    }
}