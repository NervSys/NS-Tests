<?php

/**
 * Nervsys UnitTest
 *
 * Copyright 2016-2020 秋水之冰 <27206617@qq.com>
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

namespace app\UnitTest\Unit;

use app\UnitTest\Lib\res;
use ext\conf;
use ext\crypt as unit_crypt;

class crypt
{
    public $test_list = [
        'pwd',
        'aes',
        'rsa',
        'sign'
    ];

    private $string   = '';
    private $aes_key  = '';
    private $rsa_keys = [];

    private $unit_crypt;

    /**
     * crypt constructor.
     */
    public function __construct()
    {
        $this->unit_crypt = unit_crypt::create(conf::get('crypt'));

        $this->aes_key  = $this->unit_crypt->get_key();
        $this->rsa_keys = $this->unit_crypt->rsa_keys();

        $this->string = hash('sha256', uniqid(mt_rand(), true));
    }

    /**
     * Test hash_pwd/check_pwd
     */
    public function pwd(): void
    {
        $hash    = $this->unit_crypt->hash_pwd($this->string, $this->aes_key);
        $pwd_chk = $this->unit_crypt->check_pwd($this->string, $this->aes_key, $hash);
        res::chk(__FUNCTION__, [$pwd_chk, true]);
    }

    /**
     * Test encrypt/decrypt
     */
    public function aes(): void
    {
        $enc = $this->unit_crypt->encrypt($this->string, $this->aes_key);
        $dec = $this->unit_crypt->decrypt($enc, $this->aes_key);
        res::chk(__FUNCTION__, [$this->string, $dec]);
    }

    /**
     * Test RSA encrypt/decrypt
     */
    public function rsa(): void
    {
        $enc = $this->unit_crypt->rsa_encrypt($this->string, $this->rsa_keys['public']);
        $dec = $this->unit_crypt->rsa_decrypt($enc, $this->rsa_keys['private']);
        res::chk(__FUNCTION__ . ' (pub=>pri)', [$this->string, $dec]);

        $enc = $this->unit_crypt->rsa_encrypt($this->string, $this->rsa_keys['private']);
        $dec = $this->unit_crypt->rsa_decrypt($enc, $this->rsa_keys['public']);
        res::chk(__FUNCTION__ . ' (pri=>pub)', [$this->string, $dec]);
    }

    /**
     * Test sign/verify
     */
    public function sign(): void
    {
        $enc = $this->unit_crypt->sign($this->string);
        $dec = $this->unit_crypt->verify($enc);
        res::chk(__FUNCTION__ . ' (normal)', [$this->string, $dec]);

        $enc = $this->unit_crypt->sign($this->string, $this->rsa_keys['public']);
        $dec = $this->unit_crypt->verify($enc, $this->rsa_keys['private']);
        res::chk(__FUNCTION__ . ' (pub=>pri)', [$this->string, $dec]);

        $enc = $this->unit_crypt->sign($this->string, $this->rsa_keys['private']);
        $dec = $this->unit_crypt->verify($enc, $this->rsa_keys['public']);
        res::chk(__FUNCTION__ . ' (pri=>pub)', [$this->string, $dec]);
    }
}