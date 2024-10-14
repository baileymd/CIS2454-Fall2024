<?php

//access info for database
$data_source_name = 'mysql:host=localhost;dbname=stock';
$user_name = 'stockuser';
$password = 'test';
$database = new PDO($data_source_name, $user_name, $password);
