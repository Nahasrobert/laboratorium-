<?php
require_once __DIR__ . '/app/bootstrap.php';
$pageSlug = isset($_GET['slug']) && is_string($_GET['slug']) ? $_GET['slug'] : '';
require __DIR__ . '/page-view.php';
