<?php

namespace app\common\model;

use think\Model;

class MemberCation extends Model
{
    protected $autoWriteTimestamp = 'dateTime';

    protected $updateTime = false;

    public function getNicknameAttr($value, $data)
    {
        $member = Member::get($data['member_id']);
        return $data['member_id'] . '#' . ($member ? $member['nickname'] : '--');
    }
}
