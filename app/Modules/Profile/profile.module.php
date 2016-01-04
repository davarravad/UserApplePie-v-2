<?php

use Helpers\Hooks;

Hooks::addhook('routes', 'Modules\Profile\Controllers\Profile@routes');