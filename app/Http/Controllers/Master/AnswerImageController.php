<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Unilever\OutletProgress;
use App\Models\AnswerImage\ImageAnswer;

class AnswerImageController extends Controller
{
    /**
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function picture($id)
    {
        $fileName = '';
        $picture = OutletProgress::findPicture($id);
        if (!is_null($picture)) {
            if (strlen($picture->basedir) > 0 && $picture->basedir[0] === '.') {
                $fileName = substr($picture->basedir, 1, strlen($picture->basedir) - 1) . $picture->filename;
            }
            $fileName = config('app.image_base_dir') . '/' . $fileName;
        }
        if (trim($fileName) == '' || !file_exists($fileName)) {
            $fileName = public_path('assets/images/seru_logo.png');
        }
        return response()->file($fileName);
    }

    public function get($type, $id)
    {
        $fileName = '';
        $picture = ImageAnswer::GetPicture($type, $id);
        if (!empty($picture)) {
            $fileName = config('app.image_base_dir') . '/' . $picture;
        }
        if (trim($fileName) == '' || !file_exists($fileName)) {
//            $fileName = public_path('assets/images/seru_logo.png');
            abort(404);
        }
//        return response()->download($fileName);
        return response()->file($fileName);
    }
}
