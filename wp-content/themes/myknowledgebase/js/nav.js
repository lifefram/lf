/*
 * DISPLAY MOBILE NAV
 */

window.addEventListener('DOMContentLoaded', function() {
	// Hide mobile menu by default
	document.getElementById("mobile-nav").style.display = 'none';
	let sub = document.getElementById("mobile-nav").getElementsByClassName("sub-menu");
	for (let index = 0; index < sub.length; ++index) {
		sub[index].style.display = 'none';
	}

	// Display mobile menu when clicked
	let toggle = document.getElementById("mobile-nav-toggle");
	toggle.addEventListener('click', function() {
		let nav = document.getElementById("mobile-nav");
		nav.style.display = (nav.style.display == 'block') ? 'none' : 'block';
	});

	// Add toggle and display mobile submenu
	let a = document.getElementById("mobile-nav").querySelectorAll('.menu-item-has-children > a, .page_item_has_children > a');
	for( let index = 0; index < a.length; index++ ) {
		let button = document.createElement('button'); button.className = 'subnav-toggle'; button.innerHTML = '+';
		a[index].parentNode.insertBefore(button, a[index].nextSibling);
		button.addEventListener('click', function() {
			let sub = this.parentNode.getElementsByTagName("ul")[0];
			sub.style.display = (sub.style.display == 'block') ? 'none' : 'block';
		});
	};
});
