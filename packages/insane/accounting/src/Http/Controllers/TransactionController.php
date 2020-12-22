<?php

namespace Insane\Accounting\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use Insane\Accounting\Models\Transaction;

class TransactionController extends BaseController
{

    public function __construct()
    {
        $this->model = new Transaction();
        $this->searchable = ['name'];
        $this->validationRules = [];
    }
}
