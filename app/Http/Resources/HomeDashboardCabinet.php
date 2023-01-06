<?php
namespace App\Http\Resources;

use App\Models\Warehouse\Cabinets;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class HomeDashboardCabinet extends ResourceCollection {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        $result = new \stdClass();
        $totalData = Cabinets::all()->count();
        $usedData = DB::select(DB::raw("select count(x.cabinet_id) as total
from outlet.outlet_has_cabinets as x
right join warehouse.cabinets as c on c.id = x.cabinet_id"));
        $unusedData = DB::select(DB::raw("select count(x.id) as total
from warehouse.cabinets as x
left outer join outlet.outlet_has_cabinets as c on x.id = c.cabinet_id"));
        $result->total = $totalData;
        $result->used = $usedData[0]->total;
        $result->unused = $unusedData[0]->total;
        return [
            'data' => $result
        ];
    }
}