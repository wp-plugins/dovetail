<?php
	require_once 'classes/class.members.php';
	require_once 'classes/class.member.php';
	require_once 'gateways/paypal/class.fp-paypal.php';
	require_once 'classes/registration-actions.php';
	require_once 'classes/class.shortcodes.php';

	/**
	 *	Dovetail
	 *
	 *
	 *	Plugin Name: Dovetail
	 *	Plugin URI: https://rigorous-digital.co.uk/plugins/dovetail
	 *	Description: Dovetail adds basic yet beautiful membership tools to your WordPress website. Use and enjoy.
	 *	Author: Rigorous & Factory Pattern
	 *	Version: 1.2.8
	 *	Author URI: https://rigorous-digital.co.uk/author/admin
	 */
	if ( ! class_exists( "FP_Members" ) ) :

		class FP_Members {

			function __construct() {

				load_plugin_textdomain('fp-members', false, basename( dirname( __FILE__ ) ) . '/languages' );

				add_filter( 'admin_init', array( $this, 'fp_members_admin_init' ) );

				add_action('init', array( $this, 'dovetail_membership_level_custom_post' ) , 0);

				add_action('wp_loaded', array( $this, 'fp_members_roles' ) );

				// We don't want to exclude display if they're looking at items in the admin interface
		        if ( ! is_admin() ) {
					add_filter( 'wp_get_nav_menu_items', array( $this, 'fp_members_filter_menu_items' ), null, 3 );
				}

				// insert our own admin menu walker
		        add_filter( 'wp_edit_nav_menu_walker', array( $this, 'fp_members_edit_nav_menu_walker' ), 10, 2 );

				// save the menu item meta
		        add_action( 'wp_update_nav_menu_item', array( $this, 'fp_members_nav_update'), 10, 3 );

		        // add meta to menu item
		        add_filter( 'wp_setup_nav_menu_item', array( $this, 'fp_members_setup_nav_item' ) );

				ob_start();

				add_filter( 'the_content', array( $this, 'fp_members_filter_content' ) );

				add_action( 'admin_menu', array( $this, 'fp_members_register_admin_pages' ), 5 );

				/*		Registration & login action/filters	*/
				//add_action( 'register_form', 'fp_add_payment_button' );

				//add_filter( 'registration_redirect', 'fp_registration_pay' );

				add_action( 'plugins_loaded', 'dovetail_hide_adminbar' );

				// Add the links to the admin bar
				add_action( 'admin_bar_menu', array( &$this, 'dovetail_add_toolbar_menu' ), 999 );

				//add_action('get_header', array( $this, 'dovetail_display_errors' ), 100 );

				$shortcodes = new FP_Shortcodes();
			}

			function dovetail_hide_adminbar() {
				/*	Show admin bar only for admins and editors */
				if ( !current_user_can('administrator') ) {
					add_filter('show_admin_bar', '__return_false');
				}
			}

		    /**
		     * Include required admin files.
		     *
		     * @access public
		     * @return void
		     */
		    function fp_members_admin_init() {
		      	/* include the custom admin walker */
		      	include_once( plugin_dir_path( __FILE__ ) . 'classes/class.admin-edit-menu-walker.php');
				/* Include the page functions	*/
				include_once( plugin_dir_path( __FILE__ ) . 'classes/class.page-functions.php');
				/* Include the role functions	*/
				include_once( plugin_dir_path( __FILE__ ) . 'classes/class.role-editor.php');

				// Ban non-admins from viewing the admin area
				if ( ( !current_user_can( 'administrator' ) ) && ( defined( 'DOING_AJAX' ) == false ) ) {
					wp_redirect( home_url() );
					exit;
				}
		    }

			/**
		     * Override the Admin Menu Walker
		     * @since 1.0
		     */
		    function fp_members_edit_nav_menu_walker( $walker, $menu_id ) {
		        return 'Walker_Nav_Menu_Edit_Roles';
		    }

			function fp_members_register_admin_pages() {
				$menu_slug = 'fp-members-dashboard';

				add_menu_page(
					'Dovetail',
					'Dovetail',
					'manage_options',
					$menu_slug,
					array( $this, 'fp_members_dashboard_view' ),
					plugins_url( 'dovetail/assets/images/logo/DOVE-icon.png' ),
					100
				);
				add_submenu_page(
					$menu_slug,
					'Page Settings',
					'Settings',
					'manage_options',
					'dovetail-page-settings',
					array( $this, 'fp_members_options_view' )
				);
				add_submenu_page(
					$menu_slug,
					'Page Settings',
					'Access',
					'manage_options',
					'dovetail-page-access',
					array( $this, 'fp_dovetail_access' )
				);

			}

			function fp_members_dashboard_view() {
				if ( !current_user_can( 'manage_options' ) )  {
					wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
				}

				settings_fields('fp-members-dashboard');
				do_settings_sections('plugin');

				include dirname(__FILE__)."/views/dashboard.php";
			}

			function fp_dovetail_access() {
				wp_enqueue_style( 'colors' );
				echo '<div class="nav-menus-php">';
				include dirname(__FILE__)."/../../../wp-admin/nav-menus.php";
				echo '</div>';
			}

			/** Draws up the menu options page */
			function fp_members_options_view() {
				if ( !current_user_can( 'manage_options' ) )  {
					wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
				}

				// See if the user has posted us some information
			    // If they did, this hidden field will be set to 'Y'
			    if( isset($_POST[ "data_submitted" ]) && $_POST[ "data_submitted" ] == 'Y' ) {

			        // Read their posted value
					$restricted_page_id = stripslashes($_POST[ "restricted_page_id" ]);
					$restricted_message = stripslashes($_POST[ "restricted_message" ]);

					if ( isset( $restricted_page_id ) )
			        	update_option( "fp_members_restricted_page_id", $restricted_page_id ); // Save the posted value in the database

					if ( isset( $restricted_message ) )
			        	update_option( "dovetail_restricted_message", $restricted_message ); // Save the posted value in the database

			        // Put an settings updated message on the screen
				?>
				<div class="updated"><p><strong><?php _e('Settings saved', 'fp_members_settings_saved' ); ?></strong></p></div>
				<?php

				}

				$restricted_page_id = get_option("fp_members_restricted_page_id");
				$restricted_message = get_option("dovetail_restricted_message");

				include dirname(__FILE__)."/views/pages.php";
			}

			function fp_members_filter_menu_items( $items, $menu, $args ) {
				//print_r( $items );
				// Iterate over the items to search and destroy
				foreach ( $items as $key => $item ) {

					if( isset( $item->roles ) ) {

						switch( $item->roles ) {
							case 'in' :
								$visible = is_user_logged_in() ? true : false;
							break;
							case 'out' :
								$visible = ! is_user_logged_in() ? true : false;
							break;
							case 'anyone' :
								$visible = true;
							break;
							default:
								$visible = false;
								if ( is_array( $item->roles ) && ! empty( $item->roles ) ) foreach ( $item->roles as $role ) {
									if ( current_user_can( $role ) ) $visible = true;
								}
								break;
						}
				  		if ( ! $visible ) unset( $items[$key] ) ;
					}

				}
				return $items;
			}

			/**
		     * Save the roles as menu item meta
		     * @return string
		     * @since 1.0
		     */
		    function fp_members_nav_update( $menu_id, $menu_item_db_id, $args ) {
		        global $wp_roles;

		        $allowed_roles = apply_filters( 'nav_menu_roles', $wp_roles->role_names );

		        // verify this came from our screen and with proper authorization.
		        if ( ! isset( $_POST['nav-menu-role-nonce'] ) || ! wp_verify_nonce( $_POST['nav-menu-role-nonce'], 'nav-menu-nonce-name' ) )
		            return;

		        $saved_data = false;

		        if ( isset( $_POST['nav-menu-logged-in-out'][$menu_item_db_id]  )  && in_array( $_POST['nav-menu-logged-in-out'][$menu_item_db_id], array( 'in', 'out' ) ) ) {
		              $saved_data = $_POST['nav-menu-logged-in-out'][$menu_item_db_id];
		        } elseif ( isset( $_POST['nav-menu-role'][$menu_item_db_id] ) ) {
		            $custom_roles = array();
		            // only save allowed roles
		            foreach( $_POST['nav-menu-role'][$menu_item_db_id] as $role ) {
		                if ( array_key_exists ( $role, $allowed_roles ) ) $custom_roles[] = $role;
		            }
		            if ( ! empty ( $custom_roles ) ) $saved_data = $custom_roles;
		        } elseif ( isset( $_POST['nav-menu-logged-in-out'][$menu_item_db_id] ) && in_array( $_POST['nav-menu-logged-in-out'][$menu_item_db_id], array( 'anyone' )  ) ) {

					$saved_data = $_POST['nav-menu-logged-in-out'][$menu_item_db_id];

				} else {

					$saved_data = $_POST['nav-menu-logged-in-out'][$menu_item_db_id];

				}

		        if ( $saved_data ) {
		            update_post_meta( $menu_item_db_id, '_nav_menu_role', $saved_data );
		        } else {
		            delete_post_meta( $menu_item_db_id, '_nav_menu_role' );
		        }
		    }

		    /**
		     * Adds value of new field to $item object
		     * is be passed to Walker_Nav_Menu_Edit_Custom
		     * @since 1.0
		     */
		    function fp_members_setup_nav_item( $menu_item ) {

		        $roles = get_post_meta( $menu_item->ID, '_nav_menu_role', true );

		        if ( ! empty( $roles ) ) {
		            $menu_item->roles = $roles;
		        }
		        return $menu_item;
		    }

			function fp_members_roles() {

				// create a new role for Members
				add_role('member', 'Member', array(
					'read' 			=> true
				));

			}

			function fp_members_filter_content( $content ) {
				global $post;

				$allowed_roles = get_post_meta( $post->ID, "available_roles", true );


				// This allows theme developers to skip authorisation checks in certain page templates if they want to
				$skip_check = apply_filters( "dovetail_skip_content_check", false, $post );

				if ( $skip_check ) {
					return $content;
				}

				// No restriction; allow all to view
				if ( empty( $allowed_roles ) )
					return $content;

				$visible = false;

				foreach ( $allowed_roles as $index => $role ) {
					if ( current_user_can( $role ) ) $visible = true;
				}

				if ( $visible ) :
					return $content;
				else :
					$restricted_page_id = get_option("fp_members_restricted_page_id");
					$restricted_message = urlencode( get_option("dovetail_restricted_message") );

					if ( isset( $restricted_page_id  ) && !empty( $restricted_page_id ) ) :
						$protected_page = get_page( $restricted_page_id );
					else :
						$protected_page = get_page( get_option('page_on_front') );
					endif;

					if ( isset( $protected_page ) ) :
						// Redirect to the protected page chosen
						wp_redirect( get_permalink( $protected_page->ID )."?dovetail-msg=".$restricted_message );
					else :
						wp_redirect( home_url() );
					endif;

					// Return the content from the protected page if the redirect fails
					return $protected_page->post_content;
				endif;
			}

			function fp_members_output_members_log() {
				$model = new Members();
				$members = $model->get_all_members();

				foreach ($members as $member) {
					//var_dump($member);
					echo "[".strftime( '%r %e %h %Y' ,strtotime( $member->user_registered ) )."] ";
					echo "<strong>".$member->display_name."</strong> joined.";
				}
			}

			function fp_members_output_members_count() {
				$model = new Members();
				$members = $model->get_role_count();

				return $members;
			}

			function dovetail_membership_level_custom_post() {

				// set the singular and plural label
				$name = array(
					'singular'	=> 'Member Level',
					'plural'	=> 'Member Levels'
				);

				$args = array(
					'labels' => array(
						// set the various label values
						'name'			=> $name['plural'],
						'singular_name'		=> $name['singular'],
						'add_new'		=> 'Add ' . $name['singular'], 'report',
						'add_new_item'		=> 'Add New ' . $name['singular'],
						'edit_item'		=> 'Edit ' . $name['singular'],
						'new_item'		=> 'New ' . $name['singular'],
						'view_item'		=> 'View ' . $name['singular'],
						'search_items'		=> 'Search ' . $name['plural'],
						// the next two values should be lowercase
						'not_found'		=> 'No ' . strtolower($name['plural']) . ' found',
						'not_found_in_trash'	=> 'No ' . strtolower($name['plural']) . ' found in Trash',
						'parent_item_colon'	=> ''
					),
					'supports' 	=> array('revisions', 'title', 'editor', 'excerpt', 'thumbnail'),
					'public'		=> TRUE,
					'publicly_queryable'	=> TRUE,
					'show_ui'		=> TRUE,
					'show_in_menu'	=> 'fp-members-dashboard',

				);

				// register the post type along with it's arguments
				register_post_type('member-level', $args);

			}// end of dovetail_membership_level_custom_post()

			function dovetail_display_errors() {

				if ( key_exists( "dovetail-msg", $_GET ) )
					echo $_GET["dovetail-msg"];

			}// end of dovetail_display_errors()

			/**
			 * 	Adds the Dovetail menu to the Admin Bar
			 */
			function dovetail_add_toolbar_menu( $wp_admin_bar ) {

				//$current_user = get_currentuserinfo();

				//if ( in_array( 'Administrator', $current_user->roles ) ) :

					// Add the parent node
					$args = array(
						'id'    => 'dovetail',
						'title' => '<img src="'.site_url().'/wp-content/plugins/dovetail/assets/images/logo/DOVE-icon.png">&nbsp;Dovetail',
						'href'  => site_url().'/wp-admin/admin.php?page=fp-members-dashboard',
						'meta'  => array( 'class' => 'dovetail-menu-item' )
					);
					$wp_admin_bar->add_node( $args );

					// Add the link to the Users page
					$args = array(
						'id'    => 'dovetail-users',
						'title' => 'Users',
						'href'  => site_url().'/wp-admin/users.php',
						'meta'  => array( 'class' => 'dovetail-menu-item dovetail-users-menu-item' ),
						'parent'=> 'dovetail'
					);
					$wp_admin_bar->add_node( $args );

				//endif; // End administrator check

			}

		}

	endif; // End class_exists test

	global $fp_members;
	$fp_members = new FP_Members();
?>
