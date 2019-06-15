<?php

require __DIR__ . '/vendor/autoload.php';

use App\GuiaTrabalhista;

$guiaTrabalhista = new GuiaTrabalhista();
echo $guiaTrabalhista->getSalarioMinimo();
