<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 3/4/18
 * Time: 00:23
 */

namespace Erpmonster\FwApp\Models;

use Erpmonster\Database\Eloquent\Model;

class FwObject extends Model
{
    public function tableFields()
    {
        return $this->hasMany(FwObjectTableField::class);
    }

    public function headers()
    {
        return $this->hasMany(FwObjectTableField::class);
    }

    public function editFields()
    {
        return $this->hasMany(FwObjectEditField::class);
    }

    protected $casts = [
        'checkbox_select' => 'boolean'
    ];
}
