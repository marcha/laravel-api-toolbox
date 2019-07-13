<?php

namespace Erpmonster\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    use EloquentBuilderTrait;
}
