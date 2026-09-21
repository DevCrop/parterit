<?php
/*
 * 실 오픈 시: 도메인(HTTP_HOST) + $NO_SITE_UNIQUE_KEY 와 동일한 규칙으로 해시를 발급해 등록합니다.
 * 해시 생성: php -r "echo hash('sha256', '실제HTTP_HOST' . 'DKHOLD');"
 * (base.class.php 의 $NO_SITE_UNIQUE_KEY 값과 문자열을 그대로 이어 붙입니다.)
 */


class Lisence
{
    static $keys = [];

    static function add($newLisence = '')
    {
        if (isset($newLisence) && !empty($newLisence)) {
            self::$keys[] = $newLisence;
        }
    }

    static function getAll()
    {
        return self::$keys;
    }
}

// Partnerit. DKHOLD는 CMS 데이터 호환을 위해 유지하는 내부 sitekey다.
Lisence::add('bfe00a0a9ac7bb5fa63d793ea7f6e709c494c861d3ae6571250dc67058e42993'); // localhost DKHOLD
Lisence::add('f2adb5e9f63631694034ec1eecb43229c316c0db26b5791822f478cd44861206'); // 127.0.0.1 DKHOLD
Lisence::add('8eaad1c085d650ce69346d82a02f79daf2bceef4210e1c0a7a9a0bca7cf9d4ad'); // localhost:18888 DKHOLD
Lisence::add('95328e01b5304f0e1034741c94021878d4c176d901050d6a55c9af61e0ca642f'); // 127.0.0.1:18888 DKHOLD
Lisence::add('5e84cbb6f455b39396a679c8f8106ab27474b9eb822fd7dfcdb90bcefd5fe4d4'); // partnerit.nineonelabs.co.kr DKHOLD
// 운영(고객) 도메인 확정 시 아래 형식으로 추가 (도메인만 교체):
//   php -r "echo hash('sha256', '운영도메인' . 'DKHOLD');"
// Lisence::add('생성된해시'); // 운영도메인 DKHOLD
