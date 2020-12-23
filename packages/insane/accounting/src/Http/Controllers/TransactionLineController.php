<?php

namespace Insane\Accounting\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use Insane\Accounting\Models\TransactionLine;

class TransactionLineController extends BaseController
{
    public function __construct()
    {
        $this->model = new TransactionLine();
        $this->searchable = ['name'];
        $this->validationRules = [];
    }
}
