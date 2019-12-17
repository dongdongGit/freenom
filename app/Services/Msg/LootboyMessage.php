<?php

namespace App\Services\Msg;

class LootboyMessage extends Message
{
    public function content()
    {
        $offer = $this->data['data']['offer'];

        return "lootboy 签到 {$offer['description']} 获得 {$offer['diamondBonus']} 钻石, 钻石余额: {$this->data['data']['newLootgemBalance']}";
    }
}
