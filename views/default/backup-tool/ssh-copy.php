<?php

$ssh = backup_tool_get_ssh_options();

$sync_options = array(
	'scheduled' => elgg_echo('backup-tool:schedule:same_scheduled'),
	'daily' => elgg_echo('backup-tool:schedule:daily'),
	'weekly' => elgg_echo('backup-tool:schedule:weekly'),
	'monthly' => elgg_echo('backup-tool:schedule:monthly'),
	'yearly' => elgg_echo('backup-tool:schedule:yearly'),
);

$ssh_enable = elgg_get_plugin_setting('ssh_enable', 'backup-tool');
?>

<div>
    <h3><?= elgg_echo("backup-tool:schedule:ssh-settings") ?></h3>
    <p class='elgg-subtext'><?= elgg_echo("backup-tool:schedule:ssh-settings:text") ?></p>
</div>

<div>

    <fieldset>
        <p>
			<?
			echo elgg_view("input/checkboxes", array("name" => "ssh-enable", "options" => array(
					elgg_echo('backup-tool:schedule:ssh:enable') => 'ON'
				), 'value' => $ssh_enable));
			?>
        </p>

        <p>
			<?
			echo elgg_echo('backup-tool:schedule:ssh-options');
			echo elgg_view("input/dropdown", array('name' => 'ssh[options]', 'options_values' => $sync_options, 'value' => $ssh['options']));
			?>
        </p>
        <p>
        <p>
			<?
			echo elgg_echo('backup-tool:schedule:ssh-host');
			echo elgg_view("input/text", array('name' => 'ssh[host]', 'value' => $ssh['host']));
			?>
        </p>
        <p>
			<?
			echo elgg_echo('backup-tool:schedule:ssh-port');
			echo elgg_view("input/text", array('name' => 'ssh[port]', 'value' => $ssh['port']));
			?>
        </p>
			<?
			echo elgg_echo('backup-tool:schedule:ssh-user');
			echo elgg_view("input/text", array('name' => 'ssh[user]', 'value' => $ssh['user']));
			?>
        </p>
        <p>
			<?
			echo elgg_echo('backup-tool:schedule:ssh-password');
			echo elgg_view("input/password", array('name' => 'ssh[password]', 'value' => $ssh['password']));
			?>
        </p>
        <p>
			<?
			echo elgg_echo('backup-tool:schedule:ssh-dir');
			echo elgg_view("input/text", array('name' => 'ssh[dir]', 'value' => $ssh['dir']));
			?>
        </p>

		<?php /* ?>
		  <p>
		  <?
		  echo elgg_view("output/url", array(
		  "text" => elgg_echo("backuptool:schedule:ssh:testbutton"),
		  "href" => elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/backup-tool/ssh-test"),
		  "class" => "elgg-button elgg-button-cancel",
		  "id" => "testSshConnection"));
		  ?>
		  </p>
		  <?php */ ?>
    </fieldset>
</div>