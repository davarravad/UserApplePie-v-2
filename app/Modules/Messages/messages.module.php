<?php

use Helpers\Hooks;

Hooks::addhook('routes', 'Modules\Messages\Controllers\Messages@routes');
