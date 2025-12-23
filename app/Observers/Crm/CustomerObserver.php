<?php

namespace App\Observers\Crm;

use App\Models\Crm\Customer;
use App\Observers\Concerns\LogsDeletion;

class CustomerObserver
{
    use LogsDeletion;
}
