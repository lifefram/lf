<?php // Blackhole for Bad Bots - Blackhole Template

if (!defined('ABSPATH')) exit;

function blackhole_template($ip, $host, $date, $whois, $vars) {

// BEGIN TEMPLATE

?><!doctype html>  
<html lang="en-US">
	<head>
		<meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noodp,noydir">
		<title>Welcome to Blackhole!</title>
		<style>
			body {
				margin: 0; padding: 0; 
				color: #fff; background-color: #851507; 
				font: 14px/1.5 Helvetica, Arial, sans-serif; 
				}
			div {
				width: 88%; max-width: 700px; margin: 20px auto; 
				}
			pre {
				margin: 25px 0 0 0; padding: 20px; border-radius: 10px; color: #fff; background-color: #b34334; 
				word-break: break-word; white-space: pre-line; overflow-wrap: break-word; word-wrap: break-word; 
				}
			h5 a {
				font-size: 11px; font-weight: normal; 
				color: #b34334; text-decoration: none;
				}
			a {
				color: #fff; 
				}
		</style>
	</head>
	<body>
		<div>
			<h1>You have fallen into a trap!</h1>
			<p>
				This site&rsquo;s <a href="/robots.txt">robots.txt</a> rules explicitly forbid your presence at this location. 
				Because you did not obey the robots.txt rules, further requests from your IP Address will be denied access.
				If you feel this is a mistake, you can access the site via proxy service and contact the administrator. 
			</p>
			<h2>Your Information</h2>
			<ul>
				<li><strong>IP Address:</strong> <?php echo $ip; ?></li>
				<li><strong>Host Name:</strong> <?php echo $host; ?></li>
			</ul>
			<pre>WHOIS Lookup for <?php echo $ip . "\n\n" . $date . "\n\n" . $whois; ?></pre>
			<h5><a target="_blank" rel="noopener noreferrer" href="https://perishablepress.com/blackhole-bad-bots/" title="Blackhole WordPress Plugin">Blackhole for Bad Bots</a></h5>
		</div>
	</body>
</html><?php

// END TEMPLATE

}