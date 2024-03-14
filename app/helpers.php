<?php
require_once __DIR__ . '/../app/bootstrap.php';


function renderView($view, $data = []) {
    extract($data);
    ob_start();
    include __DIR__ . "/Views/$view.php";
    $content = ob_get_clean();
    include __DIR__ . "/Views/layouts/main.php";
}

function asset($path) {
    return BASE_URL . '/' . $path;
}
