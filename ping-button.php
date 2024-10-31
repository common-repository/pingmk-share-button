<?php
/*
Plugin Name: Ping.mk копче за споделување
Plugin URI: http://wp.mk/
Description: Додава копче за споделување на написите на македонскиот агрегатор на содржина - Ping.mk
Version: 2.0
Author: Борис Кузманов
Author URI: http://wp.mk
*/

/** СТАНДАРДНИ ПОДЕСУВАЊА */
function _ping_get_default_config() {
	return array(
	'ping_post'		=> true,	/* прикажи на написи */
	'ping_str'		=> true,		/* прикажи на страници */

	'ping_stil'	=> '3',		/* изглед на копчето */
	'ping_goredole'		=> "top",		/* прикажи на врвот од написот */
	'ping_levodesno'		=> "right",		/* прикажи на десната страницата */
	);
}

add_option('ping_button_options', _ping_get_default_config(), 'Ping.mk Подесувања');
$ping_config = get_option('ping_button_options');

// КРЕИРАЊЕ НА СТРАНИЦА ЗА ПОДЕСУВАЊА (АДМИНИСТРАЦИЈА)
add_action('admin_menu', 'add_ping_option_page');

function add_ping_option_page() {
	add_options_page('Ping.mk копче за споделување - Подесувања', 'Ping.mk копче', 8, basename(__FILE__), 'ping_options_page');
}

function ping_options_page() {
	global $ping_config;
	if(!empty($_POST['_ping_update'])) { // ажурирање на подесувањата
		$ping_config['ping_post'] = $_POST['ping_post'];
		$ping_config['ping_str'] = $_POST['ping_str'];

		$ping_config['pingmk_stil'] = $_POST['pingmk_stil'];
		$ping_config['pingmk_goredole'] = $_POST['pingmk_goredole'];
		$ping_config['pingmk_levodesno'] = $_POST['pingmk_levodesno'];

		update_option('ping_button_options', $ping_config);

		echo '<div id="message" class="updated fade"><p><strong>Подесувањата се зачувани.</strong></p></div>';
	} else if(!empty($_POST['_ping_restore'])) { // врати ги станардните подесувања
		$ping_config = _ping_get_default_config();
		update_option('ping_button_options', $ping_config);

		echo '<div id="message" class="updated fade"><p><strong>Вратени се стандардните подесувања.</strong></p></div>';
	} else if(!empty($_GET['_ping_updatepost']) && !empty($_GET['_ping_action'])) {
		$_postid = $_GET['_ping_updatepost'];
		$_action = $_GET['_ping_action'];
		$_post = get_post($_postid);

	} 

	?>
	
<div class="wrap">
<h2>Ping.mk копче - Подесувања</h2>

<form method="post" action="">
	<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr>
	<th width="33%" scope="row" valign="top"> Видови на копчиња:</th>
	<td>
<label><input name="pingmk_stil" type="radio" value="1" <?php checked('1', $ping_config['pingmk_stil']); ?> /> Копче 1: <br />
<div style="position: relative; left: 20px;">
	<script>ping_url='http://r.ping.mk/buttons'</script>
	<script language="javascript" src="http://r.ping.mk/button.js?t=1"></script></label><br /><br />
</div>
<label><input name="pingmk_stil" type="radio" value="2" <?php checked('2', $ping_config['pingmk_stil']); ?> /> Копче 2: <br />
<div style="position: relative; left: 20px;">
	<script>ping_url='http://r.ping.mk/buttons'</script>
	<script language="javascript" src="http://r.ping.mk/button.js?t=2"></script></label><br /><br />
</div>
<label><input name="pingmk_stil" type="radio" value="3" <?php checked('3', $ping_config['pingmk_stil']); ?> /> Копче 3: <br />
<div style="position: relative; left: 20px;">
	<script>ping_url='http://r.ping.mk/buttons'</script>
	<script language="javascript" src="http://r.ping.mk/button.js?t=3"></script></label>
</div>
	</td>
	</tr>
	<tr>
<th width="33%" scope="row" valign="top"> Прикажи го копчето на:</th>
<td>
<?php
	$_binary_display_options = array(
		'ping_post' => 'Написи',
		'ping_str' => 'Страници',
	);
foreach($_binary_display_options as $_optid => $_descr) {
?>
<label for="<?php echo $_optid; ?>">
<input name="<?php echo $_optid; ?>" id="<?php echo $_optid; ?>" type="checkbox" <?php checked('1', $ping_config[$_optid]); ?> value="1" /> <?php echo $_descr; ?></label><br />
<?php 	} ?>
</td>
</tr>
<tr>
<th scope="row" valign="top"> Позиција на копчето:</th>
<td>
<label><input name="pingmk_goredole" type="radio" value="top" <?php checked('top', $ping_config['pingmk_goredole']); ?> /> На врвот од написот</label><br />
<label><input name="pingmk_goredole" type="radio" value="bottom" <?php checked('bottom', $ping_config['pingmk_goredole']); ?> /> На дното од написот</label><br /><br />
	
<label><input name="pingmk_levodesno" type="radio" value="left" <?php checked('left', $ping_config['pingmk_levodesno']); ?> /> На левата страна</label><br />
<label><input name="pingmk_levodesno" type="radio" value="right" <?php checked('right', $ping_config['pingmk_levodesno']); ?> /> На десната страна</label><br />
</td>
</tr>
</table>

<p class="submit">
	<input style="float:right" type='submit' name='_ping_update' id='_ping_update' value='Ажурирај &raquo;' />
	<input type='submit' name='_ping_restore' id='_ping_restore' value='Врати ги стандардните подесувања' />
</p>
</form>
</div>

<?php
}

add_filter('the_content', 'add_ping_button');
add_filter('the_excerpt', 'add_ping_button');

function add_ping_button($content='') {
	global $ping_config;
	if((is_single() && $ping_config['ping_post']) ||
	   (is_page() && $ping_config['ping_str']) ||
	   0) {
	   	if($ping_config['pingmk_goredole'] == 'top')
			$content = ping_button_code().$content;
		else
			$content .= ping_button_code();
	}

	return $content;
}

function ping_button_code() {
	global $post, $ping_config;
	$ret = '';
	$_poststate = get_post_meta($post->ID, '_ping_pingmk', true);

		$_style = "float: ".$ping_config['pingmk_levodesno']."; ";
		if($ping_config['pingmk_stil'] == '1') {
			$_style .= "width: 140px; height: 21px;";
		} else if($ping_config['pingmk_stil'] == '2') {
			$_style .= "width: 57px; height: 85px;";
		} else if($ping_config['pingmk_stil'] == '3') {
			$_style .= "width: 66px; height: 66px;";
		}
		$_style .= " overflow: hidden; position: relative; left: 8px;";
		$ret .= "<div style=\"".$_style."\">";
		$ret .= "<script>//<![CDATA[\nping_url=\"".get_permalink()."\";\n//]]>\n</script>";
		$ret .= '<script language="javascript" src="http://r.ping.mk/button.js?t='.$ping_config[pingmk_stil].'"></script>';
		$ret .= "</div>";
	
	return $ret;
}

function _ping_get_update_uri($postID = 0, $action = '') {
	return get_bloginfo('url').
		"/wp-admin/options-general.php?page=ping-button.php".
		"&_ping_updatepost=$postID".
		"&_ping_action=$action";
}


?>