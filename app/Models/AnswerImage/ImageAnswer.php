<?php

namespace App\Models\AnswerImage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ImageAnswer
 * @package App\Models\AnswerImage
 */
class ImageAnswer extends Model
{
    protected $table = "hunter.image_answer";
    protected $primaryKey = "id";
    protected $keyType = "string";

    public $incrementing = false;
    public $timestamps = false;

    /**
     * @return String
     */
    static function GetPicture($type, $id): String
    {
        $fileName = "";
        try{
            if($type=="hunter"){
                $record = DB::table('hunter.answer_image')
                    ->where('id', '=', $id)
                    ->first();
                if(is_null($record)){
                    return "";
                }
                if (strlen($record->basedir) > 0 && $record->basedir[0] === '.') {
                    $fileName = substr($record->basedir, 1, strlen($record->basedir) - 1) . $record->filename;
                }
            }elseif ($type=="auditor"){
                $record = DB::table('auditor.answer_images')
                    ->where('id', '=', $id)
                    ->first();
                if(is_null($record)){
                    return "";
                }

                $fileName = "/var/data/image/".$record->image;
            }
        }catch (QueryException $e){
            Log::error($e);
        }catch (\Exception $e){
            Log::error($e);
        }

        return $fileName;
    }
}
