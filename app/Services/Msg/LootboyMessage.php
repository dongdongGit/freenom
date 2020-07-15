<?php

namespace App\Services\Msg;

use Illuminate\Support\Arr;

class LootboyMessage extends Message
{
    public function content()
    {
        if (Arr::get($this->data['data'], 'name') == 'Error') {
            $message = Arr::get($this->data['data'], 'message');

            return "lootboy 签到错误, 信息: $message";
        }

        $offer = $this->data['data']['offer'];

        return "lootboy 签到 {$offer['description']} 获得 {$offer['diamondBonus']} 钻石, 钻石余额: {$this->data['data']['newLootgemBalance']}";
    }
}
