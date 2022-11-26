jQuery(function(){
	( function( blocks, element ) {
		var el = element.createElement,
			InspectorControls = wp.blockEditor.InspectorControls,
			ServerSideRender = wp.serverSideRender;

		/* Plugin Category */
		blocks.getCategories().push({slug: 'searchinplace', title: 'Search in Place'});

		/* ICONS */
		const iconSIP = el('img', { width: 20, height: 20, src:  "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAF3SURBVDiNrZW/LwRBFMc/Iwr6MSQnOQ0alWskJBdqcw2V5CIR7VU0/gLV/QUoREIjFENLJCQaf8DRkJDI2KgVklHsWzl7a9eP+zbfzJu3n7w3My+rQgh0U70ASqnPgHa+BtSACjAK3AE3wHFkzWkeLISACiGglEI7r4BdoJ7zzR6wHFmT2VYIgZ629bbA3oCGVNcv3pB4HdjKq1KFEBg4eZkFzoB3YCqy5iadqJ2vANfExzQXWXOeV+GqeDMLBiDxZiq/QwmwKn6Y107bfvW7hAQ4KH5bAGyJDxUBn8XHCoDj4q9FwAvxxQLggnjHhaSB2+Jr2vnJrESJr8uymZUDfHnYO8AK8XvbAE6AR2AYmAc2gT7gILJmKQv220kJwBFx2w/ATGTN47fAttZqgCWe5QkJ7wObkTUt7fw9UM6CZgKLpJ0vA1dAKQ1Nz/KPFFnzAEwDT1LppXZ+ONn/NbAI+idgBlQRX1rc93+knR/RzpcSlur2L+ADPbua8GEjZ9cAAAAASUVORK5CYII=" } );

		blocks.registerBlockType( 'searchinplace/sip', {
			title: 'Search in Place',
			icon: iconSIP,
			category: 'searchinplace',
			supports: {
				customClassName: false,
				className: false
			},

			attributes: {
				placeholder : {
					type 	: 'text',
					default : ''
				},
				search_in_page : {
					type 	: 'integer',
					default : 0
				},
				disable_enter_key : {
					type 	: 'integer',
					default : 0
				},
				no_popup : {
					type 	: 'integer',
					default : 0
				},
				exclude_hidden : {
					type 	: 'integer',
					default : 0
				},
				display_button : {
					type 	: 'integer',
					default : 0
				},
			},

			edit: function( props ){
				var focus = props.isSelected,
					children = [
						el(
							ServerSideRender,
							{
								key : 'sip_server',
								block: 'searchinplace/sip',
								attributes: props.attributes,
							}
						)
					];


				if(!!focus)
				{
					children.push(
						el(
							InspectorControls,
							{
								key: 'sip_inspector'
							},
							el(
								'div',
								{
									key 	 : 'search_in_place_container',
									className: 'components-panel__body is-opened'
								},
								[
									el(
										'label',
										{
											key : 'sip_placeholder_label',
										},
										'Placeholder text'
									),
									el(
										'input',
										{
											type 	: 'text',
											key 	: 'sip_placeholder',
											value	: props.attributes.placeholder,
											onChange: function(evt){
												props.setAttributes(
													{placeholder: evt.target.value}
												);
											},
                                            style:{width:'100%'},
										},
									),
									el( 'div', {key: 'sip_br_5', style:{marginBottom: '10px'}}),
                                    el(
										'input',
										{
											type 	: 'checkbox',
											key 	: 'sip_search_in_page',
											checked	: (props.attributes.search_in_page == 1),
											onChange: function(evt){
												props.setAttributes(
													{search_in_page: (evt.target.checked ? 1 : 0)}
												);
											},
                                        },
									),
									el(
										'label',
										{
											key : 'sip_search_in_page_label',
										},
										'Search in current page only'
									),
									el( 'div', {key: 'sip_br_4', style:{marginBottom: '10px'}}),
									el(
										'input',
										{
											type 	: 'checkbox',
											key 	: 'sip_disable_enter_key',
											checked	: (props.attributes.disable_enter_key == 1),
											onChange: function(evt){
												props.setAttributes(
													{disable_enter_key: (evt.target.checked ? 1 : 0)}
												);
											},
										},
									),
									el(
										'label',
										{
											key : 'sip_disable_enter_key_label',
										},
										'Disable enter key'
									),
									el( 'div', {key: 'sip_br', style:{marginBottom: '10px'}}),
									el(
										'input',
										{
											type 	: 'checkbox',
											key 	: 'sip_no_popup',
											checked	: (props.attributes.no_popup == 1),
											onChange: function(evt){
												props.setAttributes(
													{no_popup: (evt.target.checked ? 1 : 0)}
												);
											},
										},
									),
									el(
										'label',
										{
											key : 'sip_no_popup_label'
										},
										'Hide results pop-up (affects the search in current page only)'
									),
									el( 'div', {key: 'sip_br_2', style:{marginBottom: '10px'}}),
									el(
										'input',
										{
											type 	: 'checkbox',
											key 	: 'sip_exclude_hidden',
											checked	: (props.attributes.exclude_hidden == 1),
											onChange: function(evt){
												props.setAttributes(
													{exclude_hidden: (evt.target.checked ? 1 : 0)}
												);
											},
										},
									),
									el(
										'label',
										{
											key : 'sip_exclude_hidden_label'
										},
										'Exclude hidden terms on page (affects the search in current page only)'
									),
									el( 'div', {key: 'sip_br_3', style:{marginBottom: '10px'}}),
									el(
										'input',
										{
											type 	: 'checkbox',
											key 	: 'sip_display_button',
											checked	: (props.attributes.display_button == 1),
											onChange: function(evt){
												props.setAttributes(
													{display_button: (evt.target.checked ? 1 : 0)}
												);
											},
										},
									),
									el(
										'label',
										{
											key : 'sip_display_button_label'
										},
										'Display the search button (affects the search in current page only)'
									),
								]
							)
						)
					);
				}

				return 	children;
			},

			save: function( props ) {
				var shortcode = '[search-in-place-form';
                if(props.attributes.placeholder && props.attributes.placeholder.length)
                    shortcode += ' placeholder="'+props.attributes.placeholder.replace(/"/g, '\"')+'"';
				if(props.attributes.search_in_page) shortcode += ' in_current_page="1"';
				if(props.attributes.disable_enter_key) shortcode += ' disable_enter_key="1"';
				if(props.attributes.no_popup) shortcode += ' no_popup="1"';
				if(props.attributes.exclude_hidden) shortcode += ' exclude_hidden_terms="1"';
				if(props.attributes.display_button) shortcode += ' display_button="1"';
				shortcode += ']';
				return el( 'div', null, shortcode );
			}
		});
	} )(
		window.wp.blocks,
		window.wp.element
	);
});