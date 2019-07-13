<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/28/18
 * Time: 13:43
 */

namespace Erpmonster\FwApp\Controllers;

use Erpmonster\FwApp\Services\FwAppService;
use Erpmonster\Http\Controller;
use Illuminate\Http\Request;

class FwAppsController extends Controller
{
    /**
     * @var FwAppService
     */
    private $service;

    public function __construct(FwAppService $service)
    {
        $this->service = $service;
    }

    public function get($id=null)
    {
        if ($id){

            $data = $this->service->getById($id);

            return $this->response($data);

        }
        $resourceOptions = $this->parseResourceOptions();

        $data = $this->service->getAll($resourceOptions);

        $parsedData = $this->parseData($data, $resourceOptions);

        return $this->response($parsedData);

    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:64',
            'name'  => 'required|string|max:64'
        ]);

        $data = $request->input();

        return $this->response($this->service->create($data), 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|string|max:64',
            'name'  => 'required|string|max:64'
        ]);

        $data = $request->input();

        return $this->response($this->service->update($id, $data));
    }

    public function delete($id)
    {
        return $this->response($this->service->delete($id));
    }
}

