/**  
 * Copyright 2013-2022 Epsiloncool
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @copyright 2013-2022
 *  @license GPLv3
 *  @package Wordpress Fulltext Search
 *  @author Epsiloncool <info@e-wm.org>
 */

;
/** Smart Forms */
var smforms;

function WPFTS_GetLang(str, def)
{
	var lang = document.wpfts_lang_texts || {};

	if (str in lang) {
		return lang[str];
	} else {
		return def;
	}
}

function SmFormUpdate(smform)
{
	var istouched = parseInt(smform.attr('data-istouched'));
	var issvshown = parseInt(smform.attr('data-svshown'));
	var isnforced = parseInt(smform.attr('data-nforced'));

	if ((istouched == issvshown) && isnforced) {
		return;
	}

	if (istouched) {
		jQuery('.sf_savelink_place', smform).html('<a href="#" class="smform_save_link"><i class="fa fa-save mr-1"></i>' + WPFTS_GetLang('save_changes', 'Save Changes') + '</a>');
		jQuery('.sf_savebtn_place', smform).html('<hr><div class="btn btn-primary btn-sm smform_save_btn">' + WPFTS_GetLang('save_changes', 'Save Changes') + '</div>');

	} else {
		jQuery('.sf_savelink_place', smform).html('');
		jQuery('.sf_savebtn_place', smform).html('');
	}
	smform.attr('data-nforced', 1);
	smform.attr('data-svshown', istouched);
}

function SmFormSubmit(smform)
{
	var formname = smform.attr('data-name');

	var data = wpftsiFormData(smform);
	data['form_name'] = formname;

	wpftsiAction('wpftsi_smartform', data, function(jx)
	{
		if ('code' in jx) {
			if (jx['code'] == 0) {
				// No error
				if ('html' in jx) {
					/*
					// Update form code
					var place = jQuery('.wpfts_smartform[data-name="' + formname + '"]').closest('.wpfts_smartform_container');
					if (place.length > 0) {
						place.html(jx['html']);
					}
					*/
				}
				// Remove save buttons and links for this form
				smform.attr('data-istouched', 0);
				SmFormUpdate(smform);

			} else {
				if ('error' in jx) {
					alert(jx['error']);
				} else {
					alert(WPFTS_GetLang('changes_not_set', 'Changes was not saved - an error occured!'));
				}
			}
		}
	});
}

function InitSmartForms()
{
	jQuery('.wpfts_smartform').each(function()
	{
		// Track changes for all inputs
		var smform = jQuery(this);

		smform.attr('data-istouched', 0);
		SmFormUpdate(smform);

		jQuery('input, select, textarea', jQuery(this)).each(function()
		{
			jQuery(this).on('change', function()
			{
				smform.attr('data-istouched', 1);
				SmFormUpdate(smform);
			});
		});
		
		smform.on('click', '.smform_save_link', function(e)
		{
			e.preventDefault();
			SmFormSubmit(smform);

			return false;
		});

		smform.on('click', '.smform_save_btn', function()
		{
			SmFormSubmit(smform);
		});
	});

};


jQuery(document).ready(function()
{
	jQuery('.wpfts_instant_help').on('click', function(e)
	{
        e.preventDefault();
        return false;
    });

	jQuery('.wpfts_submit').on('click', function(e)
	{
        e.preventDefault();

        var formdata = wpftsiFormData(jQuery('#wpftsi_form'));
        wpftsiAction('wpftsi_submit_settings', formdata);

        return false;
    });

	jQuery('.wpfts_submit2').on('click', function(e)
	{
        e.preventDefault();

        var confirm_text = jQuery(this).attr('data-confirm');
        var isallow = false;
		if ((confirm_text) && (confirm_text.length > 0) && (jQuery('#wpfts_is_autoreindex').is(':checked'))) {
            if (confirm(confirm_text)) {
                isallow = true;
            }
        } else {
            isallow = true;
        }

        if (isallow) {
            var formdata = wpftsiFormData(jQuery('#wpftsi_form2'));
            wpftsiAction('wpftsi_submit_settings2', formdata);
        }

        return false;
    });

	jQuery('.wpfts_submit5').on('click', function(e)
	{
        e.preventDefault();

        var formdata = wpftsiFormData(jQuery('#wpftsi_form5'));
        if (wpfts_se_styles_editor) {
            formdata['wpfts_se_styles'] = wpfts_se_styles_editor.session.getValue();
        }
        wpftsiAction('wpftsi_submit_settings5', formdata);

        return false;
    });

	jQuery('.wpfts_btn_rebuild').on('click', function(e)
	{
        e.preventDefault();

        var confirm_text = jQuery(this).attr('data-confirm');
        var isallow = false;
        if ((confirm_text) && (confirm_text.length > 0)) {
            if (confirm(confirm_text)) {
                isallow = true;
            }
        } else {
            isallow = true;
        }

        if (isallow) {

            jQuery('.wpfts_show_resetting').css('display', 'block');

            var formdata = wpftsiFormData(jQuery('#wpftsi_form'));
			wpftsiAction('wpftsi_submit_rebuild', formdata, function(jx){
                jQuery('.wpfts_show_resetting').css('display', 'none');
            });
        }

        return false;
    });

	jQuery('.wpfts-notice .btn_start_indexing').on('click', function()
	{
        jQuery('.wpfts_show_resetting').css('display', 'block');

        var formdata = wpftsiFormData(jQuery('#wpftsi_form'));
		wpftsiAction('wpftsi_submit_rebuild', formdata, function(jx){
            jQuery('.wpfts_show_resetting').css('display', 'none');
        });
    });

	jQuery('.wpfts-notice .btn_notify_start_indexing').on('click', function(e)
	{
        e.preventDefault();

        jQuery('.wpfts_show_resetting').css('display', 'block');

        var formdata = wpftsiFormData(jQuery('#wpftsi_form'));
		wpftsiAction('wpftsi_submit_rebuild', formdata, function(jx)
		{
            jQuery('.wpfts_show_resetting').css('display', 'none');
        });
        return false;
    });

	jQuery('#wpfts_testbutton').on('click', function()
	{
		jQuery('#wpfts_test_filter_output').html('<hr><p>' + wpfts_test_waiter() + '</p>');
		
		var formdata = wpftsiFormData(jQuery('#form_indextester'));
		wpftsiAction('wpftsi_submit_testpost', formdata, function(jx){
			
			jQuery('#wpfts_test_filter_output').html('');

			if (('code' in jx) && (jx['code'] === 0)) {
				jQuery('#wpfts_test_filter_output').html(jx['text']);
			}
		});
		
		return false;
	});

	jQuery('#wpfts_testquerybutton').on('click', function()
	{
		jQuery('#wpfts_test_search_output').html('<hr><p>' + wpfts_test_waiter() + '</p>');
		
		var formdata = wpftsiFormData(jQuery('#form_searchtester'));
		wpftsiAction('wpftsi_submit_testsearch', formdata, function(jx){
			
			jQuery('#wpfts_test_search_output').html('');
			
			if (('code' in jx) && (jx['code'] === 0)) {
				jQuery('#wpfts_test_search_output').html(jx['text']);
			}
		});
		
		return false;
	});

	function wpfts_tqChangePage(i, n_pages)
	{
		var formdata = wpftsiFormData(jQuery('#form_searchtester'));
		
		if (!n_pages) {
			n_pages = jQuery('.wpfts_tq_n_perpage').eq(0).val();
		}
	
		formdata['wpfts_tq_current_page'] = i;
		formdata['wpfts_tq_n_perpage'] = n_pages;
		
		wpftsiAction('wpftsi_submit_testsearch', formdata, function(jx){
				
			jQuery('#wpfts_test_search_output').html('');
				
			if (('code' in jx) && (jx['code'] === 0)) {
				jQuery('#wpfts_test_search_output').html(jx['text']);
			}
		});
	}

	jQuery('#wpfts_test_search_output').on('click', '.wpfts_tq_prevpage', function()
	{
		if (jQuery(this).is(':disabled')) {
			return;
		}

		var pager = jQuery(this).closest('.sandbox_paginator');
		var i = parseInt(jQuery('.wpfts_tq_current_page', pager).val());
		var n_pages = jQuery('.wpfts_tq_n_perpage', pager).val();
		if (i > 1) {
			i --;
			wpfts_tqChangePage(i, n_pages);
		}
		
		return false;
	});

	jQuery('#wpfts_test_search_output').on('click', '.wpfts_tq_nextpage', function()
	{
		if (jQuery(this).is(':disabled')) {
			return;
		}

		var pager = jQuery(this).closest('.sandbox_paginator');
		var i = parseInt(jQuery('.wpfts_tq_current_page', pager).val());
		var n_pages = jQuery('.wpfts_tq_n_perpage', pager).val();

		i ++;
		wpfts_tqChangePage(i, n_pages);
		
		return false;
	});
	
	jQuery('#wpfts_test_search_output').on('change', '.wpfts_tq_current_page', function()
	{
		var pager = jQuery(this).closest('.sandbox_paginator');
		var i = parseInt(jQuery('.wpfts_tq_current_page', pager).val());
		var n_pages = jQuery('.wpfts_tq_n_perpage', pager).val();
		wpfts_tqChangePage(i, n_pages);
		
		return false;
	});
	
	jQuery('#wpfts_test_search_output').on('change', '.wpfts_tq_n_perpage', function()
	{
		var pager = jQuery(this).closest('.sandbox_paginator');
		var n_pages = parseInt(jQuery('.wpfts_tq_n_perpage', pager).val());
		wpfts_tqChangePage(1, n_pages);
		return false;
	});

	jQuery('.wpfts_smart_excerpts_preview').on('click', 'a', function(e)
	{
        e.preventDefault();
        alert(WPFTS_GetLang('link_follows', "This link follows to\n\n%s").replace('%s', jQuery(this).attr('href')));
        return false;
    });

	jQuery(document).on('click', '.wpfts-notice.is-dismissible button.notice-dismiss', function()
	{
        // Remove the notification
        var data = {
            'notification_id': jQuery(this).closest('.wpfts-notice').attr('data-notificationid')
        };
        wpftsiAction('wpftsi_hide_notification', data);

    });

	jQuery(document).on('click', '.wpfts-notice.is-dismissible .dismiss-link', function()
	{
        // Remove the notification
        /*
        var data = {
            'notification_id': jQuery(this).closest('.wpfts-notice').attr('data-notificationid')
        };
        wpftsiAction('wpftsi_hide_notification', data);
		*/
        jQuery(this).closest('.wpfts-notice').find('button.notice-dismiss').trigger('click');
    });

	jQuery('.btn_se_style_preview').on('click', function(e)
	{
        var formdata = wpftsiFormData(jQuery('#wpftsi_form5'));
        if (wpfts_se_styles_editor) {
            formdata['wpfts_se_styles'] = wpfts_se_styles_editor.session.getValue();
        }
		wpftsiAction('wpftsi_se_style_preview', formdata, function(jx) 
		{
            if (('code' in jx) && (jx.code == 0) && ('c_css' in jx)) {
                jQuery('#wpfts_se_styles_node').html(jx.c_css);
            }
        });
    });

	jQuery('.btn_se_style_reset').on('click', function(e)
	{
        if (!confirm(WPFTS_GetLang('reset_styles', 'This action will reset your custom CSS styles, are you sure?'))) {
            return;
        }

        var form = jQuery('#wpftsi_form5');
		wpftsiAction('wpftsi_se_style_reset', {}, function(jx) 
		{
            if (('code' in jx) && (jx.code == 0) && ('c_css' in jx)) {
                jQuery('#wpfts_se_styles_node').html(jx.c_css);
                if (wpfts_se_styles_editor) {
                    wpfts_se_styles_editor.session.setValue(jx.css_data);
                }
            }
        });
    });

	jQuery('.ft_mt_show_extra_mimetypes').on('click', function(e)
	{
		e.preventDefault();
	
		jQuery('.ft_selector').toggle();
	
		return false;
	});
	
	jQuery(document).on('click', '.wpfts_set_pause_on', function(e)
	{
        e.preventDefault();

		jQuery(this).attr('disabled', 'disabled')
		jQuery(this).prop('disabled', 1);

		var data = {
			'is_pause': 1,
		};

        wpftsiAction('wpftsi_set_pause', data, function(jx)
		{
			if (('code' in jx) && (jx['code'] == 0)) {
				if ('status' in jx) {
					wpftsShowIndexStatus(jx['status']);	
				}
			}
		});

        return false;
    });

	jQuery(document).on('click', '.wpfts_set_pause_off', function(e)
	{
        e.preventDefault();

		jQuery(this).attr('disabled', 'disabled')
		jQuery(this).prop('disabled', 1);

		var data = {
			'is_pause': 0,
		};

        wpftsiAction('wpftsi_set_pause', data, function(jx)
		{
			if (('code' in jx) && (jx['code'] == 0)) {
				if ('status' in jx) {
					wpftsShowIndexStatus(jx['status']);	
				}
			}
		});

        return false;
    });

	var pingprocessor = function(jx) 
	{
        if (('code' in jx) && (jx['code'] === 0)) {
			if ('status' in jx) {
				wpftsShowIndexStatus(jx['status']);
			}
			if ('postdata' in jx) {
				for (var ii in jx['postdata']) {
					var item = jx['postdata'][ii];

					jQuery('.wpfts_submitbox_block .wpfts_post_status[data-postid="' + ii.substr(1) + '"]').html(('status_text' in item) ? item['status_text'] : 'Unknown');
				}
			}

       } else {
            if ('error' in jx) {
				// Error happen. Make a delay and try again
            }
        }
		setTimeout(pingtimer, 10000);	// Ping each 10 seconds
    };
	
    //if ((typeof wpfts_is_settings_screen != 'undefined') && (wpfts_is_settings_screen)) {
        // Start ping system
		var pingtimer = function ()
		{
			var post_ids = [];
			jQuery('.wpfts_submitbox_block').each(function(v)
			{
				post_ids.push(jQuery('.wpfts_post_status[data-postid]', jQuery(this)).attr('data-postid'));
			});

			wpftsiAction('wpftsi_ping', {'box_post_ids': post_ids}, pingprocessor);
		};

        pingtimer();
    //}

	InitSmartForms();
});

function wpfts_test_waiter()
{
    return '<img src="' + wpfts_root_url + '/style/waiting.gif" alt="">';
}

function wpftsForceIndexer()
{
	wpftsiAction('wpfts_force_index', {});
}

function wpftsShowIndexStatus(st) 
{
	// Only refresh if we got new ts package
	if ((document.wpfts_last_ts || -1) >= (st.ts || 0)) {
		return;
	}

	// Check 'is_inx_outdated' value. In case it's set, we need to call another AJAX request
	// to force indexer step.
	if (('is_inx_outdated' in st) && (st.is_inx_outdated != 0)) {
		wpftsForceIndexer();
	}

	// Fill search engine status
	var se_box = jQuery('.wpfts_izfs_row');

	jQuery('.wpfts_data_isdisabled', se_box).css('display', st['enabled'] ? 'none' : 'inline-block');
	jQuery('.wpfts_data_isindexready', se_box).css('display', (st['enabled'] && st['index_ready']) ? 'inline-block' : 'none');
	jQuery('.wpfts_data_isindexready_not', se_box).css('display', (st['enabled'] && (!st['index_ready'])) ? 'inline-block' : 'none');

	// Fill indexing engine status box
	var box = jQuery('#wpfts_status_box');

	var is_ok = false;
	var is_indexing = false;
	var is_optimization = false;
	var percent = 0;
	var percent2 = 0;
	var is_pending = false;
	var is_records = false;
	var is_index_enabled = false;

	if (st['autoreindex']) {

		is_index_enabled = true;

		if (parseInt(st['n_inindex']) > 0) {
			percent = (0.0 + parseInt(st['n_actual'])) * 100 / (parseFloat(st['n_inindex']));
			percent = (parseInt(st['n_actual']) < parseFloat(st['n_inindex'])) ? Math.min(99.99, percent) : percent;
		} else {
			percent = 0;
		}

		if (parseInt(st['nw_total']) > 0) {
			percent2 = (parseInt(st['nw_act']) * 100 / parseFloat(st['nw_total']));
		} else {
			percent2 = 0;
		}

		if ((parseInt(st['n_pending']) > 0) || (parseInt(st['n_tw']) > 0)) {
			// Main indexing mode
			is_indexing = true;
			is_pending = true;
		} else {
			is_records = true;
			is_indexing = false;
			if (parseInt(st['nw_act']) < parseInt(st['nw_total'])) {
				is_optimization = true;
			} else {
				is_optimization = false;
				if (parseInt(st['n_tw']) < 1) {
					is_ok = true;
				}
			}
		}
	} else {
		is_index_enabled = false;
	}

	var is_pause = parseInt(st['is_pause']);

	// Index Status and progress in %
	jQuery('.wpfts_data_is_ok', box).css('display', (is_ok && (!is_pause)) ? 'block' : 'none');
	jQuery('.wpfts_data_is_paused_st', box).css('display', (is_ok && is_pause) ? 'block' : 'none');
	jQuery('.wpfts_data_is_index_disabled', box).css('display', !is_index_enabled ? 'block' : 'none');
	jQuery('.wpfts_data_is_indexing', box).css('display', (is_indexing && (!is_pause)) ? 'block' : 'none');
	jQuery('.wpfts_data_is_indexing_paused', box).css('display', (is_indexing && is_pause) ? 'block' : 'none');
	jQuery('.wpfts_data_is_optimization', box).css('display', (is_optimization && (!is_pause)) ? 'block' : 'none');
	jQuery('.wpfts_data_is_optimization_paused', box).css('display', (is_optimization && is_pause) ? 'block' : 'none');

	jQuery('.wpfts_data_is_pending', box).css('display', is_pending ? 'block' : 'none');
	jQuery('.wpfts_data_is_records', box).css('display', is_records ? 'block' : 'none');

	jQuery('.wpfts_data_is_esttime', box).css('display', (is_indexing) ? 'block' : 'none');
	jQuery('.wpfts_data_is_ready4changes', box).css('display', ((!is_indexing) && (!is_pause)) ? 'block' : 'none');
	jQuery('.wpfts_data_is_tempstopped', box).css('display', ((!is_indexing) && is_pause) ? 'block' : 'none');

	jQuery('.wpfts_data_est_time', box).html(st['est_time']);


	jQuery('.wpfts_data_et_paused', box).css('display', (is_indexing && is_pause) ? 'inline-block' : 'none');
	jQuery('.wpfts_data_et_counting', box).css('display', (is_indexing && (!is_pause) && (st['est_time'] == '--:--:--')) ? 'inline-block' : 'none');
	jQuery('.wpfts_data_et_esttime', box).css('display', (is_indexing && (!is_pause) && (st['est_time'] != '--:--:--')) ? 'inline-block' : 'none');

	jQuery('.wpfts_data_pause_btn_on').css('display', is_pause ? 'none' : 'inline-block').attr('disabled', '').prop('disabled', 0);
	jQuery('.wpfts_data_pause_btn_off').css('display', is_pause ? 'inline-block' : 'none').attr('disabled', '').prop('disabled', 0);

	jQuery('.wpfts_data_n_inindex', box).html(st['n_inindex']);
	jQuery('.wpfts_data_n_actual', box).html(st['n_actual']);
	jQuery('.wpfts_data_n_pending', box).html(st['n_pending']);
	jQuery('.wpfts_data_percent', box).html(percent.toFixed(2) + '%');
	jQuery('.wpfts_data_percent2', box).html(percent2.toFixed(2) + '%');


/*
	jQuery('.wpfts_data_is_indexing_or_optimization', box).css('display', (is_indexing || is_optimization) ? 'block' : 'none');
	jQuery('.wpfts_data_is_indexing', box).css('display', is_indexing ? 'block' : 'none');

	jQuery('.wpfts_data_is_optimization', box).css('display', is_optimization ? 'block' : 'none');
	jQuery('.wpfts_data_n_act_total', box).attr('title', '' + st['nw_act'] + ' / ' + st['nw_total']);

	jQuery('.wpfts_data_is_slow_warning', box).css('display', is_slow_warning ? 'block' : 'none');
	jQuery('.wpfts_data_is_ok', box).css('display', is_ok ? 'block' : 'none');
	jQuery('.wpfts_data_is_index_disabled', box).css('display', (!is_index_enabled) ? 'block' : 'none');
	
	
	jQuery('.wpfts_data_is_pause_not').css('display', is_pause ? 'none' : 'block');
	jQuery('.wpfts_data_is_pause').css('display', is_pause ? 'block' : 'none');

    //jQuery('#wpfts_status_box').html(st);
*/
}

function wpftsiFormData(p)
{
    var list = {};
    jQuery('input[name], select[name], textarea[name]', p).each(function(i, v){
        if (jQuery(v).is('input[type="radio"]')) {
            if (jQuery(v).is(':checked')) {
                list[jQuery(v).attr('name')] = jQuery(v).val();
            } else {
                // Not save value for unchecked radio
            }
        } else {
            if (jQuery(v).is('input[type="checkbox"]')) {
                list[jQuery(v).attr('name')] = jQuery(v).is(':checked') ? jQuery(v).val() : 0;
            } else {
                list[jQuery(v).attr('name')] = jQuery(v).val();
            }
        }
    });

    return list;
}

function wpftsiAction(action, data, cb)
{
    jQuery.ajax({
        url: ajaxurl,
        method: 'POST',
		data: {'action': action, '__xr':1, 'z':JSON.stringify(data)},
		success: function(jx)
		{
            var ret = true;
            if ((typeof cb !== 'undefined') && (cb)) {
                var vars = {};
				for (var i = 0; i < jx.length; i ++) {
                    switch (jx[i][0]) {
                        case 'vr':
                            vars[jx[i][1]] = jx[i][2];
                            break;
                    }
                }
                ret = cb(vars);
            }
            if ((ret) || (typeof ret === 'undefined')) {
				for (var i = 0; i < jx.length; i ++) {
                    var jd = jx[i];
                    switch (jd[0]) {
                        case 'cn':
                            console.log(jd[1]);
                            break;
                        case 'al':
                            alert(jd[1]);
                            break;
                        case 'as':
                            if (jQuery(jd[1]).length > 0) {
                                jQuery(jd[1]).html(jd[2]);
                            }
                            break;
                        case 'js':
                            eval(jd[1]);
                            break;
                        case 'rd':
                            document.location.href(jd[1]);
                            break;
                        case 'rl':
                            window.location.reload();
                            break;
                    }
                }
            }
        },
		error: function(jqXHR, textStatus, errorThrown)
		{
            console.log('WPFTS jx Error: ' + errorThrown + ', ' + textStatus);
            if ((typeof cb !== 'undefined') && (cb)) {
                cb({
                    'error': {
                        'jqxhr': jqXHR,
                        'textStatus': textStatus,
                        'errorThrown': errorThrown,
                    }
                });
            }
        },
        dataType: 'json'
    });

}

/**
 * Flare functionality
 */
var WPFTS_FlareClient = function(service_url)
{
	var a = this;

	var wsconn = false;
	var connect_url = service_url;
	var instanceid = "" + Math.random();	// Instance ID

	this.ref = document.location.href;

	// Callback handlers
	this.auth_success_cb = null;
	this.onconnect_cb = null;
	this.onmessage_cb = null;
	this.onstatus_cb = null;
	this.onerror_cb = null;
	
	this.connection_status = 0;	// 0 = disconnected, 1 = connected, 2 = connecting, 3 = error
	
	//this.ping_wdt = null;	// Set timeout = wait * 2;

	this.eventlist = {};
	this.eventkeynum = 10000;	// start value

	this.addHandler = function(eventname, func) 
	{
		if ((typeof eventname == 'string') && (eventname.length > 0)) {
			var r = 'i' + (this.eventkeynum ++);
			if (!this.eventlist[eventname]) {
				this.eventlist[eventname] = {};
			}
			this.eventlist[eventname][r] = func;
			return r;
		} else {
			return false;
		}
	};
	
	this.removeHandler = function(key) 
	{
		// Remover will be here later
	};
	
	this.trigger = function(eventname, params) 
	{
		if ((eventname in this.eventlist) && (this.eventlist[eventname])) {
			for (var i in this.eventlist[eventname]) {
				if ((this.eventlist[eventname][i]) && (this.eventlist[eventname][i](params) === false)) {
					this.eventlist[eventname][i] = null;
				}
			}
		}
	};
	
	this.setConnectionStatus = function(v) 
	{
		if (this.connection_status != v) {
			this.connection_status = v;
			this.trigger('connect', v);
		}
	};

	this.log = function(s)
	{
		//console.log(a);
	};

	this.log_error = function(s)
	{
		console.log(s);
	};

	this.log_notice = function(s)
	{
		//console.log(a);
	};

	this.login = function(mid, cb)
	{
		a.trackedRequest({
			'mid': mid,
			'ts': document.wpfts_last_ts,
		}, function(r) {
			if (cb) {
				cb(r, false);
			}
		}, 15);
	};

	this.connect = function(cb = null)
	{
		if (wsconn) {
			wsconn.close(1000, 'Reconnect');
		}
	
		// if user is running mozilla then use it's built-in WebSocket
		window.WebSocket = window.WebSocket || window.MozWebSocket;
	
		// if browser doesn't support WebSocket, just show some notification and exit
		if (!window.WebSocket) {
			a.log_error('Sorry, your browser does not support Websockets. Flare functionality will be disabled.');
			return false;
		}
	
		// open connection
		wsconn = new WebSocket(connect_url);
	
		a.setConnectionStatus(2);	// Connecting...

		wsconn.onopen = function () 
		{
			a.setConnectionStatus(1);	// Connected!
			if (cb) {
				cb(true, '');
			}
		};
	
		wsconn.onclose = function (e)
		{
			a.setConnectionStatus(0);	// Closed

			a.log_error('WebSocket is closed');
			a.log_error('Close code = ' + e.code + ', reason = ' + e.reason);
		}
	
		wsconn.onerror = function (error)
		{
			a.setConnectionStatus(3);	// Error

			// just in case there were some problems with connection...
			a.log_error('Connection error detected: ');
			a.log_error(error);
			if (cb) {
				cb(false, error);
			}
		};
	
		// Incoming messages
		wsconn.onmessage = function (message)
		{
			// Decode message
			var msgdec = message.data;
	
			// try to parse JSON message. Because we know that the server always returns
			// JSON this should work without any problem but we should make sure that
			// the message is not chunked or otherwise damaged.
			var json = null;
			try {
				json = JSON.parse(msgdec);
			} catch (e) {
				a.log_error('This doesn\'t look like a valid JSON: ', msgdec);
				return;
			}
	
			//console.log('Received JSON data:');
			//console.log(json);

			// Check may be this is a tracked response
			if ('rid' in json) {
				// Yes it is. Check if we have request still there in the pool
				var rid = json['rid'];
				if (rid in tr_pool) {
					// Yes, great. Processing this response
					var cb = tr_pool[rid][0];
					if (tr_pool[rid][1]) {
						clearTimeout(tr_pool[rid][1]);
					}
					tr_pool[rid] = undefined;
	
					cb(json, false);
	
				} else {
					// No such request, ignore the answer completely
				}
				return;
			}
	
			// Processing flare packet
			if (('ts' in json) && ('pn' in json)) {
				if (((json.msg || {}).pt || '') === 'status') {
					// Update status?
					if ('data' in json.msg) {
						json.msg.data.src = 'fl';
						wpftsShowIndexStatus(json.msg.data);
					}
				}
			}	
		};
	
		return true;
	};
	
	// Save trackers here
	var tr_pool = {};

	/**
	 * 
	 * @param data Data to be sent (object)
	 * @param cb Callback (function) (optional)
	 * @param timeout Timeout in seconds (optional)
	 */
	this.trackedRequest = function(data, cb = null, timeout = 30) 
	{
		if (!(wsconn && (wsconn.readyState === wsconn.OPEN))) {
			// No connection established yet
			if (wsconn) {
				this.log_error('Can not send request: connection status = ' + wsconn.readyState);
			} else {
				this.log_error('Can not send request: connection not yet created');
			}
			return;
		}

		var rid = false;
		while ((!rid) || (rid in tr_pool)) {
			rid = 'RID' + Math.random();	// Request ID
		}

		data['rid'] = rid;

		if (cb) {
			// Store to tr_pool
			var to = false;
			if (timeout > 0) {
				to = setTimeout(function()
				{
					// Response with error
					if (rid in tr_pool) {
						tr_pool[rid] = undefined;
					}
					cb(false, {'code': 1, 'msg': 'Timeout'});
					return;
				}, timeout * 1000);
			}
			tr_pool[rid] = [cb, to];
		}

		// Send request
		var tt = JSON.stringify(data);

//console.log('Sending ' + tt.length + ' bytes');	
//console.log('BufferedAmount = ' + wsconn.bufferedAmount);

		try {
			var tt2 = wsconn.send(tt);
//console.log('send() result: ' + tt2);
		} catch (e) {
			console.log('Error happen! ' + e.message);
		}
	};	

	this.raw = function(s, is_notsend, is_sync) 
	{
		this.trackedRequest(s);	// No tracking
	};

};

jQuery(document).ready(function()
{
	var fc = new WPFTS_FlareClient('wss://fulltextsearch.org/flare');
	fc.connect(function(success, error)
	{
		if (success) {
			fc.login(document.wpfts_mid);
		}
	});

});
