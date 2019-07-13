<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 3/5/18
 * Time: 08:13
 */

namespace Erpmonster\FwApp\Repositories;


use Erpmonster\FwApp\Models\FwObject;
use Erpmonster\Repositories\EloquentRepository;

class FwDefinitionRepository extends EloquentRepository
{
    public function getModel()
    {
        return new FwObject();
    }
}
