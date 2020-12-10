<?php

namespace app\common\model;

use think\Model;

class MemberAccount extends Model
{
    protected $autoWriteTimestamp = 'dateTime';

    protected $updateTime = false;

    public static $types = [
        'points' => '积分',
        'money' => '余额',
        'commission' => '佣金',
    ];

    public function getNicknameAttr($value, $data)
    {
        $member = Member::get($data['member_id']);
        return $data['member_id'] . '#' . ($member ? $member['nickname'] : '--');
    }

    public function getUsernameAttr($value, $data)
    {
        $member = Member::get($data['member_id']);
        return $data['member_id'] . '#' . ($member ? $member['username'] : '--');
    }

    public static function getNames()
    {
        $names = implode('/', array_values(self::$types));

        return $names;
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id');
    }
}
