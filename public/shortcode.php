<?php
add_action('wp_enqueue_scripts',
function(){
wp_enqueue_script("jquery");
});

    add_shortcode('connect_wallet','connect_wallet_cb');
    function connect_wallet_cb(){
        $wallet_connect_options = get_option( 'wallet_connect_option_name' ); // Array of All Options
        $button_classes = $wallet_connect_options['button_classes_5']; // Button Classes
        ob_start(); ?>

    <div style="margin: 0 auto;max-width: 600px;margin-top:100px;">
        <div style="text-align:center;word-wrap:break-word;">
            <?php if(is_user_logged_in()) {
                $user = wp_get_current_user();
                $address = get_user_meta($user->ID,'wpwlc_address',true);
                
                ?>
                <div id="loggedIn" class="user-login-msg">
                    Successful authentication for address:<br><span id="ethAddress"><?php echo $address ?></span>
                    <br><br>
                    You can set a public name for this account:<br>
                    <input type="text" placeholder="Public name" id="updatePublicName" onfocusout="setPublicName()" style="width:190px;">
                </div>
            <?php } //if(is_user_logged_in()) {
                else{?>  
                <button type="button" onclick="userLoginOut()" id="buttonText" class="<?php echo $button_classes ?> button button-secondary">Connect Wallet</button><div><p>&nbsp;</p></div>
            <?php } // ELSE  of   if(is_user_logged_in()) {?>
        </div>
    </div>
    
     <?php

     $ob_str=ob_get_contents();
     ob_end_clean();
     return $ob_str;
 } //shortcode Callback