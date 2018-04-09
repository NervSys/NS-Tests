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

use tests\start;
use ext\crypt as crypt_ext;

class crypt extends start
{
    /**
     * Crypt tests
     */
    public static function go(): void
    {
        echo 'Crypt Test Starts:';
        echo PHP_EOL;
        echo 'Make sure to set the right path of "openssl.cnf" in "conf.php"';
        echo PHP_EOL;
        echo 'You can provide your own "keygen" class script in "conf.php"';
        echo PHP_EOL;
        echo PHP_EOL;

        $string = (string)mt_rand();

        $aes_key = forward_static_call([crypt_ext::$keygen, 'create']);


        $enc = crypt_ext::encrypt($string, $aes_key);
        $dec = crypt_ext::decrypt($enc, $aes_key);
        self::chk_eq('encrypt/decrypt', [$string, $dec]);


        $rsa_key = crypt_ext::rsa_keys();

        $enc = crypt_ext::rsa_encrypt($string, $rsa_key['public']);
        $dec = crypt_ext::rsa_decrypt($enc, $rsa_key['private']);
        self::chk_eq('rsa_encrypt(pub)/rsa_decrypt(pri)', [$string, $dec]);


        $enc = crypt_ext::rsa_encrypt($string, $rsa_key['private']);
        $dec = crypt_ext::rsa_decrypt($enc, $rsa_key['public']);
        self::chk_eq('rsa_encrypt(pri)/rsa_decrypt(pub)', [$string, $dec]);


        $enc = crypt_ext::sign($string);
        $dec = crypt_ext::verify($enc);
        self::chk_eq('sign/verify', [$string, $dec]);


        $enc = crypt_ext::sign($string, $rsa_key['public']);
        $dec = crypt_ext::verify($enc, $rsa_key['private']);
        self::chk_eq('sign(pub)/verify(pri)', [$string, $dec]);


        $enc = crypt_ext::sign($string, $rsa_key['private']);
        $dec = crypt_ext::verify($enc, $rsa_key['public']);
        self::chk_eq('sign(pri)/verify(pub)', [$string, $dec]);


        $hash = crypt_ext::hash_pwd($string, $aes_key);
        $pwd_chk = crypt_ext::check_pwd($string, $aes_key, $hash);
        self::chk_eq('hash_pwd/check_pwd', [$pwd_chk, true]);
    }
}