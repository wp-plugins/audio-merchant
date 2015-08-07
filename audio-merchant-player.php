<?php
/**
 * @package Audio Merchant
 * @version 5.0.3
 * @author Audio Merchant <info@MyAudioMerchant.com>
 * @copyright (C) Copyright 2015 Audio Merchant, MyAudioMerchant.com. All rights reserved.
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.txt

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('ABSPATH') or die('No direct access!');

function audio_merchant_enqueue_frontend_player_scripts() {
    global $wp_scripts;
	
    $wp_scripts->queue = array();
	
	wp_enqueue_script('audio-merchant-player-js', audio_merchant_make_url_protocol_less(plugins_url('audio-merchant-html-player-frontend.js', __FILE__)), array('jquery', 'jquery-ui-core', 'jquery-ui-tooltip', 'jquery-ui-slider'));
}

function audio_merchant_enqueue_frontend_player_styles() {
    global $wp_styles;
	
    $wp_styles->queue = array();
	
	wp_enqueue_style('jquery-ui-css', audio_merchant_make_url_protocol_less(plugins_url('jquery-ui.css', __FILE__)));
	wp_enqueue_style('audio-merchant-frontend-css', audio_merchant_make_url_protocol_less(plugins_url('audio-merchant-html-player-frontend.css', __FILE__)), array('jquery-ui-css'));
}

add_action('wp_print_styles', 'audio_merchant_enqueue_frontend_player_styles', 1000);
add_action('wp_print_scripts', 'audio_merchant_enqueue_frontend_player_scripts', 1000);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<?php wp_head(); ?>
<script type="text/javascript">
	audioMerchantPlayer.imgPathUrl = '<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/', __FILE__)); ?>';
</script>
</head>
<body>
	<div class="audio_merchant_player">
		<div class="audio_merchant_player_inner">
			<div class="audio_cover_photo">
				<img class="audio_cover_photo_img" title="" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="100%" height="100%" alt="" border="0" align="middle" />
			</div><div class="player_controls">
				<div class="player_buttons">
					<a href="javascript: void(0);" class="big_play_button"><img class="play_button" src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/play_button.png', __FILE__)); ?>" alt="" border="0" title="<?php _e('Play', 'audio-merchant'); ?>" align="middle" /></a>
					<br />
					<a href="javascript: void(0);" class="previous_button_link"><img class="previous_button" src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/previous_button.png', __FILE__)); ?>" alt="" border="0" title="<?php _e('Previous', 'audio-merchant'); ?>" align="middle" /></a><a href="javascript: void(0);" class="next_button_link"><img class="next_button" src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/forward_button.png', __FILE__)); ?>" alt="" border="0" title="<?php _e('Next', 'audio-merchant'); ?>" align="middle" /></a>
				</div>
				<div class="audio_title"></div>
				<div class="clr"></div>
				<div class="time_slider" title="0:00"></div>
			</div>
			<div class="clr"></div>
			<ol class="track_list">
				<?php 
				if(!empty($audioRecords)) 
				{
					foreach($audioRecords as $key => $audioRecord)
					{
						if(preg_match('@^https?://@i', $audioRecord['audio_cover_photo']))
						{
							$coverPhotoUrl = '\''.audio_merchant_make_url_protocol_less($audioRecord['audio_cover_photo']).'\'';
						}
						elseif(!empty($audioRecord['audio_cover_photo']))
						{
							$coverPhotoUrl = '\''.audio_merchant_make_url_protocol_less($uploadUrl.'/'.$audioRecord['audio_cover_photo']).'\'';
						}
						else
						{
							$coverPhotoUrl = 'null';
						}
						
						if(preg_match('@^https?://@i', $audioRecord['audio_file_preview']))
						{
							$previewFileUrl = audio_merchant_make_url_protocol_less($audioRecord['audio_file_preview']);
						}
						else
						{
							$previewFileUrl = audio_merchant_make_url_protocol_less($uploadUrl.'/'.$audioRecord['audio_file_preview']);
						}
						
						$audioRecords[$key]['cover_photo_url'] = $coverPhotoUrl;
						$audioRecords[$key]['preview_file_url'] = $previewFileUrl;
				?>
				<li onclick="javascript: audioMerchantPlayer.play(<?php echo $audioRecord['audio_id']; ?>, '<?php echo base64_encode($audioRecord['audio_display_name']); ?>', <?php echo $audioRecord['audio_duration']; ?>, <?php echo $coverPhotoUrl; ?>, '<?php echo $previewFileUrl; ?>', true);" class="audio_<?php echo $audioRecord['audio_id']; ?>_row<?php if($audioRecord['is_sold_exclusive']) { ?> sold_audio_item<?php } ?>">
					<span class="left_container">
						<a class="play_button_small_link" title="<?php _e('Play', 'audio-merchant'); ?>" href="javascript: void(0);" onclick="javascript: event.stopPropagation(); audioMerchantPlayer.play(<?php echo $audioRecord['audio_id']; ?>, '<?php echo base64_encode($audioRecord['audio_display_name']); ?>', <?php echo $audioRecord['audio_duration']; ?>, <?php echo $coverPhotoUrl; ?>, '<?php echo $previewFileUrl; ?>', true);"><img class="play_button_small play_<?php echo $audioRecord['audio_id']; ?>" src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/play_button_small.png', __FILE__)); ?>" alt="" border="0" align="middle" /></a> <a title="<?php _e('Play', 'audio-merchant'); ?>" href="javascript: void(0);" onclick="javascript: event.stopPropagation(); audioMerchantPlayer.play(<?php echo $audioRecord['audio_id']; ?>, '<?php echo base64_encode($audioRecord['audio_display_name']); ?>', <?php echo $audioRecord['audio_duration']; ?>, <?php echo $coverPhotoUrl; ?>, '<?php echo $previewFileUrl; ?>', true);"><?php echo $audioRecord['audio_display_name']; ?></a>
					</span><span class="right_container">
						<?php if($audioRecord['is_sold_exclusive']) { ?>
							<?php _e('Sold', 'audio-merchant'); ?>
						<?php } elseif($audioRecord['audio_lease_price'] == 0.00 && $audioRecord['audio_exclusive_price'] == 0.00) { ?>
							<?php if((int)audio_merchant_get_setting('download_user_login_required') > 0 && !is_user_logged_in()) { ?>
							<a href="<?php echo wp_login_url($currentUrl); ?>" target="_top" onclick="javascript: event.stopPropagation(); alert('<?php htmlentities(_e('Please login or register to continue... You will now be redirected...', 'audio-merchant'), ENT_QUOTES); ?>');" title="<?php _e('Download FREE!', 'audio-merchant'); ?>"><img class="action_icon" align="middle" src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/download_icon.png', __FILE__)); ?>" alt="" border="0" /></a>
							<?php } else { ?>
							<a onclick="javascript: event.stopPropagation();" href="<?php echo admin_url('admin-ajax.php?action=audio_merchant_download_free&amp;audio_id='.(string)$audioRecord['audio_id']); ?>" title="<?php _e('Download FREE!', 'audio-merchant'); ?>"><img class="action_icon" align="middle" src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/download_icon.png', __FILE__)); ?>" alt="" border="0" /></a>
							<?php } ?>
						<?php } ?>
					</span>
					<div class="clr"></div>
				</li>
				<?php 
					}
				?>
				<script type="text/javascript">
					jQuery(document).ready(function() {
						var autoPlay = <?php echo ((int)@$_GET['autoplay'] > 0) ? 'true' : 'false'; ?>;
						
						audioMerchantPlayer.play(<?php echo $audioRecords[0]['audio_id']; ?>, '<?php echo base64_encode($audioRecords[0]['audio_display_name']); ?>', <?php echo $audioRecords[0]['audio_duration']; ?>, <?php echo $audioRecords[0]['cover_photo_url']; ?>, '<?php echo $audioRecords[0]['preview_file_url']; ?>', autoPlay);
					});
				</script>
				<?php
				}
				?>
			</ol>
		</div>
	</div>
	<audio class="audio_player">Your browser does not support audio.</audio>
<?php wp_footer(); ?>
</body>
</html>