<?php
/*
Plugin Name: Popup in Posts, Pages
Description: Create popups with Tinymce, within posts and pages.. 
Version: 1.2
Author: TazoTodua
Author URI: http://www.protectpages.com/profile
Plugin URI: http://www.protectpages.com/
Donate link: http://paypal.me/tazotodua
*/
$include_script=true; require_once(__DIR__.'/popup_script.php');

define('version__PIPP', 1.2);
define('networkwide__PIPP', true);
define('pluginpage__PIPP', 				'pipp_opts_page');
define('pluginadmin__PIPP',  			(is_multisite()  ? 'settings.php' : 'options-general.php' )  ) ;
define('pluginsaveopts__PIPP',  		(is_multisite()  ? 'settings.php' : 'options.php' )  ) ;
define('plugin_settings_page__PIPP', 	(is_multisite()  && networkwide__PIPP ? network_admin_url('settings.php') : admin_url( 'options-general.php') ). '?page='.pluginpage__PIPP  );
define('pipp_dtrans', "pipp_trn");
									

// ================================================== General variables ===============================================
define('HOME_URL__PIPP', 	(home_url('/','relative'))	); 
define('PLUGIN_URL__PIPP',	plugin_dir_url(__FILE__)	);
function get_opts__PIPP()	{  return $GLOBALS['pipp_OPTS']=get_site_option('pipp_opts', array()); }
function get_fields__PIPP()	{  return $GLOBALS['pipp_FIELDS']=get_site_option('pipp_fields', array()); }
function validate_pageload__PIPP($value, $action_name){ if ( !wp_verify_nonce($value, $action_name) ) { die( "go back&refresh page.  (". __FILE__ );	}  	}	




// ==========================================================================================================================

register_activation_hook( __FILE__, 'First_Time_Install__PIPP' );
function First_Time_Install__PIPP(){	}

add_action('plugins_loaded', 'refresh_options__PIPP',1);
function refresh_options__PIPP(){
	$opts = $old_opts = get_opts__PIPP(); 
	$array =  array( 'plugin_active'=>1 );
	foreach($array as $name=>$value){ if(!array_key_exists($name,$opts)){ $opts[$name]=$array[$name]; } }
	$opts['vers']= version__PIPP; 
	if($old_opts != $opts) { update_site_option('pipp_opts', $opts );  }
	return $opts;
}
// ==================================================  #### PLUGIN ACTIVATION  HOOK ==============================================
 


	
// =================================================     ADD PAGE IN SETTINGS menu ================================================= 
add_action( (is_multisite() && networkwide__PIPP ? 'network_admin_menu' : 'admin_menu')  ,  function() {
	add_submenu_page(pluginadmin__PIPP, 'Popup in posts', 'Popup in posts', 'edit_others_posts', pluginpage__PIPP, 'pipp__PIPP' );
} );
function pipp__PIPP(){		global $wpdb;
	if(!networkwide__PIPP && isset($_GET['isactivation']) && stripos($_SERVER['HTTP_REFERRER'], 'isactivation') ===false ) { echo '<script>alert("If you are using multi-site, you should set these options per sub-site one-by-one");</script>'; }
	
	if(!empty($_POST['pipp_opts'])){
		check_admin_referer('nonce_pipp');
		$opts1= get_opts__PIPP();
		$opts1['plugin_active'] = !empty($_POST['pipp_opts']['plugin_active']) ? 1 : 0; 
		update_site_option('pipp_opts',  $opts1); 
	}
	$opts1= get_opts__PIPP();
	$opts2= get_fields__PIPP();
?>
<style>
.eachFieldX{ width:100%;}
.form-table th { width: 50%; }
</style>
<div class="clear"></div>
<div id="welcome-panel" class="welcome-panel">
	<div class="welcome-panel-content">
	<h3><?php echo __('Plugin Settings Page!', pipp_dtrans);?></h3>
	<p class="about-description"><?php echo __('You can check other useful plugins at: <a href="http://j.mp/musthavewordpressplugins">Must have free plugins for everyone</a>', pipp_dtrans);?> </p>
	<div class="welcome-panel-column-container">
		<div class="welcome-panel-column" style="width:80%;">
			<h4>_</h4>
			<form method="post" action="">
			<?php 
			//$opts1	= get_opts__PIPP();
			$fields	= array_filter(get_fields__PIPP());
			?>
		    <table class="form-table">
		        <tr valign="top">
		        <th scope="row"><?php echo __('Plugin is active', pipp_dtrans);?> </th>
		        <td>yes<input type="radio" name="pipp_opts[plugin_active]" value="1" <?php checked($opts1['plugin_active'], 1) ; ?> />  no<input type="radio" name="pipp_opts[plugin_active]" value="0" <?php checked($opts1['plugin_active'], 0) ; ?> /></td>
		        </tr>
		       
		    </table>
		    <?php 
			wp_nonce_field( 'nonce_pipp' );
			submit_button(  __('Save Settings', pipp_dtrans), 'primary', 'xyz-save-settings', true,  $attrib= array( 'id' => 'xyz-submit-button' )   );
		    ?>
			</form>
		</div>
		<div class="welcome-panel-column welcome-panel-last" style="width:15%;">
			<h4>More Actions</h4>
			<ul>
				<li><div class="welcome-icon welcome-widgets-menus">Found this plugin Useful ? <BR/><a href="http://paypal.me/tazotodua">You can donate</a></div></li>
			</ul>
		</div>
	
	</div>
	</div>
</div>
<?php 
} // END PLUGIN PAGE


add_action( 'wp_enqueue_scripts','load_script__PIPP',44);
function load_script__PIPP(){
	wp_register_script	( 'popupi_script', PLUGIN_URL__PIPP.'/popup_script.php', array(), false, false );
	wp_enqueue_script	( 'popupi_script' );
}







add_shortcode('popupi',  'add_script_shortcode__PIPP' );
function add_script_shortcode__PIPP($atts, $content = '' ){
	if(empty($content)) return "";
	$post	= $GLOBALS['post'];
	foreach ($atts as $key=>$value){
		if(is_numeric($value)){
			$popup_id=$value;	 break;
		}
		elseif($key=="id"){
			$popup_id=$value;	 break;
		}
	}
	if(isset($popup_id)){
		$GLOBALS['popupi_contents'][$post->ID][] =$popup_id;
		//dont place DIV's here, because surrounding P will remove all DIV's
		return '<span class="a_pupupi"><a href="javascript:show_my_popup(\'.hpi_'.$popup_id.'\'); void(0);">'. $content .'</a></span>';
	}
	return '';
}

add_filter('the_content', 'add_content__PIPP', 21);
function add_content__PIPP($content){
	global $post;
	if(!empty($GLOBALS['popupi_contents'][$post->ID]) && is_array($GLOBALS['popupi_contents'][$post->ID])){
		$p_array=	get_post_meta($post->ID, 'popupi'); 
		foreach ($GLOBALS['popupi_contents'][$post->ID] as $key=>$pop_id){
			$content .='<div class="'.popupclassname_PIPP.' hpi_'.$pop_id.'">'.$p_array[$pop_id].'</div>';
		}
	}
	return $content;
}

	



add_action('add_meta_boxes', function (){ add_meta_box('html_id_19_pipp', 'Popups contents','metabox__PIPP', null,'normal');} );
function metabox__PIPP($post){  
	$post_id = !empty($post) ? $post->ID : ( !empty($GLOBALS['post']->ID) ? $GLOBALS['post']->ID : (!empty($GLOBALS['post']['ID']) ? $GLOBALS['post']['ID'] : false) );
	$popup_contents	= get_post_meta($post_id , 'popupi');
	?>
	<style>
	.OnePopupBlock{ width:48%; height:200px; font-size:0.9em; float:left; margin:1% 1%; }
	.OnePopupBlock > div{width:100%; height:100%;}
	.OnePopupBlock .tx{width:100%; height:100%;}
	.OnePopupBlock textarea{width:100%; height:100%;}
	.form-table th { width: 50%; }
	</style>
	<div id="fields_block_rh">
		<h1 style="text-align:center;">Insert Popups in content</h1>
		<div id="fields_holder_rh">
		<?php 
		$counter=0;
		function each_field_out($value, $name= false){ return 
			'<div class="OnePopupBlock">
				<div class="popup_block"  id="popupblock_'.$name.'">
					<div class="">Insert the shortcode anywhere in content:  <code style="color:red; font-weight:bold;">[popupi '.$name.']example anchor-text[/popupi]</code></div>
					<div class="tx"><textarea class="eachFieldX" name="pipp_fields['. $name .']" placeholder="<div>popup content ...</div>">'.htmlentities($value).'</textarea></div>
				</div>
			</div>';
		}
		echo '<div style="display:none;">'.each_field_out('', 'THE_IDENTIFIER_OF_POPUP').'</div>';
		
		if(!empty($popup_contents)){
			foreach ($popup_contents as $name=>$value){
				if($name==0) continue;
				$counter++;
				echo each_field_out($value, $name);
			}
		}
		?>
		</div>
		<div style="clear:both;"></div>
		<button type="button" class="button-small" onclick="add_new_filed();" ><?php echo __('add another popup', pipp_dtrans);?></button>
	</div>
	<script>
	counter_pipp= <?php echo $counter;?>;
	</script>
	<?php 
}
// Save Action 
add_action( 'save_post', function ($post_id) {
	if (!empty($_POST['pipp_fields'])){
		delete_post_meta($post_id, 'popupi');
		foreach ($_POST['pipp_fields'] as $key=>$value) { 
			$value = wp_kses($value,wp_kses_allowed_html('post') );  //  shapeSpace_allowed_html__PIPP()
			add_post_meta($post_id, 'popupi', $value);
		}
	}
});	

function shapeSpace_allowed_html__PIPP() { $allowed_tags = array( 'a' => array( 'class' => array(), 'href' => array(), 'rel' => array(), 'title' => array(), ), 'abbr' => array( 'title' => array(), ), 'b' => array(), 'blockquote' => array( 'cite' => array(), ), 'cite' => array( 'title' => array(), ), 'code' => array(), 'del' => array( 'datetime' => array(), 'title' => array(), ), 'dd' => array(), 'div' => array( 'class' => array(), 'title' => array(), 'style' => array(), ), 'dl' => array(), 'dt' => array(), 'em' => array(), 'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(), 'i' => array(), 'img' => array( 'alt' => array(), 'class' => array(), 'height' => array(), 'src' => array(), 'width' => array(), ), 'li' => array( 'class' => array(), ), 'ol' => array( 'class' => array(), ), 'p' => array( 'class' => array(), ), 'q' => array( 'cite' => array(), 'title' => array(), ), 'span' => array( 'class' => array(), 'title' => array(), 'style' => array(), ), 'strike' => array(), 'strong' => array(), 'ul' => array( 'class' => array(), ), ); return $allowed_tags; }





// ========================== ADD BUTTON =============================== //

	//Add Tinymce Several Buttons
	add_action('admin_init', function () { 
		if ( get_user_option('rich_editing') == 'true') {
			add_filter('mce_external_plugins', function($plugin_array) {
				return  array_merge($plugin_array,   array('MyButtonss1_pipp'=> plugin_dir_url(__FILE__).'/popup_script.php?tinymce_editor')  ); 
			});		
			add_filter('mce_buttons_2',  function($buttons){
				return  array_merge($buttons,   array('Popup_Insert')  );  
			});
		}
	});
	//this is must for REFRESHING!
	add_filter( 'tiny_mce_version',  function ($ver) {  $ver += 3;  return $ver;} );

// ========================== ADD BUTTON =============================== //


		
								
								//===========  links in Plugins list ==========//
								add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), function ( $links ) {   $links[] = '<a href="'.plugin_settings_page__PIPP.'">'.__('Settings', pipp_dtrans).'</a>'; $links[] = '<a href="http://paypal.me/tazotodua">'.__('Donate', pipp_dtrans).'</a>';  return $links; } );
								//REDIRECT SETTINGS PAGE (after activation)
								add_action( 'activated_plugin', function($plugin ) { if( $plugin == plugin_basename( __FILE__ ) ) { if(($bulk_activation = ((new WP_Plugins_List_Table())->current_action() == 'activate-selected'))) return; else  exit( wp_redirect( plugin_settings_page__PIPP.'&isactivation'  ) ); } } );
?>