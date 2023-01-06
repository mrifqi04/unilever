<?php

namespace App\export;


use App\Models\JuraganManagement\Juragan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class OutletStatusExport implements FromView
{

    /**
     * @var Carbon $startDate
     */
    private $startDate;
    /**
     * @var Carbon $endDate
     */
    private $endDate;
    private $provinceId;
    private $cityId;
    private $id;
    private $name;
    private $outletName;

    public function __construct($startDate, $endDate, $provinceId, $cityId, $id, $name, $outletName)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->provinceId = $provinceId;
        $this->cityId = $cityId;
        $this->id = $id;
        $this->name = $name;
        $this->outletName = $outletName;
    }

    public function view(): View
    {
        $model = DB::table("v_jurgan_outlet_status");
        if ($this->provinceId != "") {
            $model = $model->where("id_province", '=', $this->provinceId);
        }
        if ($this->cityId != "") {
            $model = $model->where("id_city", '=', $this->cityId);
        }
        if ($this->id != "") {
            $model = $model->where("juragan_id", '=', $this->id);
        }
        if ($this->name != "") {
            $model = $model->where("juragan", 'ilike', '%' . $this->name . '%');
        }
        if ($this->outletName != "") {
            $model = $model->where("outlet_name", 'ilike', '%' . $this->outletName . '%');
        }

        if (!is_null($this->startDate) && !is_null($this->endDate)) {
            $model = $model->whereBetween("outlet_created", [$this->startDate->format('Y-m-d'), $this->endDate->format('Y-m-d')]);
        }
//        if($this->startDate != "" || $this->startDate != null ){
//            $model = $model->where("outlet_created", '>=', $this->startDate);
//        }
//
//        if($this->endDate != "" || $this->endDate != null ){
//            $model = $model->where("outlet_created", '<=', $this->endDate);
//        }
        return view('Juragan/exportJuraganOutletStatus', [
            'datas' => $model->get()
        ]);
    }


}