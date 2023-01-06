<?php


namespace App\Http\Controllers\Auditor;


use App\Http\Controllers\Controller;
use App\Http\Controllers\GenericController;
use App\Models\Auditor\AuditPlan;
use App\Models\Auditor\JourneyPlan;
use App\Models\OutletManagement\MapOutlet;
use App\Models\OutletManagement\Outlet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class AuditPlanController extends Controller
{
    /**
     * @param AuditPlan $auditPlan
     * @return array
     */
    public static function map(AuditPlan $auditPlan)
    {
        $result = ['id' => $auditPlan->id];
        $outlet = $auditPlan->outlet;
        if (!is_null($outlet)) {
            $result['outlet'] = [
                'id' => $outlet->id,
                'name' => $outlet->name,
            ];
        } else {
            $result['outlet'] = null;
        }
        return $result;
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function index($id)
    {
        try {
            $records = AuditPlan::with(['outlet'])
                ->where('is_deleted', '=', 1)
                ->where('id_journey_plan', '=', $id)
                ->get();
            $collection = [];
            foreach ($records as $record) {
                $collection[] = self::map($record);
            }
            $result = [
                'code' => 200,
                'message' => 'OK',
                'data' => $collection,
            ];
        } catch (\Exception  $e) {
            $result = [
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['code']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function outlet(Request $request)
    {  
        $request   = Request::capture();
        $idJourneyPlan = $request->get('id_journey_plan', '');
        $idJuragan = $request->get('id_juragan', '');
        $draw      = $request->query('draw', '');
        $start     = $request->query('start', '');
        $pageSize  = $request->query('length', '');
        $page      = ($start > 0) ? ($start / $pageSize + 1) : 1;
        $keyword   = $request->get('search')['value'];
        $getOrder  = $request->get('order', []);
        $listOrder = ['id', 'id', 'name'];
        $order     = $listOrder[$getOrder[0]['column']];
        $orderType = $getOrder[0]['dir'] ? $getOrder[0]['dir'] : 'asc';

        $query = Outlet::whereHas('mapOutlet', function (Builder $query) {
            $query->where('is_mitra', '=', 1);
        })->where('is_deleted', '=', 1)
            ->where('id_juragan', '=', $idJuragan);
        if ($idJourneyPlan != '') {
            $query->whereRaw('id NOT IN(SELECT id_outlet FROM auditor.audit_plans WHERE is_deleted = 1 AND id_journey_plan = ?)',
                [$idJourneyPlan]);
        }
        $total       = $query->count();
        $totalFilter = $total;

        if ($keyword) {
            $query->where(function($query) use ($keyword) {
                $query->WhereRaw("id ilike ?", ["%" . $keyword . "%"])
                            ->orWhereRaw("name ilike ?", ["%" . $keyword . "%"]);
            });
            $totalFilter = $query->count();
        }

        $records = $query->paginate($pageSize, ['*'], 'page', $page);
        $output['draw'] = $draw;
        $output['recordsTotal'] = $total;
        $output['recordsFiltered'] = $totalFilter;
        $output['data'] = [];

        foreach ($records as $key => $value) {
            $output['data'][] = [
                '<input type="checkbox" id="val'."$key".'" class="id_outlets" name="id_outlets[]" value='."$value->id".'>&nbsp;',
                $value->id,
                $value->name
            ];
        }

        return response()->json($output);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $idJourneyPlan = $request->json('id_journey_plan', '');
        $idOutlet = $request->json('id_outlet', []);
        $createdBy = $request->json('created_by', '');
        try {
            $validator = Validator::make(
                [
                    'id_journey_plan' => $idJourneyPlan,
                    'id_outlet' => $idOutlet,
                    'created_by' => $createdBy,
                ],
                [
                    'id_journey_plan' => [
                        'required',
                        'exists:App\Models\Auditor\JourneyPlan,id,is_deleted,1'
                    ],
                    'id_outlet.*' => [
                        'required',
                        function ($attribute, $value, $fail) use ($idJourneyPlan) {
                            try {
                                $journeyPlan = JourneyPlan::where('is_deleted', 1)
                                    ->where('id', '=', $idJourneyPlan)
                                    ->firstOrFail();
                            } catch (ModelNotFoundException $e) {
                                $fail($attribute . ' journey plan is invalid.');
                                return;
                            }
                            try {
                                $mapOutlet = MapOutlet::whereHas('outlet', function (Builder $query) use ($journeyPlan) {
                                    $query->where('id_juragan', '=', $journeyPlan->id_juragan);
                                })->where('is_mitra', '=', 1)
                                    ->where('id_outlet', '=', $value)
                                    ->firstOrFail();
                            } catch (ModelNotFoundException $e) {
                                $fail($attribute . ' ' . $value . ' is invalid.');
                                return;
                            }
                        },
                    ],
                    'created_by' => 'required',
                ]
            );
            if ($validator->fails()) {
                $result = [
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ];
            } else {
                $now = Carbon::now();
                $records = [];
                foreach ($idOutlet as $item) {
                    $records[] = [
                        'id' => Uuid::uuid4()->toString(),
                        'id_journey_plan' => $idJourneyPlan,
                        'id_outlet' => $item,
                        'is_deleted' => 1,
                        'created_at' => $now->unix(),
                        'created_by' => $createdBy,
                        'updated_at' => $now->unix(),
                        'updated_by' => $createdBy,
                    ];
                }
                AuditPlan::insert($records);
                $result = [
                    'code' => 200,
                    'message' => 'OK',
                ];
            }
        } catch (\Exception  $e) {
            $result = [
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['code']);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $idJourneyPlan = $request->json('id_journey_plan', '');
        $idOutlet = $request->json('id_outlet', '');
        $updatedBy = $request->json('updated_by', '');
        try {
            $validator = Validator::make(
                [
                    'id' => $id,
                    'id_journey_plan' => $idJourneyPlan,
                    'id_outlet' => $idOutlet,
                    'updated_by' => $updatedBy,
                ],
                [
                    'id' => [
                        'required',
                        'exists:App\Models\Auditor\AuditPlan,id,is_deleted,1'
                    ],
                    'id_journey_plan' => [
                        'required',
                        'exists:App\Models\Auditor\JourneyPlan,id,is_deleted,1'
                    ],
                    'id_outlet' => [
                        'required',
                        function ($attribute, $value, $fail) use ($idJourneyPlan) {
                            try {
                                $journeyPlan = JourneyPlan::where('is_deleted', 1)
                                    ->where('id', '=', $idJourneyPlan)
                                    ->firstOrFail();
                            } catch (ModelNotFoundException $e) {
                                $fail($attribute . ' journey plan is invalid.');
                                return;
                            }
                            try {
                                $mapOutlet = MapOutlet::whereHas('outlet', function (Builder $query) use ($journeyPlan) {
                                    $query->where('id_juragan', '=', $journeyPlan->id_juragan);
                                })->where('is_mitra', '=', 1)
                                    ->where('id_outlet', '=', $value)
                                    ->firstOrFail();
                            } catch (ModelNotFoundException $e) {
                                $fail($attribute . ' is invalid.');
                                return;
                            }
                        },
                    ],
                    'updated_by' => 'required',
                ]
            );
            if ($validator->fails()) {
                $result = [
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ];
            } else {
                $now = Carbon::now();
                $auditPlan = AuditPlan::find($id);
                $auditPlan->id_outlet = $idOutlet;
                $auditPlan->updated_at = $now->unix();
                $auditPlan->updated_by = $updatedBy;
                $auditPlan->save();
                $result = [
                    'code' => 200,
                    'message' => 'OK',
                    'data' => [
                        'id' => $auditPlan->id,
                    ],
                ];
            }
        } catch (\Exception  $e) {
            $result = [
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['code']);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $deletedBy = $request->json('deleted_by', '');
        try {
            $validator = Validator::make(
                [
                    'id' => $id,
                    'deleted_by' => $deletedBy,
                ],
                [
                    'id' => [
                        'required',
                        'exists:App\Models\Auditor\AuditPlan,id,is_deleted,1'
                    ],
                    'deleted_by' => 'required',
                ]
            );
            if ($validator->fails()) {
                $result = [
                    'code' => 400,
                    'message' => $validator->errors()->first() . ' - ' . $id,
                ];
            } else {
                $now = Carbon::now();
                $auditPlan = AuditPlan::find($id);
                $auditPlan->is_deleted = 2;
                $auditPlan->updated_at = $now->unix();
                $auditPlan->updated_by = $deletedBy;
                $auditPlan->save();
                $result = [
                    'code' => 200,
                    'message' => 'OK'
                ];
            }
        } catch (\Exception $e) {
            $result = [
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['code']);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function destroyAll(Request $request, $id)
    {
        $deletedBy = $request->json('deleted_by', '');
        try {
            $validator = Validator::make(
                [
                    'id' => $id,
                    'deleted_by' => $deletedBy,
                ],
                [
                    'id' => [
                        'required',
                        'exists:App\Models\Auditor\JourneyPlan,id,is_deleted,1'
                    ],
                    'deleted_by' => 'required',
                ]
            );
            if ($validator->fails()) {
                $result = [
                    'code' => 400,
                    'message' => $validator->errors()->first() . ' - ' . $id,
                ];
            } else {
                $now = Carbon::now();
                AuditPlan::where('id_journey_plan', '=', $id)
                    ->where('is_deleted', '=', 1)
                    ->update([
                        'is_deleted' => 2,
                        'updated_at' => $now->unix(),
                        'updated_by' => $deletedBy,
                    ]);
                $result = [
                    'code' => 200,
                    'message' => 'OK'
                ];
            }
        } catch (\Exception $e) {
            $result = [
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['code']);
    }

}