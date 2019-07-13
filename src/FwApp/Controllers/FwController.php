<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 3/3/18
 * Time: 23:55
 */

namespace Erpmonster\FwApp\Controllers;


use Erpmonster\FwApp\Models\FwObject;
use Erpmonster\FwApp\Services\FwService;
use Erpmonster\Http\Controller;
use Illuminate\Http\Request;

class FwController extends Controller
{

    protected $service;

    protected $fw_obj_id;

    public function __construct(Request $request, FwService $service)
    {
        $this->fw_obj_id   = $request->get('fw_obj_id',0);

        if ($this->fw_obj_id == 0){
            $this->fw_obj_id = $request->segment(2);
        }

        $this->service = $service;

        $this->service->setObjectDefinition($this->fw_obj_id);
    }

    public function get($objId, $id = null)
    {
        $resourceOptions = $this->parseResourceOptions();

        if ($id) {

            $data = $this->service->getById($id);


        } else {
            $data = $this->service->get($resourceOptions);
        }


        $parsedData = $this->parseData($data, $resourceOptions);

        return $this->response($parsedData);
    }


    public function update(Request $request, $objId, $id)
    {
        $data = $request->input();

        $data = $this->service->update($id, $data);

        return $this->response($data);
    }

    public function lookup(Request $request, $objId){

        $keyField = $request->get('lookupKeyField');

        $displayField = $request->get('lookupDisplayField');

        $selectedId = $request->get('selectedId');

        $q = $request->get('q', '');

        $q = trim($q);

        $lookup = $this->service->lookup($keyField, $displayField, $selectedId, $q);

        return $this->response($lookup);

    }

}
