<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 4/6/18
 * Time: 18:20
 */

namespace App\erpmonster\FwApp\Services;

use Erpmonster\FwApp\Models\FwObject;
use Erpmonster\FwApp\Models\FwObjectTableField;
use Erpmonster\FwApp\Repositories\FwObjectTableFieldRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;

class FwDefinitionEditService
{

    private $tableFieldRepository;
    private $databaseManager;
    private $dispatcher;

    public function __construct(
        DatabaseManager $databaseManager,
        Dispatcher $dispatcher)

    {
        $this->databaseManager = $databaseManager;
        $this->dispatcher = $dispatcher;
        $this->tableFieldRepository = new FwObjectTableFieldRepository();

    }

    public function getTableFields($objId)
    {
        return $this->tableFieldRepository->getFwObjectTableFields($objId);
    }
    public function CreateFwObjectTableField($data)
    {
        return $this->tableFieldRepository->create($data);
    }

    public function UpdateFwObjectTableField($id, $data)
    {
        $model = $this->tableFieldRepository->getById($id);

        return $this->tableFieldRepository->update($model, $data);
    }

    public function DeleteFwObjectTableField($id)
    {
        $model = $this->tableFieldRepository->getById($id);

        if ($model){
            $this->tableFieldRepository->delete($id);
        }

    }

    public function CreateFwObjectEditField()
    {

    }

    public function UpdateFwObjectEditField()
    {

    }
}
