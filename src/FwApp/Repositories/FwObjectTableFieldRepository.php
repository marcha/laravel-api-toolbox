<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 4/6/18
 * Time: 18:15
 */

namespace Erpmonster\FwApp\Repositories;

use Erpmonster\FwApp\Models\FwObjectTableField;
use Erpmonster\Repositories\EloquentRepository;

class FwObjectTableFieldRepository extends EloquentRepository
{
    public function getModel()
    {
        return new FwObjectTableField();
    }

    public function getFwObjectTableFields($objId)
    {
        return $this->model->where('fw_object_id','=',$objId)->orderBy('order_index')->get();
    }

    public function update(FwObjectTableField $model, array $data)
    {
        $model->fill($data);

        $model->save();

        return $model;
    }

}
