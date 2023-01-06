<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class HomeDashboardOutlet extends ResourceCollection {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        $data = $this->collection->select(DB::raw("is_deleted, count(is_deleted) as total"))
            ->groupBy("is_deleted")->get();
        return [
            'data' => $data
        ];
    }
}