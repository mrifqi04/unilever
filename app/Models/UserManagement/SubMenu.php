<?php
namespace App\Models\UserManagement;
/**
 * Description of SubMenu
 *
 * @author nuansa.ramadhan
 */

use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'public.sub_menus';
    public $guarded = ['id'];
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function menu() {
        return $this->belongsTo(Menu::class);
    }

    public function permission(){
        return $this->belongsTo(Permission::class);
    }

}