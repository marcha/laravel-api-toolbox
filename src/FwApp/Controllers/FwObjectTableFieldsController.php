<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 4/6/18
 * Time: 15:20
 */

namespace Erpmonster\FwApp\Controllers;

use App\erpmonster\FwApp\Services\FwDefinitionEditService;
use Erpmonster\Http\Controller;
use Illuminate\Http\Request;

class FwObjectTableFieldsController extends Controller
{
    private $service;

    /**
     * FwObjectTableFieldsController constructor.
     * @param FwDefinitionEditService $service
     */
    public function __construct(FwDefinitionEditService $service)
    {
        $this->service = $service;
    }

    public function get($objId)
    {
        $data = $this->service->getTableFields($objId);

        return $this->response($data);
    }

}
