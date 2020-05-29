<?php

include('inc/inc_version.php');

// Test if LCM is installed
if (! include_config_exists('inc_connect')) {
	header('Location: install.php');
	exit;
}

include_lcm('inc_presentation');
include_lcm('inc_login');

global $lcm_lang_right;

lcm_html_start(_T('login_title_login'), 'login');

echo get_optional_html_login();

// Site name: mandatory
$site_name = _T(read_meta('site_name'));
if (! $site_name)
	$site_name = _T('title_software');

// Site description: may be empty
$site_desc = _T(read_meta('site_description'));

?>
<table id="login_table" align='center'>
	<tr>
		<td>
			<h3><?php echo $site_name; ?>
			<?php 
				if ($site_desc)
				?>
				<br /><span style='font-size: 80%; font-weight: normal;'><?php $site_desc; ?></span>

			</h3>
		</td>
	</tr>
	<tr>
		<td>
			<table align="center" id='login_screen'>
				<tr>
					<td>
						<?php 
							show_login('');
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

</body>
</html>
<?php 
lcm_html_end();

?>
