<?php

namespace App\Http\Controllers\Admin;

use Cache;
use App\Models\User;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;

class UtilController extends Controller
{
    public function index()
    {
        $user = $this->user();
        $cached_stats = Cache::remember('user_' . $this->user()->id, 900, function () use ($user) {
            $data = [
                'domain' => $user->domains()->count(),
                'user'   => User::count()
            ];

            return $data;
        });

        return $this->success($cached_stats);
    }

    /**
     * 返回CSRF TOKEN
     *
     * @return void
     */
    public function generateCsrfToken()
    {
        return $this->success(csrf_token());
    }

    /**
     * 多图传图
     */
    public function storeImage(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image'
        ]);

        $file = request()->file('image');

        if (empty($file) || !$file->isValid()) {
            return $this->error(40402);
        }

        $image = (new ImageService($file->path()))->save();

        return $this->success([
            'id'   => $image->id,
            'path' => $image->path
        ]);
    }

    /**
     * 多图删图
     */
    public function destroyImage(Request $request)
    {
        $data = $request->validate([
            'image_ids'   => 'required|array',
            'image_ids.*' => 'integer'
        ]);

        $ids = array_unique($data['image_ids']);

        if (!empty($ids)) {
            $images = Image::whereIn('id', $ids)->where('user_id', $this->user()->id)->get();

            foreach ($images as $image) {
                (new ImageService($image))->delete();
            }
        }

        return $this->success();
    }
}
