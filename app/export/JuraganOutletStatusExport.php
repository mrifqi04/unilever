<?php

namespace App\export;


use App\Models\JuraganManagement\Juragan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JuraganOutletStatusExport implements FromView {

    private $startDate;
    private $endDate;
    private $id;
    private $cityId;

    public function __construct($startDate, $endDate, $id, $cityId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->id = $id;
        $this->cityId = $cityId;
    }

    public function view(): View {
        $model = new Juragan();
        return view('Juragan/exportOutletStatus', [
            'datas' => $model->statusOutletSummary($this->startDate, $this->endDate, $this->id, $this->cityId)
        ]);
    }



}