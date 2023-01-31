<?php

namespace App\Http\Traits;

use App\Http\Enums\ReleaseStatus;

/**
 * @property int $status
 */
trait ReleaseStatusTrait
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!in_array("status", $this->fillable))
            $this->fillable[] = "status";

        $this->casts["status"] = "integer";
    }

    public function getStatusString(): string
    {
        if ($this->status == ReleaseStatus::RELEASED)
            return "Released";

        return "Coming soon";
    }
}
