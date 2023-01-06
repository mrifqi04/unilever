<?php
namespace App\Http\Controllers;

use App\Models\UserManagement\User;
use cebe\markdown\GithubMarkdown;
use cebe\markdown\MarkdownExtra;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class TestController extends Controller {
    //put your code here
    public function __construct() {

    }

    public function index(){
        $my_file = '/Users/admin/GoWorks/src/zero/Multilib/docs/LotteGrosirDoc.md';
        $handle = fopen($my_file, 'r');
        $data = fread($handle,filesize($my_file));
        $parser = new MarkdownExtra();
//        $parser->html5 = true;
        return $parser->parse($data);
    }

    public function store() {
        DB::beginTransaction();
        try {
            $data = new User;
            $data->id = Uuid::uuid4()->toString();
            $data->username = "nuansa.ramadhan@gmail.com";
            $data->name = "Nuansa Putra Ramadhan";
            $data->email = "nuansa.ramadhan@gmail.com";
            $data->phone = "6285817571157";
            $data->password = bcrypt("123456");
            $data->created_at = now();
            $data->created_by = "SYSTEM";
            $data->save();
            $data->roles()->attach("XXXXX001");
            DB::commit();
            return "OK";
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
    }
}