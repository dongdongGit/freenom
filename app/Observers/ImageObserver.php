<?php

namespace App\Observers;

use App\Models\Image;
use App\Services\ImageService;

class ImageObserver
{
    public function deleting(Image $image)
    {
        $imageService = new ImageService($image);
        $imageService->delete();
    }

    /**
     * Handle the image "deleted" event.
     *
     * @param  \App\Models\Image  $image
     * @return void
     */
    public function deleted(Image $image)
    {
        activity('image_delete')
            ->causedBy($image->user)
            ->performedOn($image)
            ->log(':causer.name 删除图片');
    }
}
