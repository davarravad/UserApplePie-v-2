<?php

use Helpers\Hooks;

Hooks::addhook('routes', 'Modules\AdminPanel\Controllers\AdminPanel@routes');
