<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 3/4/18
 * Time: 00:25
 */

namespace Erpmonster\FwApp\Models;


use Erpmonster\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class FwObjectTableField extends Model
{


    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['text', 'value', 'class'];

    protected $fillable = ['fw_object_id', 'field_name', 'title', 'td_class_name', 'td_control', 'is_hidden',
        'searchable', 'sortable', 'order_index'];

    public function getTextAttribute()
    {
        return $this->title;
    }

    public function getValueAttribute()
    {
        return $this->field_name;
    }

    public function getClassAttribute()
    {
        return $this->td_class_name;
    }

    protected $casts = [
        'sortable' => 'boolean',
        'searchable' => 'boolean',
        'is_hidden' => 'boolean'
    ];
}
