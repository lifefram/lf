## Introduction

This repository contains WordPress theme and plugins and IPFS content upload script that we used to publish and store biographical content. 

The main plugin that we use is [Advanced Classifieds & Directory Pro](https://wordpress.org/plugins/advanced-classifieds-and-directory-pro/) but has been heavily customized.

We use nft.storage to store files on IPFS. Visit [nft.storage](https://nft.storage) to sign-up for free and create your own API key. 

Then, insert your API key at wp-content\plugins\advanced-classifieds-and-directory-pro-premium\admin\partials\listings\acadp-admin-upload-files-display.php at approximately line 347 as shown below. You may also read [nft.storage docs here](https://nft.storage/docs/).

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

It includes a donate page after a content is submitted where you could award your donors with an NFT token containing the content itself, in the form of a JSON file and the image you uploaded.

NFT token minting and delivery is handled at the admin console during the pre-publication and storing phase. It has only been tested with [MetaMask](https://metamask.io/) wallet. Ensure you have one installed.

## Steps to Upload and Mint NFT Token

Coming soon!
<!--
* Log in to the admin panel.
* Click on the **Advanced Classifieds & Directory Pro** menu and edit one of the listing content (custom post type).
* On the content edit page, MetaMask will pop-up asking you to connect. Go ahead and connect your wallet.
* Edit your content as usual and then click on **Upload to IPFS** to upload the content (JSON and image) to IPFS via nft.storage (ensure you already inserted your API key)
*  -->
