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

var newAudioWnd = null;
var htmlWidgetDlg = null;
var errorMessageWnd = null;
var successMessageWnd = null;
var successMessageWnd2 = null;
var uploadBaseUrl = null;
var theFollowingErrorOccurredText = 'The following error(s) have occurred:';
var unknownErrorOccurredTxt = 'An unknown error occurred, please try your request again.';
var urlImgBase = '';
var audioInventoryTable = null;
var htmlPlayerTable = null;
var successWndRefreshMsg = 'Success! Please click okay to refresh the window with your latest changes...';
var editItemTxt = 'Edit';
var deleteItemTxt = 'Delete';
var confirmDeleteMsg = 'Are you sure you want to delete this item?';
var successDeleteMsg = 'Successfully deleted item!';
var newAudioWndTitle = 'Add Audio';
var saveSuccessMsg = 'Your latest changes were saved successfully!';
var displayAllTxt = 'Display All';
var selectedTxt = 'Selected';
var textMatchTxt = 'Text Match';
var ascendingTxt = 'Ascending';
var descendingTxt = 'Descending';
var filesSelected = 'Files Selected';
var fileSelected = 'File Selected';
var createHTMLWidgetTxt = 'Add Playlist';
var amTooltips = null;
var ordersTables = null;
var viewTxt = 'View';
var receiptUrl = '';
var confirmChangeStatusTxt = 'Are you sure you want to change the status for this order?';
var shareItemtxt = 'Share';
var loadingTxt = 'Loading...';
var shareDialog = null;
var lastId = 0;
var lastShareMode = 'audio_id';
var emptyAudioTxt = 'There are currently no audio files to show with the specified criteria.';
var savingPleaseWaitxt = 'Saving, please wait...';

function toggle_upload_field(mode, toggleLinkClicked, showWrapper) {
	var toggleLinkParent = jQuery(toggleLinkClicked).parent();
	
	toggleLinkParent.find('a').removeClass('hidden').removeClass('last_small_url');
	jQuery(toggleLinkClicked).addClass('hidden');
	toggleLinkParent.find('a:visible:last').addClass('last_small_url');
	
	var mainParent = toggleLinkParent.parent();
	
	mainParent.find('div').removeClass('hidden').addClass('hidden');
	jQuery('#'+showWrapper).removeClass('hidden');
	jQuery(toggleLinkParent).removeClass('hidden');
	
	mainParent.find('.upload_mode').val(mode);
}

function addNewAudioFile()
{
	var dialogSaveBtn = jQuery('.new_audio_wnd .ui-button-text:contains(Save)');
	
	dialogSaveBtn.html(savingPleaseWaitxt);
	dialogSaveBtn.parent().button("disable");
	
	var actionUrl = ajaxurl.split('?')[0]+'?action=audio_merchant_add_audio_file';
	
	jQuery.ajax({
		url: actionUrl,
		type: 'POST',
		data: new FormData(jQuery('#new_audio_file_form')[0]),
		processData: false,
		contentType: false
    }).done(function (response) {
		var isError = false;
		var errorMsg = '<span class="error_msg">'+theFollowingErrorOccurredText+'<br /><blockquote>';
		
		if(response) {
			if(response.errors.length > 0) {
				isError = true;
				response.errors.forEach(function (theErrorMsg) {
					errorMsg += theErrorMsg+'<br />';
				});
			}
		} else {
			isError = true;
			errorMsg += unknownErrorOccurredTxt;
		}
		
		errorMsg += '</blockquote></span>';
		
		if(isError) {
			jQuery("#error_msg_wrapper p").html(errorMsg);
			
			errorMessageWnd.dialog("open");
			
			dialogSaveBtn.text('Save');
			dialogSaveBtn.parent().button("enable");
		} else {
			jQuery("#success_msg_wrapper p").html(successWndRefreshMsg);
			
			successMessageWnd.dialog("open");
		}
	}).fail(function(jqXHR, textStatus, errorThrown) {
		jQuery("#error_msg_wrapper p").html(errorThrown);
			
		errorMessageWnd.dialog("open");
		
		dialogSaveBtn.text('Save');
		dialogSaveBtn.parent().button("enable");
	});
	
	return false;
}

function audioMerchantSaveSettings()
{
	jQuery.ajax({
		url: ajaxurl.split('?')[0]+'?action=audio_merchant_save_settings',
		type: 'POST',
		data: new FormData(jQuery('#settings_form')[0]),
		processData: false,
		contentType: false
    }).done(function (response) {
		var isError = false;
		var errorMsg = '<span class="error_msg">'+theFollowingErrorOccurredText+'<br /><blockquote>';
		
		if(response) {
			if(response.errors.length > 0) {
				isError = true;
				response.errors.forEach(function (theErrorMsg) {
					errorMsg += theErrorMsg+'<br />';
				});
			}
		} else {
			isError = true;
			errorMsg += unknownErrorOccurredTxt;
		}
		
		errorMsg += '</blockquote></span>';
		
		if(isError) {
			jQuery("#error_msg_wrapper p").html(errorMsg);
			
			errorMessageWnd.dialog("open");
		} else {
			jQuery("#success_msg_wrapper p").html(successWndRefreshMsg);
			
			successMessageWnd.dialog("open");
		}
	}).fail(function(jqXHR, textStatus, errorThrown) {
		jQuery("#error_msg_wrapper p").html(errorThrown);
		
		errorMessageWnd.dialog("open");
	});
	
	return false;
}

function convertSecondsToMinutes(seconds) {
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

jQuery(document).ready(function ($) {
	jQuery("#audio_merchant_main_tabs").tabs();
	
	jQuery("#add_audio_btn").button({
      icons: {
        primary: "ui-icon-circle-plus"
      }
    }).click(function (event) {
		event.preventDefault();
		
		openAddNewAudioFileWnd();
	});
	
	jQuery("#create_html_player_widget_btn").button({
      icons: {
        primary: "ui-icon-circle-plus"
      }
    }).click(function (event) {
		event.preventDefault();
		
		createHTMLWidget();
	});
	
	jQuery("#save_settings_btn").button({
      icons: {
        primary: "ui-icon-disk"
      }
    });
	
	newAudioWnd = jQuery("#new_audio_wnd").dialog({
		autoOpen: false,
		width: 550,
		modal: false,
		dialogClass: 'new_audio_wnd',
		buttons: {
			Save: function () {
				addNewAudioFile();
			}
		},
		close: function (event, ui) {
			jQuery('.ui-tooltip').hide();
		}
	});
	
	htmlWidgetDlg = jQuery("#html_widget_dlg").dialog({
		autoOpen: false,
		width: 550,
		modal: false,
		dialogClass: 'html_widget_dlg',
		buttons: {
			Save: function () {
				audioMerchantSaveHTMLPlayer();
			}
		},
		close: function (event, ui) {
			jQuery('.ui-tooltip').hide();
		}
	});
	
	shareDialog = jQuery("#share_dialog").dialog({
		modal: false,
		autoOpen: false,
		width: 'auto',
		buttons: {
		  Close: function() {
			jQuery(this).dialog("close");
		  }
		},
		close: function (event, ui) {
			jQuery('.ui-tooltip').hide();
		}
	});
	
	errorMessageWnd = jQuery("#error_msg_wrapper").dialog({
		modal: false,
		autoOpen: false,
		buttons: {
		  Ok: function() {
			jQuery(this).dialog("close");
		  }
		},
		close: function (event, ui) {
			jQuery('.ui-tooltip').hide();
		}
	});
	
	successMessageWnd = jQuery("#success_msg_wrapper").dialog({
		modal: false,
		autoOpen: false,
		buttons: {
		  Ok: function() {
			jQuery(this).dialog("close");
			jQuery(".ui-dialog-content").dialog("close");
			
			location.reload(true);
		  }
		},
		close: function (event, ui) {
			jQuery('.ui-tooltip').hide();
		}
	});
	
	successMessageWnd2 = jQuery("#success_msg_wrapper2").dialog({
		modal: false,
		autoOpen: false,
		buttons: {
		  Ok: function() {
			jQuery(this).dialog("close");
		  }
		},
		close: function (event, ui) {
			jQuery('.ui-tooltip').hide();
		}
	});
	
	audioInventoryTable = jQuery('#audio_inventory_table').DataTable({
		'lengthMenu': [[10, 20, 30, 40, 50, -1], [10, 20, 30, 40, 50, "All"]],
		'processing': true,
        'serverSide': true,
		'stateSave': true,
		"order": [[ 1, "ASC" ]],
        'ajax': ajaxurl.split('?')[0]+'?action=audio_merchant_get_audio',
		"language": {
			"emptyTable": emptyAudioTxt
		},
		
		"columnDefs": [
            {
                "render": function (data, type, row) {
					if (/^https?:/i.test(data)) {
						return '<a href="'+data+'" target="_blank">'+data+'</a>';
					} else if(!data) {
						return data;
					} else {
						return '<a href="'+uploadBaseUrl+'/'+data+'" target="_blank">'+data+'</a>';
					}
                }, "targets": [5,7,8]
            },{
                "render": function (data, type, row) {
					var output = '';
					
					if (row[4]) {
						if (/^https?:/i.test(row[4])) {
							output += '<a href="'+row[4]+'" target="_blank"><img src="'+row[4].replace(/^https?:/i, '')+'" width="50" height="50" border="0" alt="" align="middle" /></a> ';
						} else {
							output += '<a href="'+uploadBaseUrl+'/'+row[4]+'" target="_blank"><img src="'+uploadBaseUrl+'/'+row[4]+'" width="50" height="50" border="0" alt="" align="middle" /></a> ';
						}
					}
					
					if (/^https?:/i.test(row[5])) {
						output += '<a href="'+row[5]+'" target="_blank">'+data+'</a>';
					} else {
						output += '<a href="'+uploadBaseUrl+'/'+row[5]+'" target="_blank">'+data+'</a>';
					}
					
					if (row[6]) {
						if (/^https?:/i.test(row[6])) {
							output += ' - <a href="'+row[6]+'" target="_blank" class="preview_link">Preview File</a>';
						} else {
							output += ' - <a href="'+uploadBaseUrl+'/'+row[6]+'" target="_blank" class="preview_link">Preview File</a>';
						}
					}
					
					return output;
                }, "targets": [1]
            },{
				className: 'dt-body-center',
                "render": function (data, type, row) {
					var output = '';
					
					output += '<a href="javascript: void(0);" onclick="javascript: editAudio('+String(row[0])+');" title="'+editItemTxt+'"><img src="'+urlImgBase+'edit_icon.png" width="20" height="20" alt="" border="0" /></a>&nbsp;&nbsp;';
					output += '<a href="javascript: void(0);" onclick="javascript: shareAudioItem('+String(row[0])+');" title="'+shareItemtxt+'"><img src="'+urlImgBase+'share_icon.png" width="20" height="20" alt="" border="0" /></a>&nbsp;&nbsp;';
					output += '<a href="javascript: void(0);" onclick="javascript: deleteAudio('+String(row[0])+');" title="'+deleteItemTxt+'"><img src="'+urlImgBase+'trash_icon.png" width="20" height="20" alt="" border="0" /></a>';
					
					return output;
                }, "targets": [12], sortable: false
            },{
				"visible": false,  "targets": [4,5,6]
			},{
				className: 'dt-body-center', "targets": [0,2,3,10,11]
			},{
				className: 'dt-body-center', "visible": false, "targets": [9],
				"render": function (data, type, row) {
					return convertSecondsToMinutes(data);
                }
			}
        ]
	});
	
	htmlPlayerTable = jQuery('#html_player_table').DataTable({
		'lengthMenu': [[10, 20, 30, 40, 50, -1], [10, 20, 30, 40, 50, "All"]],
		'processing': true,
        'serverSide': true,
		'stateSave': true,
		"order": [[ 6, "DESC" ]],
        'ajax': ajaxurl.split('?')[0]+'?action=audio_merchant_get_playlist',
		
		"columnDefs": [
            {
				className: 'dt-body-center',
                "render": function (data, type, row) {
					var output = '';
					
					output += '<a href="javascript: void(0);" onclick="javascript: editHTMLPlayer('+String(row[0])+');" title="'+editItemTxt+'"><img src="'+urlImgBase+'edit_icon.png" width="20" height="20" alt="" border="0" /></a>&nbsp;&nbsp;';
					output += '<a href="javascript: void(0);" onclick="javascript: sharePlaylistItem('+String(row[0])+');" title="'+shareItemtxt+'"><img src="'+urlImgBase+'share_icon.png" width="20" height="20" alt="" border="0" /></a>&nbsp;&nbsp;';
					output += '<a href="javascript: void(0);" onclick="javascript: deleteHTMLPlayer('+String(row[0])+');" title="'+deleteItemTxt+'"><img src="'+urlImgBase+'trash_icon.png" width="20" height="20" alt="" border="0" /></a>';
					
					return output;
                }, "targets": [8], sortable: false
            },{
				className: 'dt-body-center',
                "render": function (data, type, row) {
					if (data == 'all') {
						return displayAllTxt;
					} else if(data == 'selected') {
						return selectedTxt;
					} else if(data == 'text_match') {
						return textMatchTxt;
					}
                }, "targets": [2]
            },{
				className: 'dt-body-center',
                "render": function (data, type, row) {
					data = data.replace("audio_", '');
					data = data.replace("_", ' ');
					
					return amUCWords(data);
                }, "targets": [4]
            },{
				className: 'dt-body-center',
                "render": function (data, type, row) {
					if (data == 'ASC') {
						return ascendingTxt;
					} else if (data == 'DESC') {
						return descendingTxt;
					}
                }, "targets": [5]
            },{
				className: 'dt-body-center',
                "render": function (data, type, row) {
					if (row[2] == 'selected') {
						var numSongsSelected = (data.match(/,/g) || []).length+1;
						
						if (numSongsSelected > 1) {
							return String(numSongsSelected)+' '+filesSelected;
						} else {
							return String(numSongsSelected)+' '+fileSelected;
						}
					} else {
						return data;
					}
                }, "targets": [3]
            },{
				className: 'dt-body-center', "targets": [0,6,7]
			}
        ]
	});
	
	ordersTables = jQuery('#orders_table').DataTable({
		'lengthMenu': [[10, 20, 30, 40, 50, -1], [10, 20, 30, 40, 50, "All"]],
		'processing': true,
        'serverSide': true,
		'stateSave': true,
		"order": [[ 10, "DESC" ]],
        'ajax': ajaxurl.split('?')[0]+'?action=audio_merchant_get_order',
		
		"columnDefs": [
            {
				className: 'dt-body-center',
                "render": function (data, type, row) {
					var output = '';
					
					output += '<a href="'+receiptUrl+row[0]+'" target="_blank" title="'+viewTxt+'"><img src="'+urlImgBase+'details_icon.png" width="20" alt="" border="0" /></a>';
					
					return output;
                }, "targets": [12], sortable: false
            },
			{
				className: 'dt-body-center',
                "render": function (data, type, row) {
					var output = '';
					
					if (row[1] > 0) {
						output += '(ID: '+row[1]+') ';
					}
					
					output += data;
					
					return output;
                }, "targets": [3]
            },
			{
				className: 'dt-body-center',
                "render": function (data, type, row) {
					var output = '';
					
					if (row[7] > 0) {
						output += '(ID: '+row[7]+') ';
					}
					
					output += data;
					
					return output;
                }, "targets": [8]
            },
			{
				className: 'dt-body-center',
                "render": function (data, type, row) {
					var output = '<select onchange="javascript: changeOrderStatus(\''+row[0]+'\', this, \''+data+'\', this.value);">';
					
					if (data == 'Pending') {
						output += '<option value="Pending" selected="selected">Pending</option>';
					} else {
						output += '<option value="Pending">Pending</option>';
					}
					
					
					if (data == 'Refunded') {
						output += '<option value="Refunded" selected="selected">Refunded</option>';
					} else {
						output += '<option value="Refunded">Refunded</option>';
					}
					
					if (data == 'Reversed') {
						output += '<option value="Reversed" selected="selected">Reversed</option>';
					} else {
						output += '<option value="Reversed">Reversed</option>';
					}
					
					if (data == 'Completed') {
						output += '<option value="Completed" selected="selected">Completed</option>';
					} else {
						output += '<option value="Completed">Completed</option>';
					}
					
					output += '</select>';
					
					return output;
                }, "targets": [5]
            },
			{
				className: 'dt-body-center',
                "targets": [0,1,2,4,6,7,9,10,11]
            },
			{
				"visible": false,  "targets": [1,7]
			}
        ]
	});
	
	amTooltips = jQuery(document).tooltip({
		hide: {effect: 'fade', duration: 1}
	});
	
	jQuery('.vertical_audio_scroller').sortable();
	jQuery('.vertical_audio_scroller').disableSelection();
	
	var client = new ZeroClipboard(document.getElementById("copy_share_code_to_clipboard"));
	
	jQuery('#settings_form input[type="text"], #settings_form select, #settings_form textarea').on('change keyup paste', function() {
		updateAuthor();
	});
});

function sharePlaylistItem(playListId)
{
	lastId = playListId;
	lastShareMode = 'playlist_id';
	
	updateShareDialog();
	
	shareDialog.dialog("open");
}

function shareAudioItem(audioId)
{
	lastId = audioId;
	lastShareMode = 'audio_id';
	
	updateShareDialog();
	
	shareDialog.dialog("open");
}

function updateShareDialog()
{
	jQuery('#share_dialog_content').val(loadingTxt);
	jQuery('#copy_share_code_to_clipboard').attr('data-clipboard-text', '');
	
	switch(jQuery('#share_dialog_mode').val())
	{
		case 'wp':
			var code = '';
			
			code = '[audio_merchant '+lastShareMode+'="'+lastId+'" height="400" auto_play="1"]';
			
			jQuery('#share_dialog_content').val(code);
			jQuery('#copy_share_code_to_clipboard').attr('data-clipboard-text', code);
			
			break;
	}
}

function changeOrderStatus(orderId, selectObj, oldValue, newValue) {
	var answer = confirm(confirmChangeStatusTxt);
	
	if (answer) {
		jQuery.get(ajaxurl.split('?')[0]+'?action=audio_merchant_change_order_status&t='+orderId+'&new_status='+newValue, function (data) {
			ordersTables.ajax.reload(null, false);
		}).fail(function(jqXHR, textStatus, errorThrown) {
			jQuery("#error_msg_wrapper p").html(errorThrown);
			
			errorMessageWnd.dialog("open");
		});
	} else {
		jQuery(selectObj).val(oldValue);
	}
}

function amUCWords(str) {
	return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
		return $1.toUpperCase();
	});
}

function openAddNewAudioFileWnd() {
	jQuery('#new_audio_file_form input[type="radio"]').prop('checked', false);
	jQuery('#new_audio_file_form input[type="text"]').val('');
	jQuery('#editing_audio_id').val('');
	
	newAudioWnd.dialog('option', 'title', newAudioWndTitle);
	newAudioWnd.dialog("open");
	
	toggle_upload_field('upload', document.getElementById('cover_photo_upload_link'), 'cover_photo_upload_file_wrapper');
	toggle_upload_field('upload', document.getElementById('audio_upload_link'), 'audio_upload_file_wrapper');
	toggle_upload_field('upload', document.getElementById('preview_audio_upload_link'), 'preview_audio_upload_file_wrapper');
	toggle_upload_field('upload', document.getElementById('additional_lease_upload_link'), 'additional_lease_file_wrapper');
	toggle_upload_field('upload', document.getElementById('additional_exclusive_upload_link'), 'additional_exclusive_file_wrapper');
}

function editHTMLPlayer(playerId) {
	jQuery('#html_widget_form')[0].reset();
			
	toggleCreateHTMLPlayerMode();
	
	var record = htmlPlayerTable.rows(function (idx, data, node) {
		return parseInt(data[0]) == parseInt(playerId) ? true : false;
    }).data();
	
	htmlWidgetDlg.dialog('option', 'title', editItemTxt);
	htmlWidgetDlg.dialog("open");
	
	jQuery('#player_id').val(record[0][0]);
	
	jQuery('#playlist_name').val(jQuery('<textarea/>').html(record[0][1]).text());
	
	if (record[0][2] == 'all') {
		jQuery('#player_mode_all').prop('checked', true);
		
		toggleCreateHTMLPlayerMode();
	} else if (record[0][2] == 'selected') {
		jQuery('#player_mode_selected').prop('checked', true);
		
		toggleCreateHTMLPlayerMode();
		
		var checkedAudioIds = record[0][3].split(',').reverse();
		
		checkedAudioIds.forEach(function (entry) {
			jQuery('#player_selected_audio_ids_'+String(entry)).prop('checked', true);
			
			jQuery('#player_selected_audio_ids_'+String(entry)).parent().parent().prepend(jQuery('#player_selected_audio_ids_'+String(entry)).parent());
		});
	} else if (record[0][2] == 'text_match') {
		jQuery('#player_mode_text_match').prop('checked', true);
		
		toggleCreateHTMLPlayerMode();
		
		jQuery('#player_mode_text_value').val(jQuery('<textarea/>').html(record[0][3]).text());
	}
	
	switch(record[0][4])
	{
		case 'audio_display_name' :
			jQuery('#player_display_order').val('1');
			break;
		
		case 'audio_lease_price':
			jQuery('#player_display_order').val('2');
			
			break;
		
		case 'audio_exclusive_price':
			jQuery('#player_display_order').val('3');
			
			break;
		
		case 'audio_duration':
			jQuery('#player_display_order').val('4');
			
			break;
		
		case 'audio_cdate':
			jQuery('#player_display_order').val('5');
			
			break;
		
		case 'audio_mdate':
			jQuery('#player_display_order').val('6');
			
			break;
	}
	
	jQuery('#player_display_order_direction').val(record[0][5]);
}

function editAudio(audioId) {
	jQuery('.existing_file_scroller').scrollLeft(0);
	
	jQuery('#new_audio_file_form input[type="radio"]').prop('checked', false);
	jQuery('#new_audio_file_form input[type="text"]').val('');
	jQuery('#editing_audio_id').val(audioId);
	
	var audioRecord = audioInventoryTable.rows(function (idx, data, node) {
		return parseInt(data[0]) == parseInt(audioId) ? true : false;
    }).data();
	
	newAudioWnd.dialog('option', 'title', editItemTxt);
	newAudioWnd.dialog("open");
	
	if (audioRecord[0][1]) {
		jQuery('#audio_display_name').val(jQuery('<textarea/>').html(audioRecord[0][1]).text());
	}
	
	if (audioRecord[0][2]) {
		jQuery('#audio_lease_price').val(String(audioRecord[0][2]).replace(/[^0-9.]+/, ''));
	}
	
	if (audioRecord[0][3]) {
		jQuery('#audio_exclusive_price').val(String(audioRecord[0][3]).replace(/[^0-9.]+/, ''));
	}
	
	if (audioRecord[0][4]) {
		if (/^https?:/i.test(audioRecord[0][4])) {
			toggle_upload_field('url', document.getElementById('cover_photo_url_link'), 'cover_photo_url_file_wrapper');
			
			jQuery('#cover_photo_url_file').val(audioRecord[0][4]);
		} else {
			toggle_upload_field('existing', document.getElementById('cover_photo_existing_link'), 'cover_photo_existing_file_wrapper');
			
			var existingFileadio = jQuery('#cover_photo_existing_file_wrapper input[type="radio"][value="'+audioRecord[0][4]+'"]');
			existingFileadio.prop('checked', true);
			existingFileadio.parent().insertBefore(jQuery('#cover_photo_existing_file_wrapper .existing_file_scroller span:first'));
			
			jQuery('#cover_photo_existing_file_wrapper .existing_file_scroller span:first')[0].scrollIntoView();
		}
	} else {
		toggle_upload_field('upload', document.getElementById('cover_photo_upload_link'), 'cover_photo_upload_file_wrapper');
	}
	
	if (audioRecord[0][5]) {
		if (/^https?:/i.test(audioRecord[0][5])) {
			toggle_upload_field('url', document.getElementById('audio_url_link'), 'audio_url_file_wrapper');
			
			jQuery('#audio_url_file').val(audioRecord[0][5]);
		} else {
			toggle_upload_field('existing', document.getElementById('audio_existing_link'), 'audio_existing_file_wrapper');
			
			var existingFileadio = jQuery('#audio_existing_file_wrapper input[type="radio"][value="'+audioRecord[0][5]+'"]');
			existingFileadio.prop('checked', true);
			existingFileadio.parent().insertBefore(jQuery('#audio_existing_file_wrapper .existing_file_scroller span:first'));
			
			jQuery('#audio_existing_file_wrapper .existing_file_scroller span:first')[0].scrollIntoView();
		}
	} else {
		toggle_upload_field('upload', document.getElementById('audio_upload_link'), 'audio_upload_file_wrapper');
	}
	
	if (audioRecord[0][6]) {
		if (/^https?:/i.test(audioRecord[0][6])) {
			toggle_upload_field('url', document.getElementById('preview_audio_url_link'), 'preview_audio_url_file_wrapper');
			
			jQuery('#preview_audio_url_file').val(audioRecord[0][6]);
		} else {
			toggle_upload_field('existing', document.getElementById('preview_audio_existing_link'), 'preview_audio_existing_file_wrapper');
			
			var existingFileadio = jQuery('#preview_audio_existing_file_wrapper input[type="radio"][value="'+audioRecord[0][6]+'"]');
			existingFileadio.prop('checked', true);
			existingFileadio.parent().insertBefore(jQuery('#preview_audio_existing_file_wrapper .existing_file_scroller span:first'));
			
			jQuery('#preview_audio_existing_file_wrapper .existing_file_scroller span:first')[0].scrollIntoView();
		}
	} else {
		toggle_upload_field('upload', document.getElementById('preview_audio_upload_link'), 'preview_audio_upload_file_wrapper');
	}
	
	if (audioRecord[0][7]) {
		if (/^https?:/i.test(audioRecord[0][7])) {
			toggle_upload_field('url', document.getElementById('additional_lease_url_link'), 'additional_lease_url_file_wrapper');
			
			jQuery('#additional_lease_url_file').val(audioRecord[0][7]);
		} else {
			toggle_upload_field('existing', document.getElementById('additional_lease_existing_link'), 'additional_lease_existing_file_wrapper');
			
			var existingFileadio = jQuery('#additional_lease_existing_file_wrapper input[type="radio"][value="'+audioRecord[0][7]+'"]');
			existingFileadio.prop('checked', true);
			existingFileadio.parent().insertBefore(jQuery('#additional_lease_existing_file_wrapper .existing_file_scroller span:first'));
			
			jQuery('#additional_lease_existing_file_wrapper .existing_file_scroller span:first')[0].scrollIntoView();
		}
	} else {
		toggle_upload_field('upload', document.getElementById('additional_lease_upload_link'), 'additional_lease_file_wrapper');
	}
	
	if (audioRecord[0][8]) {
		if (/^https?:/i.test(audioRecord[0][8])) {
			toggle_upload_field('url', document.getElementById('additional_exclusive_url_link'), 'additional_exclusive_url_file_wrapper');
			
			jQuery('#additional_exclusive_url_file').val(audioRecord[0][8]);
		} else {
			toggle_upload_field('existing', document.getElementById('additional_exclusive_existing_link'), 'additional_exclusive_existing_file_wrapper');
			
			var existingFileadio = jQuery('#additional_exclusive_existing_file_wrapper input[type="radio"][value="'+audioRecord[0][8]+'"]');
			existingFileadio.prop('checked', true);
			existingFileadio.parent().insertBefore(jQuery('#additional_exclusive_existing_file_wrapper .existing_file_scroller span:first'));
			
			jQuery('#additional_exclusive_existing_file_wrapper .existing_file_scroller span:first')[0].scrollIntoView();
		}
	} else {
		toggle_upload_field('upload', document.getElementById('additional_exclusive_upload_link'), 'additional_exclusive_file_wrapper');
	}
}

function deleteAudio(audioId)
{
	var answer = confirm(confirmDeleteMsg);
	
	if (answer) {
		jQuery.get(ajaxurl.split('?')[0]+'?action=audio_merchant_delete_audio_item&audio_id='+String(audioId), function(data) {
			jQuery("#success_msg_wrapper p").html(successWndRefreshMsg);
			
			successMessageWnd.dialog("open");
		}).fail(function(jqXHR, textStatus, errorThrown) {
			jQuery("#error_msg_wrapper p").html(errorThrown);

			errorMessageWnd.dialog("open");
		});
	}
}

function deleteHTMLPlayer(playerId)
{
	var answer = confirm(confirmDeleteMsg);
	
	if (answer) {
		jQuery.get(ajaxurl.split('?')[0]+'?action=audio_merchant_delete_playlist&player_id='+String(playerId), function (data) {
			htmlPlayerTable.ajax.reload(null, false);
			
			jQuery("#success_msg_wrapper2 p").html(successDeleteMsg);
			
			successMessageWnd2.dialog("open");
		}).fail(function(jqXHR, textStatus, errorThrown) {
			jQuery("#error_msg_wrapper p").html(errorThrown);

			errorMessageWnd.dialog("open");
		});
	}
}

function createHTMLWidget()
{
	jQuery('#html_widget_form')[0].reset();
			
	toggleCreateHTMLPlayerMode();
	
	jQuery('#player_id').val('');
	
	htmlWidgetDlg.dialog('option', 'title', createHTMLWidgetTxt);
	htmlWidgetDlg.dialog("open");
}

function toggleCreateHTMLPlayerMode()
{
	if (jQuery('#player_mode_all').is(':checked')) {
		jQuery('input[type="checkbox"][name="player_selected_audio_ids[]"]').prop("disabled", true);
		jQuery('#player_mode_text_value').prop("disabled", true);
		jQuery('#player_display_order').prop("disabled", false);
		jQuery('#player_display_order_direction').prop("disabled", false);
	} else if (jQuery('#player_mode_selected').is(':checked')) {
		jQuery('input[type="checkbox"][name="player_selected_audio_ids[]"]').prop("disabled", false);
		jQuery('#player_mode_text_value').prop("disabled", true);
		jQuery('#player_display_order').prop("disabled", true);
		jQuery('#player_display_order_direction').prop("disabled", true);
	} else if (jQuery('#player_mode_text_match').is(':checked')) {
		jQuery('input[type="checkbox"][name="player_selected_audio_ids[]"]').prop("disabled", true);
		jQuery('#player_mode_text_value').prop("disabled", false);
		jQuery('#player_display_order').prop("disabled", false);
		jQuery('#player_display_order_direction').prop("disabled", false);
	}
}

function audioMerchantSaveHTMLPlayer()
{
	var dialogSaveBtn = jQuery('.html_widget_dlg .ui-button-text:contains(Save)');
	
	dialogSaveBtn.html(savingPleaseWaitxt);
	dialogSaveBtn.parent().button("disable");
	
	var actionUrl = ajaxurl.split('?')[0]+'?action=audio_merchant_save_playlist';
	
	jQuery.ajax({
		url: actionUrl,
		type: 'POST',
		data: new FormData(jQuery('#html_widget_form')[0]),
		processData: false,
		contentType: false
    }).done(function (response) {
		var isError = false;
		var errorMsg = '<span class="error_msg">'+theFollowingErrorOccurredText+'<br /><blockquote>';
		
		if(response) {
			if(response.errors.length > 0) {
				isError = true;
				response.errors.forEach(function (theErrorMsg) {
					errorMsg += theErrorMsg+'<br />';
				});
			}
		} else {
			isError = true;
			errorMsg += unknownErrorOccurredTxt;
		}
		
		errorMsg += '</blockquote></span>';
		
		if(isError) {
			jQuery("#error_msg_wrapper p").html(errorMsg);
			
			errorMessageWnd.dialog("open");
			
			dialogSaveBtn.text('Save');
			dialogSaveBtn.parent().button("enable");
		} else {
			htmlPlayerTable.ajax.reload(null, false);
			
			jQuery("#success_msg_wrapper2 p").html(saveSuccessMsg);
			
			successMessageWnd2.dialog("open");
			
			htmlWidgetDlg.dialog("close");
			
			dialogSaveBtn.text('Save');
			dialogSaveBtn.parent().button("enable");
			
			jQuery('#html_widget_form')[0].reset();
			
			toggleCreateHTMLPlayerMode();
		}
	}).fail(function(jqXHR, textStatus, errorThrown) {
		jQuery("#error_msg_wrapper p").html(errorThrown);
			
		errorMessageWnd.dialog("open");
		
		dialogSaveBtn.text('Save');
		dialogSaveBtn.parent().button("enable");
	});
	
	return false;
}

function loadDefaultCSS()
{
	jQuery.get(ajaxurl.split('?')[0]+'?action=audio_merchant_get_default_css', function (data) {
		jQuery('#css_frontend').val(data.result);
	}).fail(function(jqXHR, textStatus, errorThrown) {
		jQuery("#error_msg_wrapper p").html(errorThrown);
		
		errorMessageWnd.dialog("open");
	});
}

function updateAuthor()
{
	if(parseInt(jQuery('#show_author_link').val()) < 1) {
		if(confirm('Some settings can not be changed when "Show Author Credits" is disabled.\n\nWould you like to enable "Show Author Credits" now? If not, the default settings will be restored.')) {
			jQuery('#show_author_link').val('1');
		} else {
			jQuery('#audio_merchant_currency').val('USD');
			jQuery('#temp_download_link_expiration').val('2');
			jQuery('#email_admin_order_notices').val('0');
			jQuery('#purchase_user_login_required').val('0');
			jQuery('#download_user_login_required').val('0');
			jQuery('#exclusive_removed').val('0');
		}
	}
}