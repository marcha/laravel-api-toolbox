<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/28/18
 * Time: 16:19
 */

namespace Erpmonster\FwApp\Services;

use Erpmonster\FwApp\Repositories\FwAppRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;

class FwAppService
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
     * @var FwAppRepository
     */
    private $repository;

    public function __construct(
        DatabaseManager $databaseManager,
        Dispatcher $dispatcher,
        FwAppRepository $repository
        )
    {
        $this->databaseManager = $databaseManager;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function create($data)
    {
        $model = $this->repository->create($data);

        // $this->dispatcher->dispatch(new FwAppWasCreated($model));

        return $model;
    }

    public function update($id, array $data)
    {
        $model = $this->repository->getRequested($id);

        $this->repository->update($model, $data);

       // $this->dispatcher->dispatch(new FwAppWasUpdated($model));

        return $model;
    }

    public function delete($id)
    {
        $model = $this->repository->getRequested($id);

        $this->repository->ddelete($id);

       // $this->dispatcher->dispatch(new FwAppWasDeleted($model));

    }
}
