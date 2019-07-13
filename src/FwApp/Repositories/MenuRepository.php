<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/28/18
 * Time: 16:19
 */

namespace Erpmonster\FwApp\Repositories;

use Erpmonster\FwApp\Models\Menu;
use Erpmonster\Repositories\EloquentRepository;

class MenuRepository extends EloquentRepository
{
    public function getModel()
    {
        return new Menu();
    }

    public function update(Menu $model, array $data)
    {
        $model->fill($data);

        $model->save();

        return $model;
    }

}
