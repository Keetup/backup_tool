<?php

require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/engine/start.php');
admin_gatekeeper();

elgg_load_library('backup_tool');


$filename = dirname(__FILE__).'/to_upload.txt';
backup_tool_upload_to_ssh($filename);

die;

