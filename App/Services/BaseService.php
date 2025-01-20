<?php

namespace App\Services;

/* Base class for all sevices inbclding a model */

use App\Factory\FModel;
use App\Models\BaseModel;
use App\Plugins\Db\Db;
use App\Lib\DbConnection;

abstract class BaseService
{
    /** @var Db  */
    protected $db;

    /** @var string */
    protected $modelName;

    /** 
     * The table model of this service
     * 
     * @var BaseModel
     */
    protected $model;

    /**
     * Constructor
     * 
     * @throws \Exception
     */
    public function __construct()
    {
        $this->db = DbConnection::getConnection();
        $this->model = FModel::build($this->modelName);
    }
}