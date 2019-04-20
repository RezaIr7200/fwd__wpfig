<?php

global $wpdb;


//create wordpress nonce
$_nonce = wp_create_nonce( 'wp_trus_nonce' ); 

$wptrus_list = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}".WPTRUS_TABLE_NAME . " WHERE archive=0 ORDER BY ID DESC" );

?>
<div class="wrap">

	<div class="wptrus-list-wrapper">
	
		<form name="wp-trus-list" method="POST" class="wptrus-list-form">
	
			<table class="wptrus-wrapper wp-list-table widefat fixed striped">
			<tr>
				<th>ID</th>
				<th>NAME</th>
				<th>Email</th>
				<th>Message</th>
				<th>Date</th>
				<th>Action</th>
			</tr>
	
			<?php foreach( $wptrus_list as $user_suggest ): ?>
			<?php $user_suggest->time =  date_i18n( get_option('date_format'),$user_suggest->time ) ?>
				<tr class="wp-trus-item">
					<td class="wp-trus-item__id"	><?php 	echo (int)		$user_suggest->id 			?> </td>
					<td class="wp-trus-item__name"	><?php 	echo esc_html(	$user_suggest->name 	)	?> </td>
					<td class="wp-trus-item__phone"	><?php 	echo esc_html(	$user_suggest->email 	)	?> </td>
					<td class="wp-trus-item__type"	><?php 	echo esc_html(	$user_suggest->message 	) 	?> </td>
					<td class="wp-trus-item__date"	><?php 	echo esc_html(	$user_suggest->time 	)	?> </td>
					
					<td class="wp-trus-item__actions">

						<a class="button" href="<?php echo get_edit_post_link($user_suggest->postid) ?>"><?php _e('Edit Post', 'wptrus') ?></a>

						<form action="" name="wp-trus-action-archive" method="POST">
							<input type="hidden" name="wp_trus_nonce" value="<?php echo $_nonce ?>">
							<input type="hidden" name="wp-trus-action" value="wptrus_archive">
							<input type="hidden" name="wp-trus-id" value="<?php echo (int)$user_suggest->id ?>">
							<button name="archive" type="submit" class="button archive">&#9989; <?php _e('Archive', 'wptrus') ?></button>
						</form>


					</td>
				</tr>
				
			<?php endforeach; ?>
			</table>
	
		</form>
	
	</div>
	
	
</div>
