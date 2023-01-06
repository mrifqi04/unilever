<?php

namespace App\Models\CallCenter;

use App\Models\OutletManagement\MapOutlet;
use App\Models\OutletManagement\Outlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class OutletRetractionProgress
 * @package App\Models\Unilever
 * @property string $id
 * @property string $id_outlet_activity
 * @property string $status_progress
 * @property int $is_deleted
 * @property Carbon $created_date
 * @property string $section
 * @property Carbon $recommend_date
 * @property string $key
 * @property string $value
 * @property Carbon $send_date
 * @property Carbon $final_send_date
 * @property int $created_at
 * @property int $updated_at
 * @property string $created_by
 * @property string $created_by_name
 * @property string $updated_by
 * @property string $updated_by_name
 * @property int $status_active
 * @property string $id_map_outlet
 * @property string $id_answer
 * @method static OutletRetractionProgress find(string $id)
 */
class OutletRetractionProgress extends Model
{
    protected $table = "outlet.outlet_retraction_progress";
    protected $primaryKey = "id";
    protected $keyType = "string";

    public $incrementing = false;
    public $timestamps = false;

    /**
     * @return bool
     */
    public function canApprove()
    {
        return (
            ($this->status_progress === '1' && $this->section === 'uli') ||
            ($this->status_progress === '3' && $this->section === 'callcenter')
        ) && $this->status_active === 1;
    }

    /**
     * @return bool
     */
    public function canCancel()
    {
        return (
            ($this->status_progress === '1' && $this->section === 'uli') ||
            ($this->status_progress === '3' && $this->section === 'callcenter')
        ) && $this->status_active === 1;
    }

    /**
     * @return bool
     */
    public function canPostpone()
    {
        return $this->status_progress === '1' && $this->section === 'uli' && $this->status_active === 1;
    }

    /**
     * @return Outlet|null
     */
    public function outlet()
    {
        $mapOutlet = MapOutlet::find($this->id_map_outlet);
        if (!is_null($mapOutlet)) {
            return Outlet::find($mapOutlet->id_outlet);
        }
        return null;
    }

    /**
     * @return Collection
     */
    public function getPictures(): Collection
    {
        $record = DB::table('hunter.answers')
            ->where('id', '=', $this->id_answer)
            ->first();
        if (is_null($record)) {
            return collect([]);
        }
        $answers = json_decode($record->answers);
        $pictureIds = [];
        if (property_exists($answers, 'picture') && is_array($answers->picture)) {
            foreach ($answers->picture as $picture) {
                if (property_exists($picture, 'id')) {
                    $pictureIds[] = $picture->id;
                }
            }
        }
        $result = DB::table('hunter.answer_image')
            ->where('is_deleted', '=', 1)
            ->whereIn('id', $pictureIds)
            ->get();
        return $result;
    }

    /**
     * @param string|null $id_answer
     * @return Collection
     */
//    public static function getPictureIds(?string $id_answer): Collection
    public static function getPictureIds($answerImages, $signatureId): Collection
    {
        $imageIds = [];
        if ($signatureId) {
            $imageIds[] = $signatureId;
        }

        if (!empty($answerImages)) {
            $images = json_decode($answerImages);
            foreach ($images as $image) {
                if (property_exists($image, 'id')) {
                    $imageIds[] = $image->id;
                }
            }
        }

        $result = DB::table('hunter.answer_image')
            ->where('is_deleted', '=', 1)
            ->whereIn('id', $imageIds)
            ->get('id');
        return $result;
    }

    /**
     * @param null|string $id
     * @return null|mixed
     */
    public static function findPicture(?string $id)
    {
        $result = DB::table('hunter.answer_image')
            ->where('is_deleted', '=', 1)
            ->where('id', '=', $id)
            ->first();
        return $result;
    }
}
