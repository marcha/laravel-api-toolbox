<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/28/18
 * Time: 19:19
 */

namespace Erpmonster\FwApp\Services;

use Erpmonster\FwApp\Models\Menu;

use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Erpmonster\FwApp\Events\MenuWasCreated;
use Erpmonster\FwApp\Events\MenuWasDeleted;
use Erpmonster\FwApp\Events\MenuWasUpdated;
use Erpmonster\FwApp\Repositories\MenuRepository;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var MenuRepository
     */
    private $repository;

    private $menuFields = [
        'menus.id',
        'menus.parent_id',
        'menus.title',
        'menus.is_model',
        'menus.is_heading',
        'menus.route',
        'menus.icon',
        'menus.icon_alt',
        'menus.color',
        'menus.order_index'
    ];

    public function __construct(
        DatabaseManager $databaseManager,
        Dispatcher $dispatcher,
        MenuRepository $repository)
    {
        $this->databaseManager = $databaseManager;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }
    
    public function getAll($options=[])
    {
        return $this->repository->getWithPagination($options);
    }

    public function create($data)
    {
        $model = $this->repository->create($data);
        Cache::tags('menu')->flush();
        $this->dispatcher->dispatch(new MenuWasCreated($model));

        return $model;
    }

    public function update($id, array $data)
    {
        $model = $this->repository->getRequested($id);

        $this->repository->update($model, $data);
        Cache::tags('menu')->flush();
        $this->dispatcher->dispatch(new MenuWasUpdated($model));

        return $model;
    }

    public function delete($id)
    {
        $model = $this->repository->getRequested($id);

        $this->repository->ddelete($id);
        Cache::tags('menu')->flush();
        $this->dispatcher->dispatch(new MenuWasDeleted($model));

    }

    public function userMenu($appId, $userId = null){

        $cacheName = 'app_user_menu-'.$appId.'-'.$userId;

        $data = Cache::tags(['app_user_menu', 'menu'])->remember($cacheName, 1440, function() use ($appId, $userId) {

            $appItems = Menu::join('fw_app_menu', 'fw_app_menu.menu_id', '=', 'menus.id')
                ->join('fw_app_user', 'fw_app_user.app_id','=','fw_app_menu.app_id')
                ->where('menus.hidden', 0)
                ->where('fw_app_menu.app_id', $appId)
                ->where('fw_app_user.user_id', $userId)
                ->orderBy('menus.order_index')
                ->select($this->menuFields)->get();

            $appItemsArray = $appItems->toArray();

            return $this->buildTreeArray($appItemsArray);
        });

        return $data;
    }

    public function getAppDevelopmentMenu($appId)
    {
        $cacheName = 'app_dev_menu-'.$appId;

        $data = Cache::tags(['app_dev_menu', 'menu'])->remember($cacheName, 1440, function() use ($appId) {

            $appItems = Menu::join('fw_app_menu', 'fw_app_menu.menu_id', '=', 'menus.id')
                ->where('menus.hidden', 0)
                ->whereIn('fw_app_menu.app_id', [1, $appId])
                ->orderBy('menus.order_index')
                ->select($this->menuFields)->get();

            $appItemsArray = $appItems->toArray();

            return $this->buildTreeArray($appItemsArray);
        });

        return $data;
    }

    private function buildTreeArray(array &$elements, $parentId = null, $idField='id', $parentIdField='parent_id', $childrenArrName='children') {

        $branch = null;

        foreach ($elements as &$element) {

            if ($element[$parentIdField] == $parentId) {
                $children = $this->buildTreeArray($elements, $element[$idField], $idField, $parentIdField, $childrenArrName);

                if ($children) {
                    usort($children, function($a, $b) {
                        return $a['order_index'] - $b['order_index'];
                    });
                    $element[$childrenArrName] = $children;
                }
                $branch[] = $element;
                unset($element);
            }

        }
        return $branch;
    }

}
