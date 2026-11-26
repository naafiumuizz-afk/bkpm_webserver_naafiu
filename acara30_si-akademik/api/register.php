<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Core/autoload.php';

(new \App\Controllers\AuthController())->register();
