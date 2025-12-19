<?php

namespace App\Observers;

use App\Models\Customer;
use App\Observers\Concerns\LogsDeletion;

class CustomerObserver
{
    use LogsDeletion;
}
