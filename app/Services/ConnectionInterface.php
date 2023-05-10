<?php

namespace App\Services;

use App\Models\Result;

interface ConnectionInterface
{
    public function calculate(): Result;
}
