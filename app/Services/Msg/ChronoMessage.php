<?php

namespace App\Services\Msg;

use Illuminate\Support\Arr;

class ChronoMessage extends Message
{
    public function content()
    {
        $data = $this->data['data'];

        if (is_array($result = $data['sign_result'])) {
            $coins = Arr::get($result, 'quest.value', 0) + Arr::get($result, 'quest.bonus', 0);
            $content = "chrono 签到获得 {$coins} 金币";

            if (!empty($chest = Arr::get($result, 'chest'))) {
                $additional_coins = Arr::get($chest, 'base', 0) + Arr::get($chest, 'bonus');
                $kind = Arr::get($chest, 'kind');
                $sum_coins = $coins + $additional_coins;
                $content .= ", 连续 {$kind} 天签到额外获得 {$additional_coins} 金币, 总共获得 {$sum_coins} 金币";
            }
        } else {
            $content = 'chrono 已经签到';
        }

        return $content . ", 金币余额: {$data['balance']} 枚";
    }
}
