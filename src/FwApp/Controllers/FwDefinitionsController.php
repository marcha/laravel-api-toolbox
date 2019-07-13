<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 3/5/18
 * Time: 08:08
 */

namespace Erpmonster\FwApp\Controllers;


use Erpmonster\FwApp\Services\FwDefinitionService;
use Erpmonster\Http\Controller;
use Illuminate\Http\Request;

class FwDefinitionsController extends Controller
{

    /**
     * @var FwDefinitionService
     */
    private $service;

    public function __construct(FwDefinitionService $service)
    {

        $this->service = $service;
    }

    public function definition($id)
    {
        $definition = $this->service->getDefinition($id);

        return $this->response($definition);
    }


}
