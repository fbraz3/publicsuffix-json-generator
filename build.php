<?php
require_once('public_suffix_json.php');

$obj = new public_suffix_json();
$obj->populate_data();
$obj->git_sync();
