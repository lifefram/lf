<?php


echo '<h1>Hi....</h1><pre>';

if(is_array($ret)) print_r($ret);
else var_dump($ret);

echo '</pre>';


?>

<style>

	#mg_container {
        border: solid 1px black;
        background: blanchedalmond;
	}

	#mg_container > #mg_status {
        font-size: 2em;
        display: block;
        padding: 5px;
        margin: 10px;
	}

	#mg_container > #mg_log {
        padding: 10px 5px;
        background: aliceblue;
	}

	#mg_container > #mg_log > span {
        display: block;
        background: lightgreen;
        margin: 5px;
        padding: 3px;
        font-size: 1.3em;
	}

</style>

<button id="clk_test">Test me...</button>
<div>!!</div>

<br>
<br>
<br>
<div id="mg_container">

	<div id="mg_status"><?php echo $ret['status'] ?></div>

	<div id="mg_log">

		<?php

		foreach($ret['log'] as $item) {

			echo '<span>'.$item.'</span>';
		}

		?>

	</div>
</div>

<script>

    let logCont;

    jQuery(document).on('click', '#clk_test', function (ev) {
        ev.preventDefault();

        jQuery('#mg_log').find('span:last').text('Found and changed....');

    });


    function addOutput($msg) {

        if(!logCont) {

            logCont = jQuery('#mg_log')
        }

        logCont.append('<span>'+$msg+'</span>');
    }

    function addLogs($arr) {

        if(!logCont) {
            logCont = jQuery('#mg_log')
        }

        console.log('>> ', $arr);

        $arr.forEach(function (item, idx) {
            logCont.append('<span>'+item+'</span>');
        })
    }


</script>
