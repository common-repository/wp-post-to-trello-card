<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://javmah.tk/wp-post-to-trello-card
 * @since      1.0.0
 *
 * @package    Bptc
 * @subpackage Bptc/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!-- @ -->

<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h2><?php esc_attr_e( 'WordPress post to Trello card settings', 'wp-post-to-trello-card' ); ?></h2>

	<?php
		$token = get_option('bptc_access_code');
		$boards = $this->bptc_getting_all_trello_boards($token);
		# bptc nonce 
		$nonce = wp_create_nonce('bptc_nonce');
		# bptc Access code and Settings 
		$bptc_access_code = get_option('bptc_access_code');
		$bptc_settings = get_option('bptc_settings');

		// print_r( $bptc_settings );

		if (!empty( $bptc_settings ) ) {
			$bptc_settings = json_decode(  $bptc_settings , true ) ;
		}

		// print_r($bptc_settings );

		if ( isset($bptc_settings['bptc_list']) && isset($bptc_settings['bptc_board']) ) {
			$selected_list_id = $bptc_settings['bptc_list'] ;
			$list = $this->bptc_gatting_spacific_board_lists($token, $bptc_settings['bptc_board']);
		}

		// Retern Message from server After Submission 
		if (isset($_GET['msg'] )) {
			$msg = esc_html($_GET['msg']); 
		}else{
			$msg = '';
		}
		
	?>

	<?php if(!empty( $msg )) : ?>
		<div class="notice notice-error"><p><b> Message : </b> <?php echo $msg ?> </p></div>
	<?php endif; ?>


	<div class="notice notice-success inline">
		<p>
			<b>Trello Access Code</b> 

			<a href="https://trello.com/1/authorize?expiration=never&name=WordPress+post+to+Trello+card&scope=read%2Cwrite&response_type=token&key=7385fea630899510fd036b6e89b90c60"  style="margin-left:150px; text-decoration: none; " target="_blank">Trello access code</a>
			<?php
				if ($boards[0] == 200) {
					echo "<span style='color:green ; font-weight:bold ;'>  Connected .. </span>";
				}else{
					echo "<span style='color:red ; font-weight:bold ;'>  Disconnected </span>";
				}
			?>
			<br>

			<form name="bptc_access_code_form" action="admin-post.php"  method="POST" >

				<input type="hidden" name="action" value="bptcaccess_code">
				<input type="hidden" name="nonce" value="<?php echo $nonce ?>" /> 

				<?php if(!$token ) : ?>
					<input type="text" name="bptc_access_code" value="" style="min-width: 500px; height: 3em;" />
				<?php else : ?>
					<input type="text" name="bptc_access_code" value="<?php echo esc_html($token); ?>" style="min-width: 500px; height: 3em;" /> 
				<?php endif; ?>

				<input type="submit" class="button-primary" value="Update"/>
			</form>

		</p>
	</div>

	<!-- STARTS -->
	<form name="bptc_settings_form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php') ); ?>" >
		<input type="hidden" name="action" value="bptc_settings">
		<input type="hidden" name="nonce" value="<?php echo $nonce ?>" /> 
	<table class="widefat">
		<tr class="alternate">
			<td class="row-title"><label for="tablecell"><?php esc_attr_e('Select a Trello Board', 'wp-post-to-trello-card'); ?></label></td>
			<td>
				
				<select name="bptc_board" id="bptc_board">
					<option value=""> Select a Board	</option>
					<?php

						if ($boards[0] ==200 AND !empty($boards[0])) {
							foreach ($boards[1] as $key => $value) {
								echo "<option value='".$key."'". selected( $bptc_settings['bptc_board'], $key , TRUE ) ."> ".esc_html($value)."</option>";
							}
						}
					?>
				</select>

				<!-- Starts -->
				<div class="overlay-loader" >
					<div class="loader" id="bptc_loader" >
						<div></div>
						<div></div>
						<div></div>
						<div></div>
						<div></div>
						<div></div>
						<div></div>
					</div>
				</div>
				<!-- Ends -->

			</td>
		</tr>

		<tr>
			<td class="row-title"><label for="tablecell"><?php esc_attr_e('Select a Trello List', 'wp-post-to-trello-card'); ?> </td>
			<td>
				<select name="bptc_list" id="bptc_list" >
					<option value=""> Select a List	</option>
					<?php
						if (isset($list)  && !empty($list) ) {
							foreach ($list[1] as $key => $value) {
								echo "<option value='".$key."'". selected( $bptc_settings['bptc_list'], $key , TRUE ) ."> ".$value."</option>";
							}
						}
					?>
				</select>
			</td>
		</tr>

		<!-- yellow, purple, blue, red, green, orange, black, sky, pink, lime, null -->
		<tr class="alternate">
			<td class="row-title"><label for="tablecell"><?php esc_attr_e('Labels colour', 'wp-post-to-trello-card'); ?> </td>
			<td>
				<select name="Label_colour" id="wt_list" >
					<option value="null" 	<?php  echo selected("null" , 	$bptc_settings['Label_colour'], TRUE); ?>> Select a colour	</option>
					<option value="yellow"	<?php  echo selected("yellow" , $bptc_settings['Label_colour'],	TRUE); ?>> yellow			</option>
					<option value="purple"	<?php  echo selected("purple" , $bptc_settings['Label_colour'],	TRUE); ?>> purple			</option>
					<option value="blue" 	<?php  echo selected("blue" , 	$bptc_settings['Label_colour'],	TRUE); ?>> blue				</option>
					<option value="red" 	<?php  echo selected("red" , 	$bptc_settings['Label_colour'],	TRUE); ?>> red				</option>
					<option value="green" 	<?php  echo selected("green"  , $bptc_settings['Label_colour'],	TRUE); ?>> green			</option>
					<option value="orange" 	<?php  echo selected("orange" , $bptc_settings['Label_colour'],	TRUE); ?>> orange			</option>
					<option value="black"	<?php  echo selected("black" , 	$bptc_settings['Label_colour'],	TRUE); ?>> black			</option>
					<option value="sky" 	<?php  echo selected("sky" , 	$bptc_settings['Label_colour'], TRUE); ?>> sky				</option>
					<option value="pink" 	<?php  echo selected("pink" , 	$bptc_settings['Label_colour'],	TRUE); ?>> pink				</option>	
					<option value="lime" 	<?php  echo selected("lime" , 	$bptc_settings['Label_colour'],	TRUE); ?>> lime				</option>
				</select>
			</td>
		</tr>

		<tr >
			<td class="row-title"><label for="tablecell"><?php esc_attr_e('Create Card After POST', 'wp-post-to-trello-card'); ?> </td>
			<td>
				<select name="create_card_after" id="wt_list" > 
					<option value="new"		<?php echo selected("new" , 	$bptc_settings['create_card_after'],TRUE); ?>> new		</option>
					<option value="publish"	<?php echo selected("publish",	$bptc_settings['create_card_after'],TRUE); ?>> publish	</option>
					<option value="pending" <?php echo selected("pending" , $bptc_settings['create_card_after'],TRUE); ?>> pending	</option>
					<option value="draft"	<?php echo selected("draft",	$bptc_settings['create_card_after'],TRUE); ?>> draft 	</option>
					<option value="future" 	<?php echo selected("future" , 	$bptc_settings['create_card_after'],TRUE); ?>> future	</option>
					<option value="private"	<?php echo selected("private" , $bptc_settings['create_card_after'],TRUE); ?>> private	</option>
					<option value="inherit" <?php echo selected("inherit" , $bptc_settings['create_card_after'],TRUE); ?>> inherit	</option>
					<option value="trash" 	<?php echo selected("trash"  , 	$bptc_settings['create_card_after'],TRUE); ?>> trash	</option>
				</select>
			</td>
		</tr>

		<tr class="alternate">
			<td class="row-title"> </td>
			<td> 
				<input class="button-primary" type="submit"  value="<?php esc_attr_e('Save'); ?>"/> 
			</td>
		</tr>

		<!-- Create a Test Card Starts -->
		

	</table>
	</form>
	<!-- Ends -->

	<!-- Test Starts -->
	<?php if($boards[0] == 200) : ?>
	<div class="notice notice-info">
		<p>
			<b> Important Note : </b> 
			If you <b><i> change anything </i></b> on a Connected <b><i>Trello Board</i></b> , like Archive or Create new Board or List with the same name. Please create a new relation with Changed board and the list although the name is same . enjoy <br><i> p.s : Please let me Know if you have any problem or question on this Plugin .</i> </p>
	</div>
	<?php endif; ?>
	<!-- Test Ends -->


</div> <!-- .wrap -->


<style type="text/css">
	
	.overlay-loader {
		/*display: block;*/
		display: inline;
		margin: auto;
		width: 40px;
		height: 40px;
		position: relative;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
	}
	.loader {
		display:none;
		/**/
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		margin: auto;
		width: 40px;
		height: 40px;
		animation-name: rotateAnim;
			-o-animation-name: rotateAnim;
			-ms-animation-name: rotateAnim;
			-webkit-animation-name: rotateAnim;
			-moz-animation-name: rotateAnim;
		animation-duration: 0.4s;
			-o-animation-duration: 0.4s;
			-ms-animation-duration: 0.4s;
			-webkit-animation-duration: 0.4s;
			-moz-animation-duration: 0.4s;
		animation-iteration-count: infinite;
			-o-animation-iteration-count: infinite;
			-ms-animation-iteration-count: infinite;
			-webkit-animation-iteration-count: infinite;
			-moz-animation-iteration-count: infinite;
		animation-timing-function: linear;
			-o-animation-timing-function: linear;
			-ms-animation-timing-function: linear;
			-webkit-animation-timing-function: linear;
			-moz-animation-timing-function: linear;
	}
	.loader div {
		width: 3px;
		height: 3px;
		border-radius: 50%;
		border: 0px solid rgb(0,0,0);
		position: absolute;
		top: 1px;
		left: 0;
		right: 0;
		bottom: 0;
		margin: auto;
	}
	.loader div:nth-child(odd) {
		border-top: none;
		border-left: none;
	}
	.loader div:nth-child(even) {
		border-bottom: none;
		border-right: none;
	}
	.loader div:nth-child(2) {
		border-width: 1px;
		left: 0px;
		top: -2px;
		width: 5px;
		height: 5px;
	}
	.loader div:nth-child(3) {
		border-width: 1px;
		left: -0px;
		top: 1px;
		width: 7px;
		height: 7px;
	}
	.loader div:nth-child(4) {
		border-width: 1px;
		left: -0px;
		top: -2px;
		width: 10px;
		height: 10px;
	}
	.loader div:nth-child(5) {
		border-width: 1px;
		left: -0px;
		top: 2px;
		width: 13px;
		height: 13px;
	}
	.loader div:nth-child(6) {
		border-width: 2px;
		left: 0px;
		top: -2px;
		width: 16px;
		height: 16px;
	}
	.loader div:nth-child(7) {
		border-width: 2px;
		left: 0px;
		top: 2px;
		width: 20px;
		height: 20px;
	}


	@keyframes rotateAnim {
		from {
			transform: rotate(360deg);
		}
		to {
			transform: rotate(0deg);
		}
	}

	@-o-keyframes rotateAnim {
		from {
			-o-transform: rotate(360deg);
		}
		to {
			-o-transform: rotate(0deg);
		}
	}

	@-ms-keyframes rotateAnim {
		from {
			-ms-transform: rotate(360deg);
		}
		to {
			-ms-transform: rotate(0deg);
		}
	}

	@-webkit-keyframes rotateAnim {
		from {
			-webkit-transform: rotate(360deg);
		}
		to {
			-webkit-transform: rotate(0deg);
		}
	}

	@-moz-keyframes rotateAnim {
		from {
			-moz-transform: rotate(360deg);
		}
		to {
			-moz-transform: rotate(0deg);
		}
</style>


