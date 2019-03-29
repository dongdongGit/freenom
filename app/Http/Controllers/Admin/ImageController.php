<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $images = $this->user()->images()->latest('id')->apiPaginate(10);

        return $this->success($images);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = $this->user()->images()->findOrFail($id);
        $image->delete();

        return $this->success();
    }
}
