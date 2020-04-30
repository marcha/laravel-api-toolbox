<?php

namespace Erpmonster\Http;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends LaravelController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
