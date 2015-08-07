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

// Create Base64 Object
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

jQuery(document).ready(function() {
    audioMerchantPlayer.init();
});

var audioMerchantPlayer = {
	coverPhotoWidthHeight: 1,
	controlsWidth: 1,
	playerControlsMaxWidthHeight: 200,
	playerControlsMinWidthHeight: 50,
	imgPathUrl: '',
	currentlyPlaying: 0,
	currentLoadedSong: null,
	
	init: function () {
		jQuery('.audio_merchant_player').css('min-width', String(jQuery('.audio_merchant_player').width())+'px');
		jQuery('.audio_merchant_player').css('min-height', String(jQuery('.audio_merchant_player').height())+'px');
		jQuery('.audio_merchant_player').css('width', String(jQuery('.audio_merchant_player').width())+'px');
		jQuery('.audio_merchant_player').css('height', String(jQuery('.audio_merchant_player').height())+'px');
		
		audioMerchantPlayer.coverPhotoWidthHeight = Number(jQuery('.audio_merchant_player').width()*0.30).toFixed();
		
		if (audioMerchantPlayer.coverPhotoWidthHeight > (jQuery('.audio_merchant_player').height()*0.5)) {
			audioMerchantPlayer.coverPhotoWidthHeight = Number(jQuery('.audio_merchant_player').height()*0.5).toFixed();
		}
		
		if (audioMerchantPlayer.coverPhotoWidthHeight < audioMerchantPlayer.playerControlsMinWidthHeight) {
			audioMerchantPlayer.coverPhotoWidthHeight = audioMerchantPlayer.playerControlsMinWidthHeight;
		} else if (audioMerchantPlayer.coverPhotoWidthHeight > audioMerchantPlayer.playerControlsMaxWidthHeight) {
			audioMerchantPlayer.coverPhotoWidthHeight = audioMerchantPlayer.playerControlsMaxWidthHeight;
		}
		
		jQuery('.audio_cover_photo').width(audioMerchantPlayer.coverPhotoWidthHeight);
		jQuery('.audio_cover_photo').height(audioMerchantPlayer.coverPhotoWidthHeight);
		
		audioMerchantPlayer.controlsWidth = Number(jQuery('.audio_merchant_player').width()-audioMerchantPlayer.coverPhotoWidthHeight).toFixed()-2;
		
		jQuery('.player_controls').width(audioMerchantPlayer.controlsWidth);
		jQuery('.player_controls').height(audioMerchantPlayer.coverPhotoWidthHeight);
		
		jQuery('.player_buttons').width(audioMerchantPlayer.coverPhotoWidthHeight*0.6);
		jQuery('.time_slider').width(audioMerchantPlayer.controlsWidth*0.9);
		jQuery('.time_slider').css('top', audioMerchantPlayer.coverPhotoWidthHeight-20);
		
		jQuery('ol.track_list').height(jQuery('.audio_merchant_player').height()-audioMerchantPlayer.coverPhotoWidthHeight);
		
		jQuery('audio.audio_player')[0].volume = 1;
		
		jQuery('div.time_slider').slider({
			range: 'min',
			change: function () {
				jQuery('div.time_slider').attr('data-title', audioMerchantPlayer.convertSecondsToMinutes(jQuery('audio.audio_player')[0].currentTime));
				
				jQuery('div.ui-tooltip .ui-tooltip-content').each(function (a, b) {
					if (jQuery(b).text().indexOf(':') > -1) {
						jQuery(b).text(audioMerchantPlayer.convertSecondsToMinutes(jQuery('audio.audio_player')[0].currentTime));
					}
				});
			},
			slide: function (theEvent, ui) {
				jQuery('audio.audio_player')[0].currentTime = ui.value;
				
				jQuery('div.time_slider').attr('data-title', audioMerchantPlayer.convertSecondsToMinutes(jQuery('audio.audio_player')[0].currentTime));
				
				jQuery('div.ui-tooltip .ui-tooltip-content').each(function (a, b) {
					if (jQuery(b).text().indexOf(':') > -1) {
						jQuery(b).text(audioMerchantPlayer.convertSecondsToMinutes(jQuery('audio.audio_player')[0].currentTime));
					}
				});
			}
		});
		
		jQuery('audio.audio_player')[0].ontimeupdate = function (theEvent) {
			jQuery('div.time_slider').slider("value", jQuery('audio.audio_player')[0].currentTime);
		};
		
		jQuery('audio.audio_player')[0].onended = function (theEvent) {
			if (audioMerchantPlayer.currentlyPlaying > 0) {
				jQuery('img.play_'+String(audioMerchantPlayer.currentLoadedSong)).attr('src', audioMerchantPlayer.imgPathUrl+'play_button_small.png');
				jQuery('img.play_button').attr('src', audioMerchantPlayer.imgPathUrl+'play_button.png');
				jQuery('div.time_slider').slider("value", 0);

				var nextSong = jQuery('img.play_'+String(audioMerchantPlayer.currentLoadedSong)).parent().parent().parent().next('li');

				if (nextSong.length > 0) {
					nextSong.find('a.play_button_small_link').trigger("click");
				} else {
					audioMerchantPlayer.currentlyPlaying = 0;

					jQuery('.track_list a.play_button_small_link:first').trigger("click");
					jQuery('.track_list a.play_button_small_link:first').trigger("click");
				}
			}
		};
		
		jQuery(document).tooltip({
			track: true,
			hide: {effect: 'fade', duration: 1},
			content: function () {
				if (jQuery(this).attr('data-title')) {
					return jQuery(this).attr('data-title');
				} else {
					return jQuery(this).attr('title');
				}
			}
		});
	},
	
	play: function (audioId, audioDisplayName, duration, coverPhotoUrl, playFileUrl, startPlaying) {
		if (audioId == audioMerchantPlayer.currentlyPlaying) {
			audioMerchantPlayer.currentlyPlaying = 0;
			
			jQuery('audio.audio_player')[0].pause();
			
			jQuery('img.play_'+String(audioId)).attr('src', audioMerchantPlayer.imgPathUrl+'play_button_small.png');
			jQuery('img.play_button').attr('src', audioMerchantPlayer.imgPathUrl+'play_button.png');
		} else {
			if (audioMerchantPlayer.currentLoadedSong != audioId) {
				audioMerchantPlayer.currentLoadedSong = audioId;
				
				jQuery('div.time_slider').slider("value", 0);
				
				audioDisplayName = Base64.decode(audioDisplayName);
				
				jQuery('.audio_title').html(audioDisplayName);
				
				if (coverPhotoUrl) {
					jQuery('img.audio_cover_photo_img').attr('src', coverPhotoUrl);
					jQuery('img.audio_cover_photo_img').attr('data-title', '<img src="'+coverPhotoUrl+'" alt="" border="0" />');
				} else {
					jQuery('img.audio_cover_photo_img').attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
					jQuery('img.audio_cover_photo_img').attr('data-title', '');
				}
				
				var updateSliderMaxFunc = function () {
					jQuery('div.time_slider').slider("option", "max", jQuery('audio.audio_player')[0].duration);
					
					jQuery('audio.audio_player').off('loadedmetadata', updateSliderMaxFunc);
				};
				
				jQuery('audio.audio_player').on('loadedmetadata', updateSliderMaxFunc);
				
				jQuery('audio.audio_player').attr('src', playFileUrl);
				jQuery('audio.audio_player')[0].load();
				
				var prevSong = jQuery('img.play_'+String(audioId)).parent().parent().parent().prev('li');
				var nextSong = jQuery('img.play_'+String(audioId)).parent().parent().parent().next('li');
				
				if (prevSong.length < 1) {
					prevSong = jQuery('.track_list li:first');
				}
				
				if (nextSong.length < 1) {
					nextSong = jQuery('.track_list li:first');
				}
				
				jQuery('a.big_play_button').attr('onclick', jQuery('img.play_'+String(audioId)).parent().attr('onclick'));
				jQuery('a.previous_button_link').attr('onclick', prevSong.find('a.play_button_small_link').attr('onclick').replace(':', ': audioMerchantPlayer.currentlyPlaying = 0; jQuery(\'audio.audio_player\')[0].currentTime = 0;'));
				jQuery('a.next_button_link').attr('onclick', nextSong.find('a.play_button_small_link').attr('onclick').replace(':', ': audioMerchantPlayer.currentlyPlaying = 0; jQuery(\'audio.audio_player\')[0].currentTime = 0;'));
				
				jQuery('.track_list li.current_audio_file_playing').removeClass('current_audio_file_playing');
				
				jQuery('.audio_'+String(audioId)+'_row').addClass('current_audio_file_playing');
			}
			
			if (startPlaying) {
				audioMerchantPlayer.currentlyPlaying = audioId;
				
				jQuery('img.play_button').attr('src', audioMerchantPlayer.imgPathUrl+'pause_button.png');
				jQuery('img.play_button_small').attr('src', audioMerchantPlayer.imgPathUrl+'play_button_small.png');
				jQuery('img.play_'+String(audioId)).attr('src', audioMerchantPlayer.imgPathUrl+'pause_button_small.png');
				
				jQuery('audio.audio_player')[0].play();
			}
		}
	},
	
	convertSecondsToMinutes: function (seconds) {
		seconds = Number(seconds).toFixed();
		
		var hours   = Math.floor(seconds / 3600);
		var minutes = Math.floor((seconds - (hours * 3600)) / 60);
		var seconds = seconds - (hours * 3600) - (minutes * 60);
		var time = "";

		if (hours != 0) {
		  time = String(hours)+":";
		}
		
		if (minutes != 0 || time !== "") {
		  minutes = (minutes < 10 && time !== "") ? "0"+String(minutes) : String(minutes);
		  time += String(minutes)+":";
		}

		if (time === "") {
		  time = '0:';

		  if (String(seconds).length < 2) {
			  time += '0';
		  }

		  time += String(seconds);
		} else {
		  time += (seconds < 10) ? "0"+String(seconds) : String(seconds);
		}

		return time;
	}
};