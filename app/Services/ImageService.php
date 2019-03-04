<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    private $max_width = 1280;
    private $max_height = 0;
    private $file_path = null;
    private $disk = null;

    public function __construct($file_path, $max_width = 1280, $max_height = 0)
    {
        if (is_integer($max_width) && $max_width > 0) {
            $this->max_width = $max_width;
        }
        if (is_integer($max_height) && $max_height > 0) {
            $this->max_height = $max_height;
        }

        $this->file_path = $file_path;
    }

    public function save($saveAs = '')
    {
        // TODO:
        if (is_object($this->file_path)) {
            $path = $this->file_path->store('uploads/' . date('Ym') . '/' . date('d'), 'cosv5');
            $filePath = $this->disk->get($path);
            $img = app('image')->make($filePath);
        } elseif (is_string($this->file_path)) {
            $path = 'uploads/' . date('Ym') . '/' . date('d') . '/' . time() . Str::random(8) . '.jpg' ;
            $img = app('image')->make($this->file_path);

            Storage::put($path, $img->encode());
        }

        if ($this->max_width > 0 && $this->max_height == 0) {
            $this->max_width = min($this->max_width, $img->width());
            $img->resize($this->max_width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $img->fit($this->max_width, $this->max_height);
        }

        if (!empty($saveAs) && file_exists($saveAs)) {
            file_put_contents($saveAs, $img->encode());
        }

        return $path;
    }
}
