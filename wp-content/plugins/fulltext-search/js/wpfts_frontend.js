;
jQuery(document).ready(function()
{
	// Add autocompleting to WPFTS Custom Search widgets
	jQuery('.search-form.wpfts_autocomplete').each(function(i, v)
	{
		var inp = jQuery('input[name="s"]', v);
		var widget_code = jQuery('input[name="wpfts_wdgt"]', v).val();

		jQuery(inp).autocomplete({
			source: function(request, response)
			{
				jQuery.ajax({
					method: 'post',
					url: document.wpfts_ajaxurl,
					dataType: "json",
					data: {
						action: 'wpfts_autocomplete',
						wpfts_wdgt: widget_code,
						sq: request.term
					},
					success: function(data) {
						response(data);
					}
				});
			},
			minLength: 1,
			delay: 300,
			select: function( event, ui ) {
				var t = jQuery('<textarea />').html(ui.item.label).text();
				// Remove <sup>
				t = t.replace(/<sup>[^<]*<\/sup>?/gm, '');
				
				// Remove all HTML tags
				t = t.replace(/<[^>]*>?/gm, '');
				
				inp.val(t);
				
				if ('link' in ui.item) {
					document.location.href = ui.item.link;
				}
				
				event.preventDefault();
				return false;
			},
			focus: function( event, ui ) {
				var t = jQuery('<textarea />').html(ui.item.label).text();
				// Remove <sup>
				t = t.replace(/<sup>[^<]*<\/sup>?/gm, '');
				
				// Remove all HTML tags
				t = t.replace(/<[^>]*>?/gm, '');				
				
				inp.val(t);
				event.preventDefault();
				return false;
			},
			create: function (event,ui) {
				jQuery(this).data('ui-autocomplete')._renderItem = function(ul, item)
				{
					var str = '<li><a>' + item.label + '</a></li>';
					return jQuery(str).appendTo(ul);
				};
			}
		});

	});

});
