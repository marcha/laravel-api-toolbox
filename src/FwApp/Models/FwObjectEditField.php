<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 3/4/18
 * Time: 00:29
 */

namespace Erpmonster\FwApp\Models;


use Erpmonster\Database\Eloquent\Model;

class FwObjectEditField extends Model
{
    protected $fillable = ['fw_object_id', 'field_name', 'title', 'default_value', 'input_type', 'input_class_name',
        'validation_cli', 'validation_srv', 'lookup_fw_object_id', 'lookup_key_field_name', 'lookup_display_field_name',
        'order_index'];
    protected $casts = [
        'is_heading' => 'boolean',
        'hidden' => 'boolean'
    ];
}
