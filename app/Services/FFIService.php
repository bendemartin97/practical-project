<?php

namespace App\Services;

use App\Models\Result;
use FFIMe\FFIMe;

class FFIService implements ConnectionInterface
{
    const TYPE = "ffi";
    private mixed $library;

    public function __construct()
    {
        $sharedObjectPath = config('app.go_shared_object');
        $sharedHeaderPath = config('app.go_shared_header');

        $this->library = (new FFIMe($sharedObjectPath))
            ->showWarnings(false)
            ->include($sharedHeaderPath)
            ->build();
    }
    public function calculate(): Result
    {
        return Result::fromJsonString(
            $this->library->calculate()->toString(),
            self::TYPE
        );
    }
}
