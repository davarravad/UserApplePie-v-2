<?php

use Helpers\Hooks;

Hooks::addhook('routes', 'Modules\Forum\Controllers\Forum@routes');
