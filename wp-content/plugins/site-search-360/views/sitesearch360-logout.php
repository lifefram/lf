<?php 
if(!isset($requestUri)) {
    $requestUri = esc_url($_SERVER['REQUEST_URI']);
}
?>
<form id="ss360-logout" name="ss360_logout" method="post" action="<?php echo $requestUri; ?>" >
    <?php wp_nonce_field(); ?>
    <input type="hidden" name="action" value="ss360_logout">
    <button class="ss360-logout" type="submit"><?php esc_html_e('Log Out', 'site-search-360') ?></button>
</form>

<style type="text/css">
.ss360-logout {
    border: none;
    background: transparent;
    color: #c64624;
    text-decoration: underline;
    cursor: pointer;
    font-size: 16px;
}
</style>