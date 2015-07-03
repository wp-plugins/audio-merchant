<?php
/**
 * @package Audio Merchant
 * @version 5.0.1
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

$supportedImageTypes = array('jpg', 'jpeg', 'png', 'gif');
$supportedAudioExtensions = wp_get_audio_extensions();

$uploadDir = wp_upload_dir();
$uploadUrl = $uploadDir['baseurl'].'/audio/'.get_current_blog_id();
$uploadDir = $uploadDir['basedir'].DIRECTORY_SEPARATOR.'audio'.DIRECTORY_SEPARATOR.get_current_blog_id();

$tempLinkExpirationsDays = (int)audio_merchant_get_setting('temp_download_link_expiration');
$currency = audio_merchant_get_setting('currency');
$downloadRequiresRegistration = (int)audio_merchant_get_setting('download_user_login_required');
$purchaseRequiresRegistration = (int)audio_merchant_get_setting('purchase_user_login_required');

$audioFiles = audio_merchant_get_audio(false);

if(!get_option('css_frontend_default'))
{
	update_option('css_frontend_default', file_get_contents(plugin_dir_path( __FILE__ ).'audio-merchant-html-player-frontend.css'));
}
?>
<div class="wrap">
	<h2><?php echo __('Audio Merchant', 'audio-merchant'); ?></h2>
	<div id="audio_merchant_main_tabs">
		<ul>
			<li><a href="#my_audio_tab"><?php echo __('Audio', 'audio-merchant'); ?></a></li>
			<li><a href="#html_player_tab"><?php echo __('Playlists', 'audio-merchant'); ?></a></li>
			<li><a href="#orders_tab"><?php echo __('Orders', 'audio-merchant'); ?></a></li>
			<li><a href="#settings_tab"><?php echo __('Settings', 'audio-merchant'); ?></a></li>
		</ul>
		<div id="my_audio_tab">
			<button name="add_audio_btn" id="add_audio_btn"><?php echo __('Add Audio', 'audio-merchant'); ?></button>
			<br /><br />
			<table id="audio_inventory_table" class="compact hover cell-border stripe" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><?php echo __('Audio ID', 'audio-merchant'); ?></th>
						<th><?php echo __('Audio Display Name', 'audio-merchant'); ?></th>
						<th><?php echo __('Lease Price', 'audio-merchant'); ?></th>
						<th><?php echo __('Exclusive Price', 'audio-merchant'); ?></th>
						<th><?php echo __('Cover Photo', 'audio-merchant'); ?></th>
						<th><?php echo __('Audio File', 'audio-merchant'); ?></th>
						<th><?php echo __('Audio Preview File', 'audio-merchant'); ?></th>
						<th><?php echo __('Additional Lease File', 'audio-merchant'); ?></th>
						<th><?php echo __('Additional Exclusive File', 'audio-merchant'); ?></th>
						<th><?php echo __('Audio Duration', 'audio-merchant'); ?></th>
						<th><?php echo __('Date Created', 'audio-merchant'); ?></th>
						<th><?php echo __('Last Modified', 'audio-merchant'); ?></th>
						<th><?php echo __('Options', 'audio-merchant'); ?></th>
					</tr>
				</thead>
			</table>
			<div id="new_audio_wnd" title="<?php echo __('Add Audio', 'audio-merchant'); ?>">
				<form id="new_audio_file_form" method="post" enctype="multipart/form-data" onsubmit="javascript: return addNewAudioFile();">
					<fieldset>
						<label for="audio_display_name"><?php echo __('Audio Display Name:', 'audio-merchant'); ?></label>
						<input type="text" name="audio_display_name" id="audio_display_name" value="" maxlength="190" placeholder="<?php echo __('Example Format: Artist - Album - Song Name 90 BPM', 'audio-merchant'); ?>" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This is the name displayed to the guest in the frontend player. You can add things like album name, BPM, etc. This field is completely flexible.', 'audio-merchant'); ?>" />
						<br />
						<br />

						<label for="audio_lease_price"><?php echo __('Lease Price:', 'audio-merchant'); ?></label>
						<input type="text" name="audio_lease_price" id="audio_lease_price" value="" placeholder="<?php echo __('Example Format: 20.00', 'audio-merchant'); ?>" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The price to purchase a lease license and download the full quality audio file. Set to 0 to disable this option. If lease price AND exclusive price are both set to 0 then this file will be available for free download.', 'audio-merchant'); ?>" />
						<br /><br />
						<label for="audio_exclusive_price"><?php echo __('Exclusive Price:', 'audio-merchant'); ?></label>
						<input type="text" name="audio_exclusive_price" id="audio_exclusive_price" value="" placeholder="<?php echo __('Example Format: 200.00', 'audio-merchant'); ?>" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The price to purchase an exclusive license and download the full quality audio file. When files are purchased exclusively, they are removed from the frontend player after a successfull checkout. Set to 0 to disable this option. If exclusive price AND lease price are both set to 0 then this file will be available for free download.', 'audio-merchant'); ?>" />

						<br />
						<br />
						<div class="upload_file_wrapper">
							<div id="cover_photo_upload_file_wrapper">
								<label for="cover_photo_upload_file"><?php echo __('Cover Photo:', 'audio-merchant'); ?></label>
								<input type="file" name="cover_photo_upload_file" id="cover_photo_upload_file" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The image that represents this audio file. This is the image displayed on the frontend player when this song is being played. The image width and height should be proportional. We recommend a size of 200 x 200 pixels, however this field is flexible and will accept any image dimensions and will resize them automatically. Acceptable formats are: .png, .jpg, .gif', 'audio-merchant'); ?>" />
							</div>
							<div id="cover_photo_url_file_wrapper" class="hidden">
								<label for="cover_photo_url_file"><?php echo __('Cover Photo:', 'audio-merchant'); ?></label>
								<input type="text" name="cover_photo_url_file" id="cover_photo_url_file" value="" placeholder="http://externalserver.com/my_cover_photo.jpg" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The image that represents this audio file. This is the image displayed on the frontend player when this song is being played. The image width and height should be proportional. We recommend a size of 200 x 200 pixels, however this field is flexible and will accept any image dimensions and will resize them automatically. Acceptable formats are: .png, .jpg, .gif', 'audio-merchant'); ?>" />
							</div>
							<div id="cover_photo_existing_file_wrapper" class="hidden">
								<?php echo __('Cover Photo:', 'audio-merchant'); ?>
								<span class="existing_file_scroller">
									<?php
									$existingFiles = array();

									if(file_exists($uploadDir))
									{
										if($supportedImageTypes)
										{
											foreach($supportedImageTypes as $supportedExtension)
											{
												foreach(glob($uploadDir.DIRECTORY_SEPARATOR.'*.'.$supportedExtension) as $file) 
												{
													$existingFiles[] = $file;
												}

												foreach(glob($uploadDir.DIRECTORY_SEPARATOR.'*.'.strtoupper($supportedExtension)) as $file) 
												{
													$existingFiles[] = $file;
												}
											}

											$existingFiles = array_unique($existingFiles);
										}
									}

									if(!empty($existingFiles))
									{
										foreach($existingFiles as $key => $existingFile)
										{
											$filename = basename($existingFile);

											echo '<span><input type="radio" name="cover_photo_existing_file" id="cover_photo_existing_file_'.$key.'" value="'.$filename.'" /><label for="cover_photo_existing_file_'.$key.'"><img src="'.audio_merchant_make_url_protocol_less($uploadUrl).'/'.$filename.'" width="50" height="50" border="0" alt="" /><br /><a href="'.$uploadUrl.'/'.$filename.'" target="_blank" title="'.$filename.'">'.substr($filename, 0, 9).'...</a></label></span>';
										}
									}
									else 
									{
										echo '<span class="warning_msg">'.__('There are currently no files uploaded!', 'audio-merchant').'</span>';
									}
									?>
								</span> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The image that represents this audio file. This is the image displayed on the frontend player when this song is being played. The image width and height should be proportional. We recommend a size of 200 x 200 pixels, however this field is flexible and will accept any image dimensions and will resize them automatically. Acceptable formats are: .png, .jpg, .gif', 'audio-merchant'); ?>" />
							</div>
							<div class="small_url_container">
								<input type="hidden" class="upload_mode" name="cover_photo_mode" value="upload" />
								<a id="cover_photo_upload_link" class="hidden" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('upload', document.getElementById('cover_photo_upload_link'), 'cover_photo_upload_file_wrapper');"><?php echo __('Upload File', 'audio-merchant'); ?></a> <a id="cover_photo_url_link" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('url', document.getElementById('cover_photo_url_link'), 'cover_photo_url_file_wrapper');"><?php echo __('Specify URL', 'audio-merchant'); ?></a> <a id="cover_photo_existing_link" class="last_small_url" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('existing', document.getElementById('cover_photo_existing_link'), 'cover_photo_existing_file_wrapper');"><?php echo __('Select Existing File', 'audio-merchant'); ?></a>
							</div>
							<div class="clear"></div>
						</div>
						<div class="upload_file_wrapper">
							<div id="audio_upload_file_wrapper">
								<label for="audio_upload_file"><?php echo __('Full Quality Audio File:', 'audio-merchant'); ?></label>
								<input type="file" name="audio_upload_file" id="audio_upload_file" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This is the full quality audio file that is delivered to the buyer at the end of a successful purchase. This file should NOT contain any audio watermarks. Acceptable formats are: .wav, .mp3, .ogg, .m4a', 'audio-merchant'); ?>" />
							</div>
							<div id="audio_url_file_wrapper" class="hidden">
								<label for="audio_url_file"><?php echo __('Full Quality Audio File:', 'audio-merchant'); ?></label>
								<input type="text" name="audio_url_file" id="audio_url_file" value="" placeholder="http://externalserver.com/my_audio_file.wav" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This is the full quality audio file that is delivered to the buyer at the end of a successful purchase. This file should NOT contain any audio watermarks. Acceptable formats are: .wav, .mp3, .ogg, .m4a', 'audio-merchant'); ?>" />
							</div>
							<div id="audio_existing_file_wrapper" class="hidden">
								<?php echo __('Full Quality Audio File:', 'audio-merchant'); ?>
								<span class="existing_file_scroller">
									<?php
									$existingAudioFiles = array();

									if(file_exists($uploadDir))
									{
										if($supportedAudioExtensions)
										{
											foreach($supportedAudioExtensions as $supportedAudioExtension)
											{
												foreach(glob($uploadDir.DIRECTORY_SEPARATOR.'*.'.$supportedAudioExtension) as $file) 
												{
													$existingAudioFiles[] = $file;
												}

												foreach(glob($uploadDir.DIRECTORY_SEPARATOR.'*.'.strtoupper($supportedAudioExtension)) as $file) 
												{
													$existingAudioFiles[] = $file;
												}
											}

											$existingAudioFiles = array_unique($existingAudioFiles);
										}
									}

									if(!empty($existingAudioFiles))
									{
										foreach($existingAudioFiles as $key => $existingAudioFile)
										{
											$filename = basename($existingAudioFile);

											echo '<span><input type="radio" name="audio_existing_file" id="audio_existing_file_'.$key.'" value="'.$filename.'" /><label for="audio_existing_file_'.$key.'"><img src="'.audio_merchant_make_url_protocol_less(plugins_url('images/audio_icon.png', __FILE__)).'" width="50" height="50" border="0" alt="" /><br /><a href="'.$uploadUrl.'/'.$filename.'" target="_blank" title="'.$filename.'">'.substr($filename, 0, 9).'...</a></label></span>';
										}
									}
									else 
									{
										echo '<span class="warning_msg">'.__('There are currently no files uploaded!', 'audio-merchant').'</span>';
									}
									?>
								</span> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This is the full quality audio file that is delivered to the buyer at the end of a successful purchase. This file should NOT contain any audio watermarks. Acceptable formats are: .wav, .mp3, .ogg, .m4a', 'audio-merchant'); ?>" />
							</div>
							<div class="small_url_container">
								<input type="hidden" class="upload_mode" name="audio_mode" value="upload" />
								<a id="audio_upload_link" class="hidden" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('upload', document.getElementById('audio_upload_link'), 'audio_upload_file_wrapper');"><?php echo __('Upload File', 'audio-merchant'); ?></a> <a id="audio_url_link" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('url', document.getElementById('audio_url_link'), 'audio_url_file_wrapper');"><?php echo __('Specify URL', 'audio-merchant'); ?></a> <a id="audio_existing_link" class="last_small_url" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('existing', document.getElementById('audio_existing_link'), 'audio_existing_file_wrapper');"><?php echo __('Select Existing File', 'audio-merchant'); ?></a>
							</div>
							<div class="clear"></div>
						</div>
						<div class="upload_file_wrapper">
							<div id="preview_audio_upload_file_wrapper">
								<label for="preview_audio_upload_file"><?php echo __('Preview Audio File:', 'audio-merchant'); ?></label>
								<input type="file" name="preview_audio_upload_file" id="preview_audio_upload_file" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This is the audio file that the guest listens to on the frontend player BEFORE they purchase the full quality audio file. This file is used as a form of protection, and MAY contain audio watermarks and/or other forms of protection. This file may even be shorter than the full quality audio file, if you choose. Alternatively, you can play the full quality audio file for your guests as the preview file, which we HIGHLY discourage, by re-uploading your full quality audio file again in this field, however we HIGHLY recommend differentiating your preview file audo file and your full quality audio file for security reasons. Acceptable formats are: .wav, .mp3, .ogg, .m4a', 'audio-merchant'); ?>" />
							</div>
							<div id="preview_audio_url_file_wrapper" class="hidden">
								<label for="preview_audio_url_file"><?php echo __('Preview Audio File:', 'audio-merchant'); ?></label>
								<input type="text" name="preview_audio_url_file" id="preview_audio_url_file" value="" placeholder="http://externalserver.com/my_audio_preview_file.wav" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This is the audio file that the guest listens to on the frontend player BEFORE they purchase the full quality audio file. This file is used as a form of protection, and MAY contain audio watermarks and/or other forms of protection. This file may even be shorter than the full quality audio file, if you choose. Alternatively, you can play the full quality audio file for your guests as the preview file, which we HIGHLY discourage, by re-uploading your full quality audio file again in this field, however we HIGHLY recommend differentiating your preview file audo file and your full quality audio file for security reasons. Acceptable formats are: .wav, .mp3, .ogg, .m4a', 'audio-merchant'); ?>" />
							</div>
							<div id="preview_audio_existing_file_wrapper" class="hidden">
								<?php echo __('Preview Audio File:', 'audio-merchant'); ?>
								<span class="existing_file_scroller">
									<?php
									$existingAudioFiles = array();

									if(file_exists($uploadDir))
									{
										if($supportedAudioExtensions)
										{
											foreach($supportedAudioExtensions as $supportedAudioExtension)
											{
												foreach(glob($uploadDir.DIRECTORY_SEPARATOR.'*.'.$supportedAudioExtension) as $file) 
												{
													$existingAudioFiles[] = $file;
												}

												foreach(glob($uploadDir.DIRECTORY_SEPARATOR.'*.'.strtoupper($supportedAudioExtension)) as $file) 
												{
													$existingAudioFiles[] = $file;
												}
											}

											$existingAudioFiles = array_unique($existingAudioFiles);
										}
									}

									if(!empty($existingAudioFiles))
									{
										foreach($existingAudioFiles as $key => $existingAudioFile)
										{
											$filename = basename($existingAudioFile);

											echo '<span><input type="radio" name="preview_audio_existing_file" id="preview_audio_existing_file_'.$key.'" value="'.$filename.'" /><label for="preview_audio_existing_file_'.$key.'"><img src="'.audio_merchant_make_url_protocol_less(plugins_url('images/audio_icon.png', __FILE__)).'" width="50" height="50" border="0" alt="" /><br /><a href="'.$uploadUrl.'/'.$filename.'" target="_blank" title="'.$filename.'">'.substr($filename, 0, 9).'...</a></label></span>';
										}
									}
									else 
									{
										echo '<span class="warning_msg">'.__('There are currently no files uploaded!', 'audio-merchant').'</span>';
									}
									?>
								</span> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This is the audio file that the guest listens to on the frontend player BEFORE they purchase the full quality audio file. This file is used as a form of protection, and MAY contain audio watermarks and/or other forms of protection. This file may even be shorter than the full quality audio file, if you choose. Alternatively, you can play the full quality audio file for your guests as the preview file, which we HIGHLY discourage, by re-uploading your full quality audio file again in this field, however we HIGHLY recommend differentiating your preview file audo file and your full quality audio file for security reasons. Acceptable formats are: .wav, .mp3, .ogg, .m4a', 'audio-merchant'); ?>" />
							</div>
							<div class="small_url_container">
								<input type="hidden" class="upload_mode" name="preview_audio_mode" value="upload" />
								<a id="preview_audio_upload_link" class="hidden" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('upload', document.getElementById('preview_audio_upload_link'), 'preview_audio_upload_file_wrapper');"><?php echo __('Upload File', 'audio-merchant'); ?></a> <a id="preview_audio_url_link" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('url', document.getElementById('preview_audio_url_link'), 'preview_audio_url_file_wrapper');"><?php echo __('Specify URL', 'audio-merchant'); ?></a> <a id="preview_audio_existing_link" class="last_small_url" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('existing', document.getElementById('preview_audio_existing_link'), 'preview_audio_existing_file_wrapper');"><?php echo __('Select Existing File', 'audio-merchant'); ?></a>
							</div>
							<div class="clear"></div>
						</div>
						<div class="upload_file_wrapper">
							<div id="additional_lease_file_wrapper">
								<label for="additional_lease_file"><?php echo __('Additional File To Provide With Lease Purchase:', 'audio-merchant'); ?></label>
								<input type="file" name="additional_lease_file" id="additional_lease_file" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This file is provided to buyers AFTER they purchase a lease license. This is a great place to provide additional license files (.pdf), or any other file that may relate to your audio file. Accepts any file format. If you wish to provide multiple files, please archive (.zip, .rar, etc.) them up into one file first, then upload that file here.', 'audio-merchant'); ?>" />
							</div>
							<div id="additional_lease_url_file_wrapper" class="hidden">
								<label for="additional_lease_url_file"><?php echo __('Additional File To Provide With Lease Purchase:', 'audio-merchant'); ?></label>
								<input type="text" name="additional_lease_url_file" id="additional_lease_url_file" value="" placeholder="http://externalserver.com/my_additional_lease_file.zip" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This file is provided to buyers AFTER they purchase a lease license. This is a great place to provide additional license files (.pdf), or any other file that may relate to your audio file. Accepts any file format. If you wish to provide multiple files, please archive (.zip, .rar, etc.) them up into one file first, then upload that file here.', 'audio-merchant'); ?>" />
							</div>
							<div id="additional_lease_existing_file_wrapper" class="hidden">
								<?php echo __('Additional File To Provide With Lease Purchase:', 'audio-merchant'); ?>
								<span class="existing_file_scroller">
									<?php
									$existingAudioFiles = array();

									if(file_exists($uploadDir))
									{
										foreach(glob($uploadDir.DIRECTORY_SEPARATOR.'*') as $file) 
										{
											$existingAudioFiles[] = $file;
										}
									}

									if(!empty($existingAudioFiles))
									{
										$supportedAudioExtensionsList = implode('|', $supportedAudioExtensions);
										$supportedImageTypesList = implode('|', $supportedImageTypes);

										foreach($existingAudioFiles as $key => $existingAudioFile)
										{
											$filename = basename($existingAudioFile);
											
											if('index.html' == $filename)
											{
												continue;
											}
											
											if(preg_match('@\.('.$supportedAudioExtensionsList.')$@i', $filename))
											{
												$iconSrc = audio_merchant_make_url_protocol_less(plugins_url('images/audio_icon.png', __FILE__));
											}
											elseif(preg_match('@\.('.$supportedImageTypesList.')$@i', $filename))
											{
												$iconSrc = audio_merchant_make_url_protocol_less($uploadUrl.'/'.$filename);
											}
											else
											{
												$iconSrc = audio_merchant_make_url_protocol_less(plugins_url('images/file_icon.png', __FILE__));
											}

											echo '<span><input type="radio" name="additional_lease_existing_file" id="additional_lease_existing_file_'.$key.'" value="'.$filename.'" /><label for="additional_lease_existing_file_'.$key.'"><img src="'.audio_merchant_make_url_protocol_less($iconSrc).'" width="50" height="50" border="0" alt="" /><br /><a href="'.$uploadUrl.'/'.$filename.'" target="_blank" title="'.$filename.'">'.substr($filename, 0, 9).'...</a></label></span>';
										}
									}
									else 
									{
										echo '<span class="warning_msg">'.__('There are currently no files uploaded!', 'audio-merchant').'</span>';
									}
									?>
								</span> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This file is provided to buyers AFTER they purchase a lease license. This is a great place to provide additional license files (.pdf), or any other file that may relate to your audio file. Accepts any file format. If you wish to provide multiple files, please archive (.zip, .rar, etc.) them up into one file first, then upload that file here.', 'audio-merchant'); ?>" />
							</div>
							<div class="small_url_container">
								<input type="hidden" class="upload_mode" name="addtional_file_lease_mode" value="upload" />
								<a id="additional_lease_upload_link" class="hidden" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('upload', document.getElementById('additional_lease_upload_link'), 'additional_lease_file_wrapper');"><?php echo __('Upload File', 'audio-merchant'); ?></a> <a id="additional_lease_url_link" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('url', document.getElementById('additional_lease_url_link'), 'additional_lease_url_file_wrapper');"><?php echo __('Specify URL', 'audio-merchant'); ?></a> <a id="additional_lease_existing_link" class="last_small_url" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('existing', document.getElementById('additional_lease_existing_link'), 'additional_lease_existing_file_wrapper');"><?php echo __('Select Existing File', 'audio-merchant'); ?></a>
							</div>
							<div class="clear"></div>
						</div>

						<div class="upload_file_wrapper">
							<div id="additional_exclusive_file_wrapper">
								<label for="additional_exclusive_file"><?php echo __('Additional File To Provide With Exclusive Purchase:', 'audio-merchant'); ?></label>
								<input type="file" name="additional_exclusive_file" id="additional_exclusive_file" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This file is provided to buyers AFTER they purchase an exclusive license. This is a great place to provide tracked out wav files or any additional license files (.pdf) that may relate to your audio file. Accepts any file format. If you wish to provide multiple files, please archive (.zip, .rar, etc.) them up into one file first, then upload that file here.', 'audio-merchant'); ?>" />
							</div>
							<div id="additional_exclusive_url_file_wrapper" class="hidden">
								<label for="additional_exclusive_url_file"><?php echo __('Additional File To Provide With Exclusive Purchase:', 'audio-merchant'); ?></label>
								<input type="text" name="additional_exclusive_url_file" id="additional_exclusive_url_file" value="" placeholder="http://externalserver.com/my_additional_exclusive_file.zip" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This file is provided to buyers AFTER they purchase an exclusive license. This is a great place to provide tracked out wav files or any additional license files (.pdf) that may relate to your audio file. Accepts any file format. If you wish to provide multiple files, please archive (.zip, .rar, etc.) them up into one file first, then upload that file here.', 'audio-merchant'); ?>" />
							</div>
							<div id="additional_exclusive_existing_file_wrapper" class="hidden">
								<?php echo __('Additional File To Provide With Exclusive Purchase:', 'audio-merchant'); ?>
								<span class="existing_file_scroller">
									<?php
									$existingAudioFiles = array();

									if(file_exists($uploadDir))
									{
										foreach(glob($uploadDir.DIRECTORY_SEPARATOR.'*') as $file) 
										{
											$existingAudioFiles[] = $file;
										}
									}

									if(!empty($existingAudioFiles))
									{
										$supportedAudioExtensionsList = implode('|', $supportedAudioExtensions);
										$supportedImageTypesList = implode('|', $supportedImageTypes);

										foreach($existingAudioFiles as $key => $existingAudioFile)
										{
											$filename = basename($existingAudioFile);
											
											if('index.html' == $filename)
											{
												continue;
											}
											
											if(preg_match('@\.('.$supportedAudioExtensionsList.')$@i', $filename))
											{
												$iconSrc = audio_merchant_make_url_protocol_less(plugins_url('images/audio_icon.png', __FILE__));
											}
											elseif(preg_match('@\.('.$supportedImageTypesList.')$@i', $filename))
											{
												$iconSrc = audio_merchant_make_url_protocol_less($uploadUrl.'/'.$filename);
											}
											else
											{
												$iconSrc = audio_merchant_make_url_protocol_less(plugins_url('images/file_icon.png', __FILE__));
											}

											echo '<span><input type="radio" name="additional_exclusive_existing_file" id="additional_exclusive_existing_file_'.$key.'" value="'.$filename.'" /><label for="additional_exclusive_existing_file_'.$key.'"><img src="'.audio_merchant_make_url_protocol_less($iconSrc).'" width="50" height="50" border="0" alt="" /><br /><a href="'.$uploadUrl.'/'.$filename.'" target="_blank" title="'.$filename.'">'.substr($filename, 0, 9).'...</a></label></span>';
										}
									}
									else 
									{
										echo '<span class="warning_msg">'.__('There are currently no files uploaded!', 'audio-merchant').'</span>';
									}
									?>
								</span> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This file is provided to buyers AFTER they purchase an exclusive license. This is a great place to provide tracked out wav files or any additional license files (.pdf) that may relate to your audio file. Accepts any file format. If you wish to provide multiple files, please archive (.zip, .rar, etc.) them up into one file first, then upload that file here.', 'audio-merchant'); ?>" />
							</div>
							<div class="small_url_container">
								<input type="hidden" class="upload_mode" name="addtional_file_exclusive_mode" value="upload" />
								<a id="additional_exclusive_upload_link" class="hidden" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('upload', document.getElementById('additional_exclusive_upload_link'), 'additional_exclusive_file_wrapper');"><?php echo __('Upload File', 'audio-merchant'); ?></a> <a id="additional_exclusive_url_link" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('url', document.getElementById('additional_exclusive_url_link'), 'additional_exclusive_url_file_wrapper');"><?php echo __('Specify URL', 'audio-merchant'); ?></a> <a id="additional_exclusive_existing_link" class="last_small_url" href="javascript: void(0);" onclick="javascript: return toggle_upload_field('existing', document.getElementById('additional_exclusive_existing_link'), 'additional_exclusive_existing_file_wrapper');"><?php echo __('Select Existing File', 'audio-merchant'); ?></a>
							</div>
							<div class="clear"></div>
						</div>
						<input type="hidden" name="editing_audio_id" id="editing_audio_id" value="" />
						<!-- Allow form submission with keyboard without duplicating the dialog button -->
						<input type="submit" tabindex="-1" class="default_submit_button" />
					</fieldset>
				</form>
			</div>
		</div>
		<div id="html_player_tab">
			<button name="create_html_player_widget_btn" id="create_html_player_widget_btn"><?php echo __('Add Playlist', 'audio-merchant'); ?></button>
			<br /><br />
			<table id="html_player_table" class="compact hover cell-border stripe" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><?php echo __('Playlist ID', 'audio-merchant'); ?></th>
						<th><?php echo __('Playlist Name', 'audio-merchant'); ?></th>
						<th><?php echo __('Mode', 'audio-merchant'); ?></th>
						<th><?php echo __('Filter', 'audio-merchant'); ?></th>
						<th><?php echo __('Order By Field', 'audio-merchant'); ?></th>
						<th><?php echo __('Order By Direction', 'audio-merchant'); ?></th>
						<th><?php echo __('Date Created', 'audio-merchant'); ?></th>
						<th><?php echo __('Last Modified', 'audio-merchant'); ?></th>
						<th><?php echo __('Options', 'audio-merchant'); ?></th>
					</tr>
				</thead>
			</table>
			<div id="html_widget_dlg" title="<?php echo __('Add Playlist', 'audio-merchant'); ?>">
				<form id="html_widget_form" method="post" onsubmit="javascript: return audioMerchantSaveHTMLPlayer();">
					<fieldset>
						<label for="playlist_name"><?php echo __('Playlist Name:', 'audio-merchant'); ?></label>
						<input type="text" name="playlist_name" id="playlist_name" value="" maxlength="190" placeholder="<?php echo __('Example Format: My Slow Jams', 'audio-merchant'); ?>" class="text ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('A descriptive name for you to remember what this playlist is about. This name is used for administrative purposes only, it is never displayed to the frontend user/listener.', 'audio-merchant'); ?>" />
						<br />
						<br />
						
						<input onchange="javascript: toggleCreateHTMLPlayerMode();" type="radio" name="player_mode" id="player_mode_all" value="all" class="text2 ui-corner-all" checked="checked" /><label for="player_mode_all"><?php echo __('Display All Audio', 'audio-merchant'); ?></label> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This option will display all the audio files available in the system for your listener.', 'audio-merchant'); ?>" />
						<br /><br />
						<input onchange="javascript: toggleCreateHTMLPlayerMode();" type="radio" name="player_mode" id="player_mode_selected" value="selected" class="text2 ui-corner-all" /><label for="player_mode_selected"><?php echo __('Display Selected Audio', 'audio-merchant'); ?></label> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This option will display the below selected tracks in the frontend player for your listener. This list is sortable.', 'audio-merchant'); ?>" />
						<span class="select_all_container"><a class="unselect_all" href="javascript: void(0);" onclick="javascript: jQuery('input[name=\'player_selected_audio_ids[]\']').prop('checked', false); jQuery(this).parent().find('.select_all').css('display', 'inline-block'); jQuery(this).css('display', 'none');"><?php _e('- Unselect All -', 'audio-merchant'); ?></a><a class="select_all" href="javascript: void(0);" onclick="javascript: jQuery('#player_mode_selected').prop('checked', true); toggleCreateHTMLPlayerMode(); jQuery('input[name=\'player_selected_audio_ids[]\']').prop('checked', true); jQuery(this).parent().find('.unselect_all').css('display', 'inline-block'); jQuery(this).css('display', 'none');"><?php _e('- Select All -', 'audio-merchant'); ?></a></span>
						<ol class="vertical_audio_scroller">
							<?php
							if(!empty($audioFiles['data']))
							{
								foreach($audioFiles['data'] as $arrKey => $audioFile)
								{
									if(preg_match('@^https?://@i', $audioFile[4]))
									{
										$coverPhotoUrl = $audioFile[4];
									}
									else
									{
										$coverPhotoUrl = $uploadUrl.'/'.$audioFile[4];
									}
									
									if(preg_match('@^https?://@i', $audioFile[5]))
									{
										$fileUrl = $audioFile[5];
									}
									else
									{
										$fileUrl = $uploadUrl.'/'.$audioFile[5];
									}
									
									if(!empty($audioFile[4]))
									{
										$coverPhoto = '<img src="'.audio_merchant_make_url_protocol_less($coverPhotoUrl).'" width="50" height="50" border="0" alt="" />';
									}
									else
									{
										$coverPhoto = '';
									}
									
									echo '<li><input type="checkbox" name="player_selected_audio_ids[]" id="player_selected_audio_ids_'.$audioFile[0].'" value="'.$audioFile[0].'" disabled="disabled" /> <label for="player_selected_audio_ids_'.$audioFile[0].'">'.$coverPhoto.' <a href="'.$fileUrl.'" target="_blank">'.$audioFile[1].'</a></label></li>';
								}
							}
							else 
							{
								echo '<li class="warning_msg">'.__('There are currently no files uploaded!', 'audio-merchant').'</li>';
							}
							?>
						</ol>
						<span class="small_grey"><?php _e('^ Sortable List', 'audio-merchant'); ?></span>
						<br /><br />
						<input type="radio" onchange="javascript: toggleCreateHTMLPlayerMode();" name="player_mode" id="player_mode_text_match" value="text_match" class="text2 ui-corner-all" /><label for="player_mode_text_match"><?php echo __('Display Audio Matching Text', 'audio-merchant'); ?></label> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('This option will display all the tracks that match the text defined below.', 'audio-merchant'); ?>" />
						<br />
						<blockquote>
							<label for="player_mode_text_value"><?php echo __('Match String:', 'audio-merchant'); ?></label> <input type="text" name="player_mode_text_value" id="player_mode_text_value" value="" size="25" maxlength="190" placeholder="<?php echo __('Artist - Album - Song Name', 'audio-merchant'); ?>" class="text2 ui-widget-content ui-corner-all" disabled="disabled" />
						</blockquote>
						<br />
						<label for="player_display_order"><?php echo __('Display Order:', 'audio-merchant'); ?></label> <select name="player_display_order" id="player_display_order" class="text2 ui-corner-all">
						<option value="1"><?php echo __('By Display Name', 'audio-merchant'); ?></option>
						<option value="2"><?php echo __('By Lease Price', 'audio-merchant'); ?></option>
						<option value="3"><?php echo __('By Exclusive Price', 'audio-merchant'); ?></option>
						<option value="4"><?php echo __('By Duration', 'audio-merchant'); ?></option>
						<option value="5"><?php echo __('By Date Created', 'audio-merchant'); ?></option>
						<option value="6"><?php echo __('By Date Modified', 'audio-merchant'); ?></option>
						</select><select name="player_display_order_direction" id="player_display_order_direction" class="text2 ui-corner-all">
						<option value="ASC"><?php echo __('Ascending', 'audio-merchant'); ?></option>
						<option value="DESC"><?php echo __('Descending', 'audio-merchant'); ?></option>
						</select> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The order in which the playlist tracks will display in.', 'audio-merchant'); ?>" />
						
						<input type="hidden" id="player_id" name="player_id" value="" />
						<input type="submit" tabindex="-1" class="default_submit_button" />
					</fieldset>
				</form>
			</div>
		</div>
		<div id="orders_tab">
			<table id="orders_table" class="compact hover cell-border stripe" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><?php echo __('Order ID', 'audio-merchant'); ?></th>
						<th><?php echo __('User ID', 'audio-merchant'); ?></th>
						<th><?php echo __('Payment Transaction ID', 'audio-merchant'); ?></th>
						<th><?php echo __('Customer Name', 'audio-merchant'); ?></th>
						<th><?php echo __('Customer Email', 'audio-merchant'); ?></th>
						<th><?php echo __('Payment Status', 'audio-merchant'); ?></th>
						<th><?php echo __('Grand Total', 'audio-merchant'); ?></th>
						<th><?php echo __('Audio ID', 'audio-merchant'); ?></th>
						<th><?php echo __('Order Item', 'audio-merchant'); ?></th>
						<th><?php echo __('License Type', 'audio-merchant'); ?></th>
						<th><?php echo __('Date Created', 'audio-merchant'); ?></th>
						<th><?php echo __('Last Modified', 'audio-merchant'); ?></th>
						<th><?php echo __('Options', 'audio-merchant'); ?></th>
					</tr>
				</thead>
			</table>
		</div>
		<div id="settings_tab">
			<p>
				<form id="settings_form" method="post" onsubmit="javascript: return audioMerchantSaveSettings();">
					<label for="paypal_email"><?php echo __('Paypal Email:', 'audio-merchant'); ?></label> <?php echo __('Selling feature is only available in Pro version of this plugin. <a href="http://www.myaudiomerchant.com/#download" target="_blank">Click here</a> to upgrade &gt;&gt;', 'audio-merchant'); ?> <input type="hidden" name="paypal_email" id="paypal_email" value="" placeholder="you@youremail.com" size="20" class="text2 ui-widget-content ui-corner-all" /> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The PayPal email address that should receive payments. This should be the same as your primary email address in your PayPal account. If no PayPal email address is supplied, then selling functionality is disabled in the frontend HTML5 audio player, and you will only be showcasing your audio files at that point.', 'audio-merchant'); ?>" />
					<br />
					<label for="audio_merchant_currency"><?php echo __('Currency:', 'audio-merchant'); ?></label> <select name="audio_merchant_currency" id="audio_merchant_currency" class="text2 ui-corner-all">
						<option value="AUD"<?php if('AUD' == $currency) { ?> selected="yes"<?php } ?>>Australian Dollar</option>
						<option value="BRL"<?php if('BRL' == $currency) { ?> selected="yes"<?php } ?>>Brazilian Real </option>
						<option value="CAD"<?php if('CAD' == $currency) { ?> selected="yes"<?php } ?>>Canadian Dollar</option>
						<option value="CZK"<?php if('CZK' == $currency) { ?> selected="yes"<?php } ?>>Czech Koruna</option>
						<option value="DKK"<?php if('DKK' == $currency) { ?> selected="yes"<?php } ?>>Danish Krone</option>
						<option value="EUR"<?php if('EUR' == $currency) { ?> selected="yes"<?php } ?>>Euro</option>
						<option value="HKD"<?php if('HKD' == $currency) { ?> selected="yes"<?php } ?>>Hong Kong Dollar</option>
						<option value="HUF"<?php if('HUF' == $currency) { ?> selected="yes"<?php } ?>>Hungarian Forint </option>
						<option value="ILS"<?php if('ILS' == $currency) { ?> selected="yes"<?php } ?>>Israeli New Sheqel</option>
						<option value="JPY"<?php if('JPY' == $currency) { ?> selected="yes"<?php } ?>>Japanese Yen</option>
						<option value="MYR"<?php if('MYR' == $currency) { ?> selected="yes"<?php } ?>>Malaysian Ringgit</option>
						<option value="MXN"<?php if('MXN' == $currency) { ?> selected="yes"<?php } ?>>Mexican Peso</option>
						<option value="NOK"<?php if('NOK' == $currency) { ?> selected="yes"<?php } ?>>Norwegian Krone</option>
						<option value="NZD"<?php if('NZD' == $currency) { ?> selected="yes"<?php } ?>>New Zealand Dollar</option>
						<option value="PHP"<?php if('PHP' == $currency) { ?> selected="yes"<?php } ?>>Philippine Peso</option>
						<option value="PLN"<?php if('PLN' == $currency) { ?> selected="yes"<?php } ?>>Polish Zloty</option>
						<option value="GBP"<?php if('GBP' == $currency) { ?> selected="yes"<?php } ?>>Pound Sterling</option>
						<option value="SGD"<?php if('SGD' == $currency) { ?> selected="yes"<?php } ?>>Singapore Dollar</option>
						<option value="SEK"<?php if('SEK' == $currency) { ?> selected="yes"<?php } ?>>Swedish Krona</option>
						<option value="CHF"<?php if('CHF' == $currency) { ?> selected="yes"<?php } ?>>Swiss Franc</option>
						<option value="TWD"<?php if('TWD' == $currency) { ?> selected="yes"<?php } ?>>Taiwan New Dollar</option>
						<option value="THB"<?php if('THB' == $currency) { ?> selected="yes"<?php } ?>>Thai Baht</option>
						<option value="TRY"<?php if('TRY' == $currency) { ?> selected="yes"<?php } ?>>Turkish Lira</option>
						<option value="USD"<?php if('USD' == $currency) { ?> selected="yes"<?php } ?>>U.S. Dollar</option>
					</select> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The currency that should be used for all sales.', 'audio-merchant'); ?>" />
					
					
					<br />
					<label for="temp_download_link_expiration"><?php echo __('Download Links Expire:', 'audio-merchant'); ?></label> <select name="temp_download_link_expiration" id="temp_download_link_expiration" class="text2 ui-corner-all">
						<?php for ($a = 1; $a <= 365; $a++) { ?>
						<option value="<?php echo $a; ?>"<?php if($tempLinkExpirationsDays == $a) { ?> selected="yes"<?php } ?>><?php 
						if($a <= 1)
						{
							echo $a.' '.__('Day', 'audio-merchant'); 
						}
						else
						{
							echo $a.' '.__('Days', 'audio-merchant');
						}
						?></option>
						<?php } ?>
						<option value="0"<?php if($tempLinkExpirationsDays == 0) { ?> selected="yes"<?php } ?>><?php echo __('Never', 'audio-merchant'); ?></option>
					</select> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The number of days the download links for an order should remain valid until they expire and are no longer valid.', 'audio-merchant'); ?>" />
					
					<br />
					<label for="email_admin_order_notices"><?php echo __('Email Admin Order Notifications:', 'audio-merchant'); ?></label> <select name="email_admin_order_notices" id="email_admin_order_notices" class="text2 ui-corner-all">
						<option value="1"<?php if((int)audio_merchant_get_setting('email_admin_order_notices') == 1) { ?> selected="yes"<?php } ?>><?php echo __('Yes', 'audio-merchant'); ?></option>
						<option value="0"<?php if((int)audio_merchant_get_setting('email_admin_order_notices') == 0) { ?> selected="yes"<?php } ?>><?php echo __('No', 'audio-merchant'); ?></option>
					</select> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('Email the admin a copy of the receipt email sent to customer after a purchase is completed.', 'audio-merchant'); ?>" />
					
					
					<br />
					<label for="purchase_user_login_required"><?php echo __('Require User Registration For Purchases:', 'audio-merchant'); ?></label> <select name="purchase_user_login_required" id="purchase_user_login_required" class="text2 ui-corner-all">
						<option value="1"<?php if($purchaseRequiresRegistration == 1) { ?> selected="yes"<?php } ?>><?php echo __('Yes', 'audio-merchant'); ?></option>
						<option value="0"<?php if($purchaseRequiresRegistration == 0) { ?> selected="yes"<?php } ?>><?php echo __('No', 'audio-merchant'); ?></option>
					</select> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('Require website login or registration in order to purchase anything from the frontend player.', 'audio-merchant'); ?>" />
					
					
					<br />
					<label for="download_user_login_required"><?php echo __('Require User Registration For Downloads:', 'audio-merchant'); ?></label> <select name="download_user_login_required" id="download_user_login_required" class="text2 ui-corner-all">
						<option value="1"<?php if($downloadRequiresRegistration == 1) { ?> selected="yes"<?php } ?>><?php echo __('Yes', 'audio-merchant'); ?></option>
						<option value="0"<?php if($downloadRequiresRegistration == 0) { ?> selected="yes"<?php } ?>><?php echo __('No', 'audio-merchant'); ?></option>
					</select> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('Require website login or registration in order to download FREE items from the frontend player.', 'audio-merchant'); ?>" />
					
					
					<br />
					<label for="exclusive_removed"><?php echo __('Exclusively Sold Items Are Removed From Player:', 'audio-merchant'); ?></label> <select name="exclusive_removed" id="exclusive_removed" class="text2 ui-corner-all">
						<option value="1"<?php if((int)audio_merchant_get_setting('exclusive_removed') == 1) { ?> selected="yes"<?php } ?>><?php echo __('Yes', 'audio-merchant'); ?></option>
						<option value="0"<?php if((int)audio_merchant_get_setting('exclusive_removed') == 0) { ?> selected="yes"<?php } ?>><?php echo __('No', 'audio-merchant'); ?></option>
					</select> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('Remove exclusively purchased items from the frontend player or leave them and display a SOLD text next to them instead.', 'audio-merchant'); ?>" />
					
					<br />
					<label for="show_author_link"><?php echo __('Show author credits:', 'audio-merchant'); ?></label> <select name="show_author_link" id="show_author_link" class="text2 ui-corner-all">
						<option value="1"<?php if((int)audio_merchant_get_setting('show_author_link') == 1) { ?> selected="yes"<?php } ?>><?php echo __('Yes', 'audio-merchant'); ?></option>
						<option value="0"<?php if((int)audio_merchant_get_setting('show_author_link') == 0) { ?> selected="yes"<?php } ?>><?php echo __('No', 'audio-merchant'); ?></option>
					</select> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('Give credit back to the plugin author. (Only in Lite version)', 'audio-merchant'); ?>" />
					
					
					<br />
					<label id="css_frontend_label" for="css_frontend"><?php echo __('Frontend HTML5 Audio Player CSS Styles:', 'audio-merchant'); ?></label> <textarea id="css_frontend" name="css_frontend"><?php echo file_get_contents(plugin_dir_path( __FILE__ ).'audio-merchant-html-player-frontend.css'); ?></textarea> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('CSS Styles for frontend HTML5 audio player. Use this to customize the frontend look and feel.', 'audio-merchant'); ?>" />
					<br />
					<label></label> <a class="use_default_link" href="javascript: void(0);" onclick="javascript: loadDefaultCSS();"><?php echo __('- Use Default CSS -', 'audio-merchant'); ?></a>
					
					
					<br /><br />
					<label><?php echo __('php.ini upload_max_filesize:', 'audio-merchant'); ?></label> <?php echo ini_get('upload_max_filesize'); ?> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The maximum file size of EACH FILE that can be uploaded to your webserver. This is the maximum file size PER individual file and not the total filesize of all files uploaded at any given time.', 'audio-merchant'); ?>" />
					<br />
					<label><?php echo __('php.ini post_max_size:', 'audio-merchant'); ?></label> <?php echo ini_get('post_max_size'); ?> <img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/question_mark.png', __FILE__)); ?>" width="15" height="15" align="absmiddle" border="0" alt="" title="<?php echo __('The maximum size of a single post which may include multiple file uploads. This is the total number of all uploads at a time. Please increase to a sufficient size that allows uploading of your files to your web server.', 'audio-merchant'); ?>" />
					
					<br /><br />
					<label for="save_settings_btn">&nbsp;</label> <button type="submit" name="save_settings_btn" id="save_settings_btn"><?php echo __('Save', 'audio-merchant'); ?></button>
				</form>
			</p>
		</div>
	</div>
	<div id="error_msg_wrapper" title="<?php echo __('Error', 'audio-merchant'); ?>"><p></p></div>
	<div id="success_msg_wrapper" title="<?php echo __('Success', 'audio-merchant'); ?>"><p></p></div>
	<div id="success_msg_wrapper2" title="<?php echo __('Success', 'audio-merchant'); ?>"><p></p></div>
	<div id="share_dialog" title="<?php echo __('Share', 'audio-merchant'); ?>">
		<p>
			<textarea id="share_dialog_content"></textarea>
			<br />
			<a id="copy_share_code_to_clipboard" data-clipboard-text="" onclick="javascript: alert('<?php echo __('Copied to clipboard!', 'audio-merchant'); ?>');" href="javascript: void(0);" class="float_right" title="<?php echo __('Copy to Clipboard', 'audio-merchant'); ?>"><img src="<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/copy_icon.png', __FILE__)); ?>" width="20" alt="" border="0" /></a>
			<select id="share_dialog_mode" name="share_dialog_mode" onchange="javascript: updateShareDialog();">
				<option value="wp"><?php echo __('WordPress ShortCode', 'audio-merchant'); ?></option>
				<option value="link" disabled="disabled"><?php echo __('Link (Only available in Pro version)', 'audio-merchant'); ?></option>
				<option value="iframe" disabled="disabled"><?php echo __('IFrame (Only available in Pro version)', 'audio-merchant'); ?></option>
			</select>
		</p>
	</div>
	<br />
	<div style="text-align: center;"><?php echo __('You are currently using the free version of this plugin, selling functionality is not available in this version. Please <a href="http://www.myaudiomerchant.com/#download" target="_blank">click here</a> to upgrade for a $25 one-time fee &gt;&gt;', 'audio-merchant'); ?></div>
</div>
<script>
	uploadBaseUrl = '<?php echo audio_merchant_make_url_protocol_less($uploadUrl); ?>';
	theFollowingErrorOccurredText = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('The following error(s) have occurred:', 'audio-merchant'), ENT_QUOTES)); ?>';
	unknownErrorOccurredTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('An unknown error occurred, please try your request again.', 'audio-merchant'), ENT_QUOTES)); ?>';
	urlImgBase = '<?php echo audio_merchant_make_url_protocol_less(plugins_url('images/', __FILE__)); ?>';
	successWndRefreshMsg = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Success! Please click okay to refresh the window with your latest changes...', 'audio-merchant'), ENT_QUOTES)); ?>';
	editItemTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Edit', 'audio-merchant'), ENT_QUOTES)); ?>';
	deleteItemTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Delete', 'audio-merchant'), ENT_QUOTES)); ?>';
	confirmDeleteMsg = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Are you sure you want to delete this item?', 'audio-merchant'), ENT_QUOTES)); ?>';
	successDeleteMsg = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Successfully deleted item!', 'audio-merchant'), ENT_QUOTES)); ?>';
	newAudioWndTitle = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Add Audio', 'audio-merchant'), ENT_QUOTES)); ?>';
	saveSuccessMsg = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Your latest changes were saved successfully!', 'audio-merchant'), ENT_QUOTES)); ?>';
	displayAllTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Display All', 'audio-merchant'), ENT_QUOTES)); ?>';
	selectedTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Selected', 'audio-merchant'), ENT_QUOTES)); ?>';
	textMatchTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Text Match', 'audio-merchant'), ENT_QUOTES)); ?>';
	ascendingTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Ascending', 'audio-merchant'), ENT_QUOTES)); ?>';
	descendingTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Descending', 'audio-merchant'), ENT_QUOTES)); ?>';
	filesSelected = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Files Selected', 'audio-merchant'), ENT_QUOTES)); ?>';
	fileSelected = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('File Selected', 'audio-merchant'), ENT_QUOTES)); ?>';
	createHTMLWidgetTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Add Playlist', 'audio-merchant'), ENT_QUOTES)); ?>';
	viewTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('View', 'audio-merchant'), ENT_QUOTES)); ?>';
	receiptUrl = '<?php echo admin_url('admin-ajax.php?action=audio_merchant_checkout_complete&amp;t='); ?>';
	confirmChangeStatusTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Are you sure you want to change the status for this order?', 'audio-merchant'), ENT_QUOTES)); ?>';
	shareItemtxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Share', 'audio-merchant'), ENT_QUOTES)); ?>';
	loadingTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('Loading...', 'audio-merchant'), ENT_QUOTES)); ?>';
	emptyAudioTxt = '<?php echo preg_replace('@[\r\n\t]+@', '', htmlentities(__('There are currently no audio files to show with the specified criteria.', 'audio-merchant'), ENT_QUOTES)); ?>';
	savingPleaseWaitxt = "<?php echo preg_replace('@[\r\n\t]+@', '', __('<img src=\''.audio_merchant_make_url_protocol_less(plugins_url('images/ajax-loader.gif', __FILE__)).'\' style=\'vertical-align: middle;\' border=\'0\' alt=\'\' align=\'middle\' width=\'16\' height=\'16\' /> Saving, please wait...', 'audio-merchant')); ?>";
</script>