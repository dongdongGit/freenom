<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class ConfigurationFreenomAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:freenom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '配置freenom账号';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (file_exists($envFilePath = $this->getPathToEnvFile()) === false) {
            $this->info("Could not find env file!");
        }

        $username = $this->ask('请输入Freenom账号:');
        if ($this->updateEnvFile($envFilePath, 'FREENOM_USERNAME', $username)) {
            $this->info("File .env updated with FREENOM_USERNAME: $username");
        }

        $password = Crypt::encryptString($this->ask('请输入Freenom密码:'));
        if ($this->updateEnvFile($envFilePath, 'FREENOM_PASSWORD', $password)) {
            $this->info("File .env updated with FREENOM_PASSWORD: $password");
        }
    }

    /**
     * @return string
     */
    private function getPathToEnvFile()
    {
        return base_path('.env');
    }

    private function updateEnvFile($path, $name, $value)
    {
        if (empty($name)) {
            return false;
        }

        if (file_exists($path)) {

            $oldContent = file_get_contents($path);
            $search = "{$name}=" . env($name);

            if (!Str::contains($oldContent, $search)) {
                $search = "{$name}=";
            }

            $newContent = str_replace($search, "{$name}=" . $value, $oldContent);

            return file_put_contents($path, $newContent);
        }

        return false;
    }
}
