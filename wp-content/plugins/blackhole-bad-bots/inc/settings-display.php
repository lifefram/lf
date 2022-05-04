<?php // Blackhole for Bad Bots - Display Settings

if (!defined('ABSPATH')) exit;

function blackhole_menu_pages() {
	
	// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	add_menu_page(esc_html__('Blackhole for Bad Bots', 'blackhole-bad-bots'), esc_html__('Blackhole', 'blackhole-bad-bots'), 'manage_options', 'blackhole_settings', 'blackhole_display_settings'); // avoid duplicate menu item: menu function = submenu function
	
	// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	add_submenu_page('blackhole_settings', esc_html__('Settings', 'blackhole-bad-bots'), esc_html__('Settings', 'blackhole-bad-bots'), 'manage_options', 'blackhole_settings', 'blackhole_display_settings'); // avoid duplicate menu item: parent slug = menu slug
	add_submenu_page('blackhole_settings', esc_html__('Bad Bots', 'blackhole-bad-bots'), esc_html__('Bad Bots', 'blackhole-bad-bots'), 'manage_options', 'blackhole_badbots',  'blackhole_display_badbots');
	
}

function blackhole_display_settings() { ?>
	
	<div class="wrap">
		<h1 class="bbb-title"><?php echo BBB_NAME; ?> <span><?php echo BBB_VERSION; ?></span></h1>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			
			<?php 
				settings_fields('bbb_options');
				do_settings_sections('bbb_options');
				submit_button(); 
			?>
			
		</form>
	</div>
	
<?php }

function blackhole_display_badbots() { ?>
	
	<div class="wrap">
		<h1 class="bbb-title"><?php echo BBB_NAME; ?> <span><?php echo BBB_VERSION; ?></span></h1>
		<form method="post" action="options.php">
			
			<?php 				
				settings_fields('bbb_badbots');
				do_settings_sections('bbb_badbots');
				// submit_button(); 
			?>
			
		</form>
	</div>
	
<?php }
