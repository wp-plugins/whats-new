<?php
/*
Plugin Name: Whats New 
Description: Shows post,pages,products added since user last visit
Plugin URI: http://profiles.wordpress.org/skomfare2/
Version: 1.0
Author: Skomfare2
Author URI: http://profiles.wordpress.org/skomfare2/
*/

class albdesign_whats_new {
    
	
	
	public function __construct(){

		//define some constants
		define( 'ALBDESIGN_WHATSNEW_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		define( 'ALBDESIGN_WHATSNEW_PLUGIN_DIR', plugin_dir_path(__FILE__) );

		//add the plugin options page on the admin menu
		add_action( 'admin_menu', array($this,'register_albdesign_menu') );
		

		// associating a function to login hook
		add_action('wp_login', array($this,'albdesign_set_last_login'));
		
		//enqueue CSS file 
		add_action('wp_footer',array($this,'enqueue_scripts_and_styles'));

    }
	

	
	public function register_albdesign_menu(){
		
		add_menu_page( 'Whats New', 'Whats New','manage_options', 'albdesign_whatsnew_post',array( $this, 'show_options_page' ) );
		
	}

	

	public function show_options_page(){
	
		require_once(plugin_dir_path(__FILE__). 'settings_page_form.php');

	}

	
	
	/*
	*	Enqueue CSS 
	*/
	function enqueue_scripts_and_styles(){
	
		wp_enqueue_style( 'albdesign_whats_new_css',ALBDESIGN_WHATSNEW_PLUGIN_URL.'/style.css' );
	
	}
	
	
	/*
	*	set last login date for user
	*/
	function albdesign_set_last_login($login) {
	
	   $user = get_user_by('login',$login);
	   
	   if($user->ID==0){
	   
			return ;
			
	   }
	 
	   //add or update the last login value for logged in user
	   update_user_meta( $user->ID, 'albdesign_last_login', current_time('mysql') );
	   
	}	
	

	
	/*
	* get last login
	*/
	static function albdesign_get_last_login(){
	
		$current_user = wp_get_current_user();
		
		if( $current_user->ID == 0){
		
			return ;
			
		}
		
		$user_last_login = get_user_meta( $current_user->ID, 'albdesign_last_login', current_time('mysql') );

		return $user_last_login;
		
	}


	
	/*
	*	Filter posts published after a certain date
	*/
	static function filter_posts_published_after_date($where='') {
	
		$date = albdesign_whats_new::albdesign_get_last_login();
		
		$where .= " AND post_date >= '".$date."'";
		
		return $where;
		
	}

	
	
	
	/*
	*	Get posts,pages,products
	*/
	static function albdesign_get_posts_pages_products() {
	
		//check if a loggedin user or guest
		$current_user = wp_get_current_user();
		
		if( $current_user->ID == 0){
			return ;
		}	
	
		global $post;
				
		//initialize arrays;
		$posts_array=array();		
		$pages_array=array();		
		$products_array=array();

		$response=array();
		
		$total_new_posts_found = 0; 

	
		//get saved options
		$albdesign_newpost_saved_options = get_option('albdesign_whatsnew_options');		

		//check if "show new posts" is enabled 
		if(isset($albdesign_newpost_saved_options) && isset($albdesign_newpost_saved_options['enabled_for_posts']) && $albdesign_newpost_saved_options['enabled_for_posts']=='yes'){
				
				$args_type_post = array(
					
					'post_type' => 'post',
					'post_status' => 'publish',
					'showposts' => -1,
					'perm' => 'readable'
					
				);

				//Filter by date
				add_filter( 'posts_where', array('albdesign_whats_new','filter_posts_published_after_date'));
				
				$query2_posts = new WP_Query( $args_type_post );
				
				remove_filter( 'posts_where', array('albdesign_whats_new','filter_posts_published_after_date'));
				
				
				// Create an array with posts infos
				if ( $query2_posts->have_posts() ) {
				
					while ( $query2_posts->have_posts() ) {
					
						$query2_posts->the_post();
						
						$posts_array[get_the_id()]['id'] = get_the_id();
						
						$posts_array[get_the_id()]['title'] = get_the_title();
						
						$posts_array[get_the_id()]['permalink'] = get_the_permalink();
						
						$posts_array[get_the_id()]['author'] = get_the_author();

						
						//What image to show 
						if($albdesign_newpost_saved_options['show_images']=='yes'){
							
							//POST FEATURED IMAGE
							$page_featured = wp_get_attachment_image( get_post_thumbnail_id($query2_posts->post->ID),'thumbnail');
							
							if($page_featured){
							
								$posts_array[get_the_id()]['featured_image'] = $page_featured;
								
							}
								
						}
										
					} //end while
					
				} //end if have_posts
				
				wp_reset_query();
				
				
				$response['posts']= $posts_array;
				
				//increase total posts count
				$total_new_posts_found = $total_new_posts_found +  count($posts_array);

		} // end IF enabled for posts	
					
					
				
		//check if "show new pages" is enabled
		if(isset($albdesign_newpost_saved_options) && isset($albdesign_newpost_saved_options['enabled_for_pages']) && $albdesign_newpost_saved_options['enabled_for_pages']=='yes'){
				
				$args_type_pages = array(
					'post_type' => 'page',
					'post_status' => 'publish',
					'showposts' => -1,
					'perm' => 'readable'
				);
				
				//Filter by date
				add_filter( 'posts_where', array('albdesign_whats_new','filter_posts_published_after_date'));
				
				$query2_pages = new WP_Query( $args_type_pages );	
				
				remove_filter( 'posts_where', array('albdesign_whats_new','filter_posts_published_after_date'));
				
				
				// Create an array with pages infos
				if ( $query2_pages->have_posts() ) {
				
					while ( $query2_pages->have_posts() ) {
					
						$query2_pages->the_post();
						
						$pages_array[get_the_id()]['id'] = get_the_id();
						
						$pages_array[get_the_id()]['title'] = get_the_title();
						
						$pages_array[get_the_id()]['permalink'] = get_the_permalink();
						
						$pages_array[get_the_id()]['author'] = get_the_author();

						
						//What image to show 
						if($albdesign_newpost_saved_options['show_images']=='yes'){
							
							//POST FEATURED IMAGE
							$post_featured = wp_get_attachment_image( get_post_thumbnail_id($query2_pages->post->ID),'thumbnail');
							
							if($post_featured){
							
								$pages_array[get_the_id()]['featured_image'] = $post_featured;
								
							}
								
						}
										
					} //end while
					
				} //end if have_posts				
				
			wp_reset_query();
				
			$response['pages']= $pages_array;
			$total_new_posts_found = $total_new_posts_found +  count($pages_array);
				
		} //end IF enabled for pages		
		
		
					
					
		//check if "show new woocommerce products" is enabled
		if(isset($albdesign_newpost_saved_options) && isset($albdesign_newpost_saved_options['enabled_for_wc_products']) && $albdesign_newpost_saved_options['enabled_for_wc_products']=='yes'){
		
				$args_type_product = array(
					'post_type' => 'product',
					'post_status' => 'publish',
					'showposts' => -1,
					'perm' => 'readable'
				);
				
				//Filter by date
				add_filter( 'posts_where', array('albdesign_whats_new','filter_posts_published_after_date'));
				
				$query2_product = new WP_Query( $args_type_product );	
				
				remove_filter( 'posts_where', array('albdesign_whats_new','filter_posts_published_after_date'));
				
				
				// Create an array with pages infos
				if ( $query2_product->have_posts() ) {
				
					while ( $query2_product->have_posts() ) {
					
						$query2_product->the_post();
						
						$products_array[get_the_id()]['id'] = get_the_id();
						
						$products_array[get_the_id()]['title'] = get_the_title();
						
						$products_array[get_the_id()]['permalink'] = get_the_permalink();
						
						$products_array[get_the_id()]['author'] = get_the_author();

						
						//What image to show 
						if($albdesign_newpost_saved_options['show_images']=='yes'){
							
							//PRODUCT FEATURED IMAGE
							$product_featured = wp_get_attachment_image( get_post_thumbnail_id($query2_product->post->ID),'thumbnail');
							
							if($product_featured){
							
								$products_array[get_the_id()]['featured_image'] = $product_featured;
								
							}
								
						}
										
					} //end while
					
				} //end if have_posts				
				
				wp_reset_query();
				
			$response['products']= $products_array;
			
			$total_new_posts_found = $total_new_posts_found + count($products_array);
				
		} //end IF enabled for wc products		

		$response['total_found'] = $total_new_posts_found;
		
		
		
		return $response;
		
	}	

	/*
	*	Get the excerpt by id
	*/
	function get_excerpt_by_id($post_id){
	
		$albdesign_get_saved_options_excerpt_length = get_option('albdesign_whatsnew_options');	
	
		$the_post = get_post($post_id); 
		
		$the_excerpt = $the_post->post_content; 
		
		$excerpt_length = $albdesign_get_saved_options_excerpt_length['excerpt_length']; //Sets excerpt length by word count
		
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
		
		$words = explode(' ', $the_excerpt, $excerpt_length + 1);
		
		if(count($words) > $excerpt_length) {
			
			array_pop($words);
			
			array_push($words, '…');
			
			$the_excerpt = implode(' ', $words);
		}
		
		$the_excerpt = '<p>' . $the_excerpt . '</p>';
		
		return $the_excerpt;
	}	
	
	/*
	*	Find template
	*/
	static public function albdesign_locate_template($file){

			//check if we are overriding from the theme folder
			if (file_exists(TEMPLATEPATH . '/albdesign_whatsnew/'.$file)){
				$return_template = TEMPLATEPATH .'/albdesign_whatsnew/'.$file;
			}
			else {
				//no overridings. use the templates from plugin folder
				$return_template = ALBDESIGN_WHATSNEW_PLUGIN_DIR . 'templates/'.$file;
				
			}

			return $return_template;

	}
		
		

	/*
	* Register the shortcode
	*/
	static public function add_shortcode_support(){
	
		global $albdesign_whatsnew_ouput_array;
		
		$rendered_output='';
		
		$options_array = get_option('albdesign_whatsnew_options');
		
		//print_r($options_array);
		
		
		$new_posts_text_header = $options_array['albdesign_whatsnew_post_header'];
		$new_pages_text_header = $options_array['albdesign_whatsnew_page_header'];
		$new_products_text_header = $options_array['albdesign_whatsnew_product_header'];

		$show_featured_image = $options_array['show_images'];
		$show_author = $options_array['show_author'];
		$show_date = $options_array['show_date'];
		
		$show_excerpt = $options_array['show_excerpt'];
		
		$nothing_new_posted = $options_array['nothing_new'];
		
		

		$albdesign_whatsnew_ouput_array = self::albdesign_get_posts_pages_products();
		
		
		ob_start();
		
		$template = self::albdesign_locate_template('whatsnew.php');
		
		//if nothing new 
		
		if($albdesign_whatsnew_ouput_array['total_found'] <= 0){

			$albdesign_whatsnew_ouput_array['error']='Nothing new';

		}

		
		include($template);
		
		$rendered_output = ob_get_clean();
					
		return $rendered_output;

	}
	



} //end class
 
 
 
$albdesign_whats_new = new albdesign_whats_new();

add_shortcode( 'albdesign_whatsnew', array( 'albdesign_whats_new','add_shortcode_support' ) );

