<?php

namespace App\Observers;

use App\Models\Image;

class ImageObserver
{
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
