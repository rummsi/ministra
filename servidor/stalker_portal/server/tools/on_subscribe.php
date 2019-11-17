<?php

require __DIR__ . '/../common.php';
if (empty($_REQUEST['mac']) || empty($_REQUEST['tariff_id']) || empty($_REQUEST['package_id'])) {
    echo '{"status":"ERROR","results":false,"error":"mac and tariff_id required"}';
    exit;
}
echo '{"status":"OK","results":true}';
