<?php
namespace App\Models\UserManagement;
/**
 * Description of Menu
 *
 * @author nuansa.ramadhan
 */

use Illuminate\Database\Eloquent\Model;

class Menu extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'public.menus';
    public $guarded = ['id'];
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function submenus(){
        return $this->hasMany(SubMenu::class);
    }
}