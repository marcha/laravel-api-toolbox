<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/28/18
 * Time: 14:49
 */

namespace Erpmonster\FwApp\Controllers;

use Erpmonster\FwApp\Services\MenuService;
use Erpmonster\Http\Controller;
use Illuminate\Http\Request;

class MenusController extends Controller
{
    /**
     * @var MenuService
     */
    private $service;

    public function __construct(MenuService $service)
    {
        $this->service = $service;
    }

    public function get($id=null)
    {
        if ($id) {

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
            'title'     => 'required|string|max:32',
            'parent_id' => 'integer',
            'icon'      => 'string|max:32',
            'route'     => 'string|max:128',
            'color'     => 'string|max:32'
        ]);

        $data = $request->input();

        return $this->response($this->service->create($data), 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'     => 'required|string|max:32',
            'parent_id' => 'integer',
            'icon'      => 'string|max:32',
            'route'     => 'string|max:128',
            'color'     => 'string|max:32'
        ]);

        $data = $request->input();

        return $this->response($this->service->update($id, $data));
    }

    public function delete($id)
    {
        return $this->response($this->service->delete($id));
    }

    public function getUserMenu($appId, $userId = null)
    {
        return $this->response($this->service->userMenu($appId, $userId));
    }

    public function getAppDevelopmentMenu($appId)
    {
        return $this->response($this->service->getAppDevelopmentMenu($appId));
    }

}
