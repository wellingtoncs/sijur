<?php

include('inc/inc.php');
include_lcm('inc_filters');
include_lcm('inc_contacts');

global $author_session;
$user = array(); // form data
$author = intval(_request('author'));
//FT incluído manager
$statuses = array('admin', 'normal', 'manager', 'external', 'trash'); // , 'suspended'

$meta_subscription = read_meta('site_open_subscription');
if ($meta_subscription == 'moderated' || $meta_subscription == 'yes')
	array_push($statuses, 'waiting');

// Set the returning page
if (_request('ref'))
	$user['ref_edit_author'] = _request('ref');
else
	$user['ref_edit_author'] = $GLOBALS['HTTP_REFERER'];

// Find out if this is existing or new case
$existing = ($author > 0);

if ($existing) {
	// Check if user is permitted to edit this author's data
	//FT dando permissão para o gestor
	if (($author_session['status'] != 'admin') && ($author_session['status'] != 'manager') &&
			($author != $author_session['id_author'])) {
		die(htmlentities("Você não tem permissão para editar os detalhes do usuário"));
	}

	// Get author data
	$q = "SELECT * FROM lcm_author WHERE id_author = $author";
	$result = lcm_query($q);
	if ($row = lcm_fetch_array($result)) {
		foreach ($row as $key => $value) {
			$user[$key] = $value;
		}
	} else {
		lcm_header("Location: listauthors.php");
		exit;
	}
} else {
	$user['id_author'] = 0;
	$user['email'] = '';
	$user['status'] = 'normal';
}

// Fetch values that caused errors to show them with the error message
if (isset($_SESSION['form_data']))
	foreach($_SESSION['form_data'] as $key => $value)
		$user[$key] = $value;

// Start the page with the proper title
if ($existing) lcm_page_start(_T('title_author_edit'));
else lcm_page_start(_T('title_author_new'));

echo show_all_errors($_SESSION['errors']);

?>
<form name="edit_author" method="post" action="upd_author.php">
	<input name="id_author" type="hidden" id="id_author" value="<?php echo $user['id_author']; ?>"/>
	<input name="ref_edit_author" type="hidden" id="ref_edit_author" value="<?php 
			$ref_link = new Link($user['ref_edit_author']);
			echo $ref_link->getUrl();
		?>"/>

	<table width="99%" border="0" align="center" cellpadding="5" cellspacing="0" class="tbl_usr_dtl">
		<?php
			//
			// PERSONAL INFO
			//
		?>
		<tr>
			<td colspan="2" align="center" valign="middle" class="heading">
<?php
			show_page_subtitle(_T('authoredit_subtitle_personalinfo'), 'author_edit');
?>
			</td>
		</tr>

		<tr><td align="left" valign="top"><?php echo f_err_star('name_first') . _T('person_input_name_first'); ?></td>
			<td align="left" valign="top"><input name="name_first" type="text" class="search_form_txt" id="name_first" size="35" value="<?php echo clean_output($user['name_first']); ?>"/></td>
		</tr>

		<?php
			// Middle name can be desactivated, but show anyway if there is one
			if ($user['name_middle'] || read_meta('adverso_name_middle') == 'yes') {
		?>

		<tr><td align="left" valign="top"><?php echo _T('person_input_name_middle'); ?></td>
			<td align="left" valign="top"><input name="name_middle" type="text" class="search_form_txt" id="name_middle" size="35" value="<?php echo clean_output($user['name_middle']); ?>"/></td>
		</tr>

		<?php
			}
		?>
		<tr><td align="left" valign="top"><?php echo f_err_star('name_last') . _T('person_input_name_last'); ?></td>
			<td align="left" valign="top"><input name="name_last" type="text" class="search_form_txt" id="name_last" size="35"  value="<?php echo clean_output($user['name_last']); ?>"/></td>
		</tr>
<?php

	//
	// Contacts (e-mail, phones, etc.)
	//

	echo "<tr>\n";
	echo '<td colspan="2" align="center" valign="middle" class="heading">';
	show_page_subtitle(_T('adverso_subtitle_contacts'), 'contacts');
	echo '</td>';
	echo "</tr>\n";

	show_edit_contacts_form('author', $user['id_author']);

	//
	// LOGIN INFO
	//

	?>
		<tr>
			<td colspan="2" align="center" valign="middle" class="heading">
<?php
			show_page_subtitle(_T('authoredit_subtitle_connectionidentifiers'), 'author_edit');
?>
			</td>
		</tr>
		<tr><td align="left" valign="top"><?php echo f_err_star('username') . _T('authoredit_input_username'); ?></td>
			<td align="left" valign="top">

		<?php
			global $author_session;

			$class_auth = 'Auth_db';
			include_lcm('inc_auth_db');

			$auth = new $class_auth;

			if (! $auth->init()) {
				// [ML] If this happens, it's a bug. Right now, no auth methods
				// use it, so hell with translation
				echo "<p><b>ERROR: failed to initialize auth method: " . $auth->error . "</b></p>\n"; // TRAD-LATER
				lcm_log("ERROR: failed to initialize auth method: " . $auth->error);
			}

			// Some authentication methods might not allow the username to be 
			// changed. Also, it is generally better not to allow users to
			// change their username. Show the fields only if it is possible.
			echo '<input name="username_old" type="hidden" id="username_old" value="' . clean_output($user['username']) .'"/>';
			echo "\n";

			if ($auth->is_newusername_allowed($user['id_author'], $user['username'], $author_session)) {
				echo '<input name="username" type="text" class="search_form_txt" id="username" size="35" value="'
					. ($user['username'] == '0' ? '' : clean_output($user['username'])) .'"/>';
			} else {
				echo '<input type="hidden" name="username" value="' . clean_output($user['username']) . '"/>';
				echo $user['username'];
			}
		?>

			</td>
		</tr>

		<?php
			// Some authentication methods might not allow the password
			// to be changed. Show the fields only if it is possible.
			if ($auth->is_newpass_allowed($user['id_author'], $user['username'], $author_session)) {
				// Do not request 'current password' if new author or admin
				//FT dando permissão para o gestor
				if ($user['id_author'] && $author_session['status'] != 'admin' && $author_session['status'] != 'manager') {
					echo '
		<tr>
			<td align="left" valign="top">' . f_err_star('password_current') . _T('authorconf_input_password_current') . '</td>
			<td align="left" valign="top"><input name="usr_old_passwd" type="password" class="search_form_txt" id="usr_old_passwd" size="35" /></td>
		</tr>' . "\n";
				}
		?>
		
		<tr>
			<td align="left" valign="top"><?php echo f_err_star('password_confirm') . _T('authorconf_input_password_new'); ?></td>
			<td align="left" valign="top"><input name="usr_new_passwd" type="password" class="search_form_txt" id="usr_new_passwd" size="35" /></td>
		</tr>
		<tr>
			<td align="left" valign="top"><?php echo f_err_star('password_confirm') . _T('authorconf_input_password_confirm'); ?></td>
			<td align="left" valign="top"><input name="usr_retype_passwd" type="password" class="search_form_txt" id="usr_retype_passwd" size="35" /></td>
		</tr>

		<?php
			} /* is_newpass_allowed() */
		?>

		<tr><td align="left" valign="top"><?php echo _Ti('authoredit_input_status'); ?></td>
			<td align="left" valign="top">
			
<?php
			echo '<input type="hidden" name="status_old" value="' . $user['status'] . '"/>' . "\n";
			//FT dando pernissão para o gestor
			if ($author_session['status'] == 'admin' && $user['id_author'] != $author_session['id_author']) {
				echo '<select name="status" class="sel_frm" id="status">' . "\n";

				foreach ($statuses as $s) {
					echo "\t\t\t\t<option value=\"$s\""
						. (($s == $user['status']) ? ' selected="selected"' : '') . ">" . _T('authoredit_input_status_' . $s) . "</option>\n";
				}

				echo "</select>\n";
			} else {
				echo '<input type="hidden" name="status" value="' . $user['status'] . '"/>' . "\n";
				echo $user['status'];
			}
?>
			</td>
		</tr>
		<tr><td colspan="2" align="center" valign="middle">
			<input name="submit" type="submit" class="search_form_btn" id="submit" value="<?php echo _T('button_validate') ?>" /></td>
		</tr>
	</table>
</form>

<?php

lcm_page_end();

// Reset error messages
$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();
$_SESSION['usr'] = array(); // DEPRECATED 0.7.1

?>
