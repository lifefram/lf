This repository contains WordPress theme and plugins that we used to publish and store biographical content. 

The main plugin that we use is [Advanced Classifieds & Directory Pro](https://wordpress.org/plugins/advanced-classifieds-and-directory-pro/) but has been heavily customized.

We use nft.storage to store files on IPFS. Visit [nft.storage](https://nft.storage) to sign-up for free and create your own API key. 

Then, insert your API key at wp-content\plugins\advanced-classifieds-and-directory-pro-premium\admin\partials\listings\acadp-admin-upload-files-display.php at approximately line 347 as shown below.

	jQuery.ajax({
		type: "POST",
		enctype: 'multipart/form-data',
		url: "https://api.nft.storage/upload",
		beforeSend: function(xhr) {
			xhr.setRequestHeader("Authorization", "Bearer <paste API key here>")
		},
		data: fd,
		...
		...

It includes a donate page after a content is submitted. This would assist you to seek out donations.
