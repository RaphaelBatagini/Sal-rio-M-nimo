<?php

require __DIR__ . '/vendor/autoload.php';

use App\LaborGuide;

echo '<pre>';
var_dump(LaborGuide::getMinimumWage());
echo '</pre>';