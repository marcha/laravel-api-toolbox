<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 3/4/18
 * Time: 00:19
 */

namespace Erpmonster\FwApp\Services;


use Erpmonster\FwApp\Models\FwObject;
use Erpmonster\FwApp\Repositories\FwEloquentRepository;
use Erpmonster\FwApp\Repositories\FwRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;

class FwService
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    private $repository;
    public $definition;
    /**
     * FwService constructor.
     * @param DatabaseManager $databaseManager
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        DatabaseManager $databaseManager,
        Dispatcher $dispatcher
    )
    {
        $this->databaseManager = $databaseManager;
        $this->dispatcher = $dispatcher;
    }

    public function setObjectDefinition($objId)
    {
        $obj = FwObject::find($objId);

        if ($obj) {
            $this->definition = $obj->with('tableFields', 'editFields')->get();

            $this->repository = new $obj->repository_name;

            if ($this->repository instanceOf FwEloquentRepository) {
                $this->repository->setModel($obj->model_name);
            }
        }
    }

    public function update($id, array $data)
    {
        $model = $this->getById($id);

        return $this->repository->update($model, $data);

    }



    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function get($options=[])
    {
        return $this->repository->getWithPagination($options);
    }

    public function lookup($keyField, $displayField, $selectedId, $q)
    {
        return $this->repository->lookup($keyField, $displayField, $selectedId, $q);
    }
}
