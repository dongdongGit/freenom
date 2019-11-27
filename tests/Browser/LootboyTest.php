<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LootboyTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser = $browser->visit('https://www.lootboy.de')
                ->waitFor('#onesignal-popover-cancel-button', 10)
                ->click('#onesignal-popover-cancel-button')
                ->pause(mt_rand(1000, 2000))
                ->press('#root > div > div.mainBody > div > div.navbar > div.left > div.menuIcons > a:nth-child(1)')
                ->pause(mt_rand(200, 300))
                ->click('#root > div > div.Dialog.MenuView.big > div > div > div.modalContent > div > div.top > div > div.lootboy-button-container.white > div')
                ->pause(10000)
                ->click('#root > div > div:nth-child(6) > div.Dialog.gdpr-dialog.big > div > div > div > div.footer > div.lootboy-button-container.green > div')
                ->pause(50000);
        });
    }
}
