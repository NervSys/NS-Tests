<?php

/**
 * Nervsys test suites
 *
 * Copyright 2016-2019 秋水之冰 <27206617@qq.com>
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

namespace app\tests;

use app\tests\lib\res;
use ext\crypt AS base_crypt;

class crypt extends base_crypt
{
    public static $tz = [
        'test_pwd'       => '',
        'test_crypt_aes' => '',
        'test_crypt_rsa' => '',
        'test_sign'      => ''
    ];

    private $crypt;

    private $string   = '';
    private $aes_key  = [];
    private $rsa_keys = [];

    /**
     * crypt constructor.
     */
    public function __construct()
    {
        $this->crypt = parent::config(['conf' => 'D:/Programs/Serv-Me/Program/PHP/extras/ssl/openssl.cnf']);

        $this->aes_key  = $this->crypt->get_key();
        $this->rsa_keys = $this->crypt->rsa_keys($this->aes_key);

        $this->string = hash('sha256', uniqid(mt_rand(), true));
    }

    /**
     * Test hash_pwd/check_pwd
     */
    public function test_pwd(): void
    {
        $hash    = $this->crypt->hash_pwd($this->string, $this->aes_key);
        $pwd_chk = $this->crypt->check_pwd($this->string, $this->aes_key, $hash);
        res::chk_eq('hash_pwd/check_pwd', [$pwd_chk, true]);
    }

    /**
     * Test encrypt/decrypt
     */
    public function test_crypt_aes(): void
    {
        $enc = $this->crypt->encrypt($this->string, $this->aes_key);
        $dec = $this->crypt->decrypt($enc, $this->aes_key);
        res::chk_eq('encrypt/decrypt', [$this->string, $dec]);
    }

    /**
     * Test RSA encrypt/decrypt
     */
    public function test_crypt_rsa(): void
    {
        $enc = $this->crypt->rsa_encrypt($this->string, $this->rsa_keys['public']);
        $dec = $this->crypt->rsa_decrypt($enc, $this->rsa_keys['private']);
        res::chk_eq('rsa_encrypt(pub)/rsa_decrypt(pri)', [$this->string, $dec]);

        $enc = $this->crypt->rsa_encrypt($this->string, $this->rsa_keys['private']);
        $dec = $this->crypt->rsa_decrypt($enc, $this->rsa_keys['public']);
        res::chk_eq('rsa_encrypt(pri)/rsa_decrypt(pub)', [$this->string, $dec]);
    }

    /**
     * Test sign/verify
     */
    public function test_sign(): void
    {
        $enc = $this->crypt->sign($this->string);
        $dec = $this->crypt->verify($enc);
        res::chk_eq('sign/verify', [$this->string, $dec]);

        $enc = $this->crypt->sign($this->string, $this->rsa_keys['public']);
        $dec = $this->crypt->verify($enc, $this->rsa_keys['private']);
        res::chk_eq('sign(pub)/verify(pri)', [$this->string, $dec]);

        $enc = $this->crypt->sign($this->string, $this->rsa_keys['private']);
        $dec = $this->crypt->verify($enc, $this->rsa_keys['public']);
        res::chk_eq('sign(pri)/verify(pub)', [$this->string, $dec]);
    }
}