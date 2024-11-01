<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://javmah.tk/WordPress post to Trello card 
 * @since      1.0.0
 *
 * @package    Wp_Post_To_Trello_Card
 * @subpackage Wp_Post_To_Trello_Card/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Post_To_Trello_Card
 * @subpackage Wp_Post_To_Trello_Card/admin
 * @author     javmah <jaedmah@gmail.com>
 */
class Wp_Post_To_Trello_Card_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	# Application ID 
	private $key = '7385fea630899510fd036b6e89b90c60';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-post-to-trello-card-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-post-to-trello-card-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function bptc_menu_pages($value=''){
		add_menu_page( __( 'Blog Post to Trello Card', 'bptc' ), __( 'Post to Trello Card', 'bptc' ),'manage_options','bptc', array( $this, 'bptc_settings_view' ),'dashicons-media-interactive');
	}


	public function bptc_settings_view($value=''){
		require_once plugin_dir_path(dirname(__FILE__)).'admin/partials/wp-post-to-trello-card-admin-display.php';
	}


	# Testing and Validation Area 
	public function bptc_admin_notice($value=''){
		// $token = get_option('bptc_access_code');
		// print_r($token );

		// echo "<br>";
		// $bptc_settings = get_option('bptc_settings');
		// print_r($bptc_settings );

	}


	public function bptc_access_code($value=''){
		if (!wp_verify_nonce( $_POST['nonce'] , 'bptc_nonce' ) || empty($_POST['bptc_access_code'])  ) {
			wp_safe_redirect(site_url( '/wp-admin/admin.php?page=bptc&msg=empty+Trello+access+code' ));
			return;
		}

		update_option('bptc_access_code', sanitize_text_field( trim($_POST['bptc_access_code']) ));
		wp_safe_redirect( esc_url( site_url( '/wp-admin/admin.php?page=bptc' )) );
		exit();
	}

	public function bptc_settings(){
		if (!wp_verify_nonce( $_POST['nonce'] , 'bptc_nonce' ) ) {
			return;
		}

		if ( empty($_POST['bptc_board']) || empty($_POST['bptc_list']) ) {
			wp_safe_redirect(site_url("/wp-admin/admin.php?page=bptc&msg=empty+trello+board+or+trello+list.+Please+%2C+select+a+board+and+its+list+then+resubmit." ));
			exit();
		}

		$data = array();
		$data['bptc_board'] 		= sanitize_text_field(	trim($_POST['bptc_board'])			);
		$data['bptc_list'] 			= sanitize_text_field(	trim($_POST['bptc_list'])				);
		$data['Label_colour'] 		= sanitize_text_field(	trim($_POST['Label_colour'])		);
		$data['create_card_after'] 	= sanitize_text_field(	trim($_POST['create_card_after'])	);

		update_option('bptc_settings', json_encode( $data ,true));
		wp_safe_redirect( esc_url( site_url( '/wp-admin/admin.php?page=bptc' )) );
		exit();
	}


	public function bptc_getting_all_trello_boards($token=''){
		if (empty($token)) {
			return;
		}

		$key = $this->key ;
		$url = 'https://api.trello.com/1/members/me/boards?&filter=open&key='.$key.'&token='.$token.'';

		$trello_returns = wp_remote_get( $url , array());
		$boards = array();

		if ($trello_returns['response']['code'] == 200) {
			foreach (json_decode($trello_returns['body'] , true) as $key => $value) {
				$boards[$value['id']] = $value['name'];
			}
		}else{
			# Error Log 
		}
		return array( $trello_returns['response']['code'] , $boards );
	}

	# Gatting Lists
	public function bptc_gatting_spacific_board_lists( $token='', $board_id=''){
		if (empty($token) || empty($board_id)) {
			return;
		}
		$key = $this->key ;
		$url = 'https://api.trello.com/1/boards/'.$board_id.'/lists?filter=open&key='.$key.'&token='.$token.'';

		$trello_returns = wp_remote_get( $url , array());
		$lists = array();

		if ($trello_returns['response']['code'] == 200) {
			foreach (json_decode($trello_returns['body'] , true) as $key => $value) {
				$lists[$value['id']] = $value['name'];
			}
		}else{
			# Error Log 
			
		}

		return array( $trello_returns['response']['code'] , $lists );
	}

	# AJAX
	public function bptc_ajax_response(){
		$boardId = sanitize_text_field( $_POST['boardId'] );
		$token = get_option('bptc_access_code');

		if (empty($boardId) ||  empty($token)) {
			return ;
		}

		$lists =  $this->bptc_gatting_spacific_board_lists($token, $boardId);
		if ($lists[0]==200) {
			echo json_encode($lists[1],true);
		}
		
		exit();
	}

	# Action Fired Func
	public function bptc_on_all_post_status_transitions($new_status, $old_status, $post ){
		$token = get_option('bptc_access_code');
		$bptc_settings = get_option('bptc_settings');
		$res = '';

		if (empty($token)) {
			# error ::  No token  || token in wp option is empty 
			return ;
		}

		if (!empty( $bptc_settings ) ) {
			# ok :: had settings Now decode settings JSON to PHP array ;
			$bptc_settings = json_decode(  $bptc_settings , true ) ;
		}else{
			# error :: settings is not defined || no settings on wp options 
			return;
		}

		if (empty( $bptc_settings['bptc_list'] )) {
			# error :: trello list id is Empty || no Trello list id on wp option
			return;
		}

		if ($old_status == 'new' AND $bptc_settings['create_card_after'] ==  'new') {
			# inherit
			$res = $this->bptc_create_trello_card($this->key, $token,$bptc_settings['bptc_list'], $bptc_settings['Label_colour'] ,$post->ID , $post);

		}elseif ($new_status == 'pending' AND $bptc_settings['create_card_after'] ==  'pending') {
			# pending
			$res = $this->bptc_create_trello_card($this->key, $token, $bptc_settings['bptc_list'] , $bptc_settings['Label_colour'] , $post->ID , $post);

		}elseif ($new_status == 'draft' AND $bptc_settings['create_card_after'] ==  'draft') {
			# draft
			$res = $this->bptc_create_trello_card($this->key, $token, $bptc_settings['bptc_list'], $bptc_settings['Label_colour'] , $post->ID , $post);

		}elseif ($new_status == 'future' AND $bptc_settings['create_card_after'] ==  'future') {
			# future
			$res = $this->bptc_create_trello_card($this->key, $token, $bptc_settings['bptc_list'] , $bptc_settings['Label_colour'] , $post->ID , $post);

		}elseif ($new_status == 'private' AND $bptc_settings['create_card_after'] ==  'private') {
			# private
			$res = $this->bptc_create_trello_card($this->key, $token, $bptc_settings['bptc_list'] , $bptc_settings['Label_colour'] , $post->ID , $post);

		}elseif ($new_status == 'trash' AND $bptc_settings['create_card_after'] ==  'trash') {
			# trash
			$res = $this->bptc_create_trello_card($this->key, $token, $bptc_settings['bptc_list'] ,  $bptc_settings['Label_colour']  , $post->ID , $post);

		}else{
			# Nothing Left to 
		}
	}

	#  Post Published func fired 
	public function bptc_post_published_notification( $ID, $post ){
		$token = get_option('bptc_access_code');
		$bptc_settings = get_option('bptc_settings');

		if (!empty( $bptc_settings ) ) {
			$bptc_settings = json_decode(  $bptc_settings , true ) ;
		}else{
			return;
		}

		if (empty( $bptc_settings['bptc_list'] )) {
			# error :: no Trello list id on wp options 
			return;
		}


		if ($bptc_settings['create_card_after'] == "publish") {

			$this->bptc_create_trello_card($this->key, $token,  $bptc_settings['bptc_list'],  $bptc_settings['Label_colour'] , $ID , $post);
		}	
	} 

	# Create a New Card
	public function bptc_create_trello_card($key ='', $token ='', $bptc_list ='',$Label_colour='' , $post_id ='', $post =''){


		if (empty($token) ) {
			return "Problem 2 :: Token " ;
		}

		if ( empty($bptc_list) ) {
			return "Problem 1 :: Trello  list  id is Empty" ;
		}

		if ( empty($post_id ) || empty($post ) ) {
			return "Problem 3 :: Post id is Nill or , There is NO POST";
		}


   		$author 		= 	$post->post_author ;
   	   	$display_name 	=  	get_the_author_meta( 'display_name', $author );
   	   	$email 			= 	get_the_author_meta( 'user_email', $author );
   	   	$title 			= 	$post->post_title;
   	   	$permalink 		= 	get_permalink( $post_id );
   	   	$post_date 		= 	$post->post_date ;
   	   	$post_content 	= 	$post->post_content ;
   	   	$post_status 	= 	$post->post_status ;

   	   	$card_title 	= "[".$post_id ."#".$display_name."#". date('Y-m-d', strtotime($post_date))."] ". substr($title,0,40).'...'; ;

		$desc  = " %0A ** Display name :** " 		. urlencode($display_name) ;
		$desc .= " %0A ** Email :** " 				. urlencode(	$email );
		$desc .= " %0A ** Post date :** " 			. urlencode($post_date);
		$desc .= " %0A ** Post status  :** " 		. urlencode($post_status) ;
		$desc .= " %0A ** Post title  :** %0A" 		. urlencode($title) ;
		$desc .= " %0A ** Post content  :** %0A%0A ". urlencode( str_replace("&nbsp;",' ', strip_tags($post_content , '&nbsp;'))  );
		
		# Creating a Card in The List 
		
		$card_url = 'https://api.trello.com/1/cards?name='.urlencode($card_title).'&desc='.$desc.'&pos=top&idList='.$bptc_list.'&keepFromSource=all&key='.$key.'&token='.$token.'';

		$Create_card_response = wp_remote_post($card_url , array());

		if ($Create_card_response['response']['code'] == 200) {
			# convart body Json into array 
			$new_card_body = json_decode( $Create_card_response['body'] , true );
			# Set Label Colour 
			if (! empty($Label_colour) || $Label_colour != 'null' ) {
				wp_remote_post('https://api.trello.com/1/cards/'.$new_card_body['id'].'/labels?color='.$Label_colour.'&key='.$key.'&token='.$token.'',array());
			}
		}

		return  $Create_card_response['response']['code'] ;
	}

}

