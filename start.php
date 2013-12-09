<?

function backup_tool_init() {

	$plugins_path = elgg_get_plugins_path();
	elgg_register_action("backup-tool/create", "{$plugins_path}backup-tool/actions/create.php", "admin");
	elgg_register_action("backup-tool/download", "{$plugins_path}backup-tool/actions/download.php", "admin");
	elgg_register_action("backup-tool/remove", "{$plugins_path}backup-tool/actions/remove.php", "admin");
	elgg_register_action("backup-tool/ftp-test", "{$plugins_path}backup-tool/actions/ftp-test.php", "admin");

	elgg_register_action("backup-tool/schedule-settings", "{$plugins_path}backup-tool/actions/schedule-settings.php", "admin");

	elgg_register_library("backup_tool", "{$plugins_path}backup-tool/lib/backup-tool.php");

	elgg_register_css('backup_tool', elgg_get_simplecache_url('css', 'backup_tool'));
	elgg_register_simplecache_view('css/backup_tool');

	elgg_register_js('backup_tool', elgg_get_simplecache_url('js', 'backup_tool'));
	elgg_register_simplecache_view('js/backup_tool');

	//register cron jobs only if schedule was enabled
	if (elgg_get_plugin_setting('enable_schedule', 'backup-tool')) {
		$schedule_period = elgg_get_plugin_setting('schedule_period', 'backup-tool');
		$schedule_delete = elgg_get_plugin_setting('schedule_delete', 'backup-tool');

		if ($schedule_period != "never") {
			// Register cron hook for creating backup
			elgg_register_plugin_hook_handler('cron', $schedule_period, 'backup_tool_cron');
		}

		elgg_register_plugin_hook_handler('cron', 'monthly', 'backup_tool_monthly_cron');

		if ($schedule_delete != 'never') {
			// Register cron hook for deletion of selected archived logs
			elgg_register_plugin_hook_handler('cron', 'daily', 'backup_tool_cleanup_cron');
		}
	}

	//register view of dialog of backup creation
	elgg_register_ajax_view('backup-tool/create-backup');
}

function backup_tool_pagesetup() {
	//add menu item on admin panel to update source from svn
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('administer', 'list', 'backups', 0);
		elgg_register_admin_menu_item('administer', 'schedule', 'backups', 0);
	}
}

function backup_tool_cron($hook, $entity_type, $returnvalue, $params) {

	elgg_load_library("backup_tool");
	$backup_options = unserialize(elgg_get_plugin_setting('backup_options', 'backup-tool'));
	$filename = backup_tool_create_backup($backup_options);

	//get path to default backup dir specified in plugin settings
	$backup_dir = elgg_get_plugin_setting('backup_dir', 'backup-tool');
	//get ftp settings
	$ftp_enable = elgg_get_plugin_setting('ftp_enable', 'backup-tool');

	if ($ftp_enable == "ON") {
		//connect to remote ftp server
		backup_tool_upload_to_ftp($filename);
	}


	return $returnvalue;
}

/**
 * Creates a monthly backup in another folder to have better saving integration
 * 
 * @param type $hook
 * @param type $entity_type
 * @param type $returnvalue
 * @param type $params
 * @return type
 */

function backup_tool_monthly_cron($hook, $entity_type, $returnvalue, $params) {
	$monthly_backup_dir = elgg_get_plugin_setting('monthly_backup_dir', 'backup-tool');
	if (empty($monthly_backup_dir) || !is_dir($monthly_backup_dir)) {
		return $returnvalue;
	}
	
	
	elgg_load_library("backup_tool");
	$backup_options = unserialize(elgg_get_plugin_setting('backup_options', 'backup-tool'));
	
	if (!is_array($backup_options)) {
		$backup_options = array();
	}

	$backup_options['backup_dir'] = $monthly_backup_dir;
	$filename = backup_tool_create_backup($backup_options);
	
	//get ftp settings
	$ftp_enable = elgg_get_plugin_setting('ftp_enable', 'backup-tool');

	if ($ftp_enable == "ON") {
		//connect to remote ftp server
		backup_tool_upload_to_ftp($filename);
	}


	return $returnvalue;
}

function backup_tool_cleanup_cron($hook, $entity_type, $returnvalue, $params) {

	elgg_load_library("backup_tool");

	$period = elgg_get_plugin_setting('schedule_delete', 'backup-tool');

	$day = 86400;
	$offset = 0;

	switch ($period) {
		case 'weekly':
			$offset = $day * 7;
			break;
		case 'yearly':
			$offset = $day * 365;
			break;
		case 'monthly':
		default:
			// assume 28 days even if a month is longer. Won't cause data loss.
			$offset = $day * 28;
	}

	backup_tool_cleanup($offset);


	return $returnvalue;
}

elgg_register_event_handler('init', 'system', 'backup_tool_init');
elgg_register_event_handler('pagesetup', 'system', 'backup_tool_pagesetup', 1000);
