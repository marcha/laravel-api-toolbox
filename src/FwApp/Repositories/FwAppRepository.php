<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/28/18
 * Time: 16:18
 */

namespace Erpmonster\FwApp\Repositories;


use Erpmonster\FwApp\Models\FwApp;
use Erpmonster\Repositories\EloquentRepository;

class FwAppRepository extends EloquentRepository
{
    public function getModel()
    {
        return new FwApp();
    }

    public function update(FwApp $model, array $data)
    {
        $model->fill($data);

        $model->save();

        return $model;
    }
}
