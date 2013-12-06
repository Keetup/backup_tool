<?php

$backup_dir = elgg_get_plugin_setting('backup_dir', 'backup-tool');
if (empty($backup_dir)) {
	$backup_dir = elgg_get_data_path().'site-backups/';
	
	if (!is_dir($backup_dir)) {
		mkdir($backup_dir);
	}
	
	elgg_set_plugin_setting('backup_dir', $backup_dir, 'backup-tool');
}
