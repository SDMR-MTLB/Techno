<?php
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/validator.php';

$db = DB::getConnection();
echo "Core files loaded and database connected!";