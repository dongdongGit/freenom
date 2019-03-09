<?php

namespace App\Services;

use Exception;
use App\Models\Image;
use Illuminate\Support\Str;

class ImageService
{
    private $max_width = 1280;
    private $max_height = 0;
    private $object = null;
    private $disk = null;
    private $savedToModel = false;

    public function __construct($object, $savedToModel = true, $max_width = 1280, $max_height = 0)
    {
        if (is_integer($max_width) && $max_width > 0) {
            $this->max_width = $max_width;
        }
        if (is_integer($max_height) && $max_height > 0) {
            $this->max_height = $max_height;
        }

        $this->disk = app('filesystem')->disk('upload');
        $this->object = $object;
        $this->savedToModel = $savedToModel;
    }

    public function save($saveAs = '')
    {
        if (is_object($this->object)) {
            $path = $this->object->store(date('Ym') . '/' . date('d'), 'upload');
            $filePath = $this->disk->get($path);
            $img = app('image')->make($filePath);
        } elseif (is_string($this->object)) {
            $path = date('Ym') . '/' . date('d') . '/' . time() . Str::random(8) . '.jpg' ;
            $img = app('image')->make($this->object);

            // Storage::put($path, $img->encode());
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

        try {
            if ($this->disk->put($path, $img->encode())) {
                // return $path;
            }
        } catch (Exception $e) {
            return false;
        }

        $authUser = auth_user();
        if ($this->savedToModel) {
            return Image::create([
                'path'    => $path,
                'width'   => $img->width(),
                'height'  => $img->height(),
                'user_id' => auth_user() ? $authUser->id : 0
            ]);
        }

        return $path;
    }

    public function delete()
    {
        try {
            if (!empty($this->object) && $this->disk->exists($this->object->getOriginal('path'))) {
                $this->disk->delete($this->object->getOriginal('path'));
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
}
