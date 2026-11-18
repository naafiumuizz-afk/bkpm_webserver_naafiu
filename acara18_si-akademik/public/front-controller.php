<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Core/autoload.php';

(new App\Core\Router(require __DIR__ . '/../routes/web.php'))->dispatch();
