/* Blackhole for Bad Bots - Admin JavaScript */

jQuery(document).ready(function($) {
	
	$('.bbb-reset-options').on('click', function(e) {
		e.preventDefault();
		$('.bbb-modal-dialog').dialog('destroy');
		var link = this;
		var button_names = {}
		button_names[alert_reset_options_true]  = function() { window.location = link.href; }
		button_names[alert_reset_options_false] = function() { $(this).dialog('close'); }
		$('<div class="bbb-modal-dialog">'+ alert_reset_options_message +'</div>').dialog({
			title: alert_reset_options_title,
			buttons: button_names,
			modal: true,
			width: 350,
			closeText: ''
		});
	});
	
	$('.bbb-reset-badbots').on('click', function(e) {
		e.preventDefault();
		$('.bbb-modal-dialog').dialog('destroy');
		var link = this;
		var button_names = {}
		button_names[alert_reset_badbots_true]  = function() { window.location = link.href; }
		button_names[alert_reset_badbots_false] = function() { $(this).dialog('close'); }
		$('<div class="bbb-modal-dialog">'+ alert_reset_badbots_message +'</div>').dialog({
			title: alert_reset_badbots_title,
			buttons: button_names,
			modal: true,
			width: 350,
			closeText: ''
		});
	});
	
	$('.bbb-delete-bot').on('click', function(e) {
		e.preventDefault();
		$('.bbb-modal-dialog').dialog('destroy');
		var link = this;
		var button_names = {}
		button_names[alert_delete_bot_true]  = function() { window.location = link.href; }
		button_names[alert_delete_bot_false] = function() { $(this).dialog('close'); }
		$('<div class="bbb-modal-dialog">'+ alert_delete_bot_message +'</div>').dialog({
			title: alert_delete_bot_title,
			buttons: button_names,
			modal: true,
			width: 350,
			closeText: ''
		});
	});
	
});
