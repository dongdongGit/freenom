<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Crypt;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $meituan = config('meituan');
        $meituan['password'] = Crypt::decryptString($meituan['password']);

        $this->browse(function (Browser $browser) use ($meituan) {

            $browser = $browser->visit('https://i.meituan.com/awp/hfe/block/7bb53f589b1f/21960/index.html?lch=qiandaoshare/')
                ->reload()
                ->waitFor('div.block-platform-app-userinfo-container > div > div.info > p > span')
                ->click('div.block-platform-app-userinfo-container > div > div.info > p > span')
                ->type('username', $meituan['username'])
                ->type('password', $meituan['password'])
                ->press('.mj_login.login-btn')
                // ->visit('https://i.meituan.com/awp/hfe/block/7bb53f589b1f/21960/index.html?lch=qiandaoshare/')
                ->reload()
                ->waitFor('div.block-platform-app-userinfo-container > div > div.info > p > span')
                ->click('div.block-platform-app-userinfo-container > div > div.info > p > span');
        });
    }
}
