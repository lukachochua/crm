<?php

namespace App\Observers;

use App\Models\Vehicle;
use App\Observers\Concerns\LogsDeletion;

class VehicleObserver
{
    use LogsDeletion;
}
