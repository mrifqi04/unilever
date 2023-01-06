<?php
namespace App\Models\JuraganManagement;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Login
 *
 * @author nuansa.ramadhan
 * @property string $id primary key
 * @property string $juragan_id
 * @property string $username
 * @property string $password hash using bcrypt
 * @property boolean $is_active
 * @property \Carbon $last_login
 * @property \Carbon $created_at
 * @property string $created_by
 * @property \Carbon $updated_at
 * @property string $updated_by
 * @property integer $is_deleted
 */
class Login extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'juragan.logins';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';


    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function login()
    {
        return $this->belongsTo(\App\Models\Juragan::class);
    }
}