<?php
require_once __DIR__ . '/../core/db.php';

$db = DB::getConnection();
echo "Database connected successfully!";