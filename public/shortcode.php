<?php
add_action('wp_enqueue_scripts',
function(){
wp_enqueue_script("jquery");
});
add_shortcode('connect_wallet','connect_wallet_cb');
 function connect_wallet_cb(){
    $wallet_connect_options = get_option( 'wallet_connect_option_name' ); // Array of All Options
    $button_classes = $wallet_connect_options['button_classes_5']; // Button Classes
    ob_start(); 







    ?>

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

    <?php }
    else { 
    
        $wallet_connect_options = get_option( 'wallet_connect_option_name' );

    $headers = array(
        'Content-Type' => 'application/json',
        'X-API-Key' => $wallet_connect_options['xumm_id_5'],//'ef6d02f3-86b1-44f0-b20a-af3b63716088',
        'X-API-Secret' => $wallet_connect_options['xumm_key_5']//'30beb485-897c-4d92-97fa-f01a0d55aede'
    );
    $body = [
        'txjson'  => array(
            'TransactionType' => 'SignIn',
        )
    ];
    $body = wp_json_encode($body);

    $response = wp_remote_post('https://xumm.app/api/v1/platform/payload', array(
        'method'    => 'POST',
        'headers'   => $headers,
        'body'      => $body
        )
    );
    $xumm_connect_a = json_decode($response['body'],true);
    $xumm_connect = $xumm_connect_a['next']['always'] ? $xumm_connect_a['next']['always'] : '';
     //echo '<pre>';
//print_r($xumm_connect_a);
//     var_dump(get_option('xumm_1'));
//     var_dump(json_decode(get_option('xumm_2'),true));
    
    ?>
    <script>
    function xumm_loginauth(){
        setTimeout(function () {
            jQuery.post("<?php echo admin_url('admin-ajax.php?action=xumm_connect_wallet&auth=1') ?>",
          {
            uuid: "<?php echo $xumm_connect_a['uuid'] ?>",
          },
          function(data, status){
            //console.log("Data: " + data + "\nStatus: " + status);
            if(data > 0){
            window.location.reload();
            }
          });
          xumm_loginauth();
  }, 5000)
    }
    xumm_loginauth();
    </script>
    <p>
    <a href="#" onclick="event.preventDefault();jQuery('#xumm_qr').show()" class="<?php echo $button_classes ?>">Connect Xumm</a>
    </p>
    <div id="xumm_qr" style="display:none"><img src="<?php echo $xumm_connect_a['refs']['qr_png']?>" /> 
    <p>Waiting for you to scan the SignIn request with the xumm app.</p></div>
    
    
    
    
    <div id="signTheMessage" style="display:none;" class="user-login-msg">
        Sign the message with your wallet to authenticate
    </div>
    <div id="loggedIn" style="display:none;" class="user-login-msg">
        Successful authentication for address:<br><span id="ethAddress"></span>
        <br><br>
        You can set a public name for this account:<br>
        <input type="text" placeholder="Public name" id="updatePublicName" onfocusout="setPublicName()" style="width:190px;">
    </div>

    <button onclick="userLoginOut()" id="buttonText" class="<?php echo $button_classes ?>">Connect Wallet</button>
    <?php } ?>
    </div>
    </div>
    <!-- <span style="font-size:19px;">
        <b>Web3 Passwordless User Authentication System</b>
    </span> -->
    <!-- <div style="height:5px;"></div> -->

    <!-- <div style="height:20px;"></div> -->

   

    <!-- <div style="height:60px;"></div> -->

    <!-- <div id="loggedOut" class="user-login-msg">
        Click the button to (sign up and) login!
    </div> -->
    <!-- <div id="needMetamask" style="display:none;color: rgb(255, 115, 0);" class="user-login-msg">
        To login, first install a Web3 wallet like the <a href="https://metamask.io/" style="color:#ff7300" target="_blank">MetaMask</a> browser extension or mobile app
    </div> -->
    <!-- <div id="needLogInToMetaMask" style="display:none;color: rgb(255, 115, 0);" class="user-login-msg">
        Log in to your wallet account first!
    </div> -->
    <!-- <div id="signTheMessage" style="display:none;" class="user-login-msg">
        Sign the message with your wallet to authenticate
    </div> -->
    <!-- <div id="loggedIn" style="display:none;" class="user-login-msg">
        Successful authentication for address:<br><span id="ethAddress"></span>
        <br><br>
        You can set a public name for this account:<br>
        <input type="text" placeholder="Public name" id="updatePublicName" onfocusout="setPublicName()" style="width:190px;">
    </div>
    <br> -->
    
    
    <!-- <div style="height:40px;"></div> -->

     <?php
     $ob_str=ob_get_contents();
     ob_end_clean();
     return $ob_str;
 } //shortcode Callback