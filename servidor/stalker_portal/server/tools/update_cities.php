<?php

\set_time_limit(0);
require __DIR__ . '/../common.php';
use Ministra\Lib\L10n;
$l10n = new \Ministra\Lib\L10n();
$l10n->updateCitiesInfo();
