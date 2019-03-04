<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Image;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;

class SaveImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将图片路径保存到模型';

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
        $dir = storage_path('app/uploads');
        $this->getDirFiles($dir);
    }

    public function getDirFiles($folder)
    {
        $data = [];

        if (is_dir($folder)) {
            $hander = opendir($folder);
            while ($file = readdir($hander)) {
                if ($file == '.' || $file == '..') {
                    continue;
                } elseif (is_file($folder . '/' . $file)) {
                    $complete_filepath = $folder . '/' . $file;

                    if (preg_match('/uploads\/\d+\/\d+\/\S+/', $complete_filepath, $filepath)) {
                        $img = app('image')->make($complete_filepath);
                        $time = Carbon::now()->format('Y-m-d H:i:s');

                        $data[] = [
                            'user_id'    => 1,
                            'path'       => Arr::first($filepath),
                            'width'      => $img->width(),
                            'height'     => $img->height(),
                            'mime'       => $img->mime(),
                            'created_at' => $time,
                            'updated_at' => $time
                        ];
                    }
                } elseif (is_dir($folder . '/' . $file)) {
                    $this->getDirFiles($folder . '/' . $file, $data);
                }
            }
        }

        if (!empty($data)) {
            array_chunk($data, 1000);

            foreach ($data as $item) {
                Image::insert($item);
            }
        }
    }
}
