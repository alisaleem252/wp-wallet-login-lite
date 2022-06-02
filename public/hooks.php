<?php
use Elliptic\EC;
use kornrunner\Keccak;

/**
 * Lock User
 */
add_action('wp','wpwlc_restrict_user');
function wpwlc_restrict_user(){
    global $post;
    if(is_admin())
    return;

    $limit_access = get_post_meta($post->ID,'wplmc_limit_access',true);
    if($limit_access != '1')
    return;
    
    if(is_user_logged_in()) {
        $user = wp_get_current_user();
        $selected_users = get_post_meta($post->ID,'wplmc_users_access',true);
        if(in_array($user->ID,$selected_users))
        return;
        else {
            wp_redirect(wp_login_url());
            exit;
        }
    }
    else {
        wp_redirect(wp_login_url());
        exit;
    }

}

 add_action('wp_ajax_connect_wallet','connect_wallet_ajax_cb');
 add_action('wp_ajax_nopriv_connect_wallet','connect_wallet_ajax_cb');
 function connect_wallet_ajax_cb(){
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    global $wpdb;
    require_once wpwlc_PATH."/lib/Keccak/Keccak.php";
    require_once wpwlc_PATH."/lib/Elliptic/EC.php";
    require_once wpwlc_PATH."/lib/Elliptic/Curves.php";
    //require_once wpwlc_PATH."/lib/JWT/jwt_helper.php";
    //$GLOBALS['JWT_secret'] = '4Eac8AS2cw84easd65araADX';

    $data = json_decode(file_get_contents("php://input"));
    $request = $data->request;

    // Create a standard of eth address by lowercasing them
    // Some wallets send address with upper and lower case characters
    if (!empty($data->address)) {
    $data->address = strtolower($data->address);
    }

    if ($request == "login") {
        $address = $data->address;
        $balance = $data->balance;

        // Prepared statement to protect against SQL injections
        // $stmt = $conn->prepare("SELECT nonce FROM $tablename WHERE address = ?");
        // $stmt->bindParam(1, $address);
        // $stmt->execute();
        // $nonce = $stmt->fetchColumn();
        $usermeta = $wpdb->get_results("SELECT b.meta_value FROM `".$wpdb->prefix."usermeta` as a, `".$wpdb->prefix."usermeta` as b  WHERE a.meta_key LIKE 'wpwlc_address' AND a.meta_value LIKE '$address' AND b.meta_key LIKE 'wpwlc_nonce'  AND a.user_id LIKE b.user_id");
        


            

        if (isset($usermeta[0])) {
            $nonce = $usermeta[0]->meta_value;
            // If user exists, return message to sign
            echo("Sign this message to validate that you are the owner of the account. Random string: " . $nonce);
        }
        else {
            // If user doesn't exist, register new user with generated nonce, then return message to sign
            $nonce = uniqid();
            $user_id = wp_create_user( $address, wp_generate_password() );
            update_user_meta($user_id,'wpwlc_address',$address);
            update_user_meta($user_id,'wpwlc_nonce',$nonce);
            if($balance)
            update_user_meta($user_id,'wpwlc_balance',$balance);
            
            // Prepared statement to protect against SQL injections
            //$stmt = $conn->prepare("INSERT INTO $tablename (address, nonce) VALUES (?, ?)");
            //$stmt->bindParam(1, $address);
            //$stmt->bindParam(2, $nonce);

            if (!is_wp_error( $user_id ) ) {
            echo ("Sign this message to validate that you are the owner of the account. Random string: " . $nonce);
            } else {
            echo "Error" . $user_id->get_error_message();
            }
        }

        exit;
    }

    if ($request == "auth") {
        $address = $data->address;
        $signature = $data->signature;

        // Prepared statement to protect against SQL injections
        $usermeta = $wpdb->get_results("SELECT b.meta_value FROM `".$wpdb->prefix."usermeta` as a, `".$wpdb->prefix."usermeta` as b  WHERE a.meta_key LIKE 'wpwlc_address' AND a.meta_value LIKE '$address' AND b.meta_key LIKE 'wpwlc_nonce' AND a.user_id LIKE b.user_id");
        if(isset($usermeta[0])) {
            
            $nonce = $usermeta[0]->meta_value;
            $message = "Sign this message to validate that you are the owner of the account. Random string: " . $nonce;
        }

        // Check if the message was signed with the same private key to which the public address belongs
        function pubKeyToAddress($pubkey) {
            return "0x" . substr(Keccak::hash(substr(hex2bin($pubkey->encode("hex")), 1), 256), 24);
        }

        function verifySignature($message, $signature, $address) {
            $msglen = strlen($message);
            $hash   = Keccak::hash("\x19Ethereum Signed Message:\n{$msglen}{$message}", 256);
            $sign   = ["r" => substr($signature, 2, 64),
                    "s" => substr($signature, 66, 64)];
            $recid  = ord(hex2bin(substr($signature, 130, 2))) - 27;
            if ($recid != ($recid & 1))
                return false;

            $ec = new EC('secp256k1');
            $pubkey = $ec->recoverPubKey($hash, $sign, $recid);

            return $address == pubKeyToAddress($pubkey);
        }

        // If verification passed, authenticate user
        if (verifySignature($message, $signature, $address)) {

         //   $stmt = $conn->prepare("SELECT publicName FROM $tablename WHERE address = ?");
           // $stmt->bindParam(1, $address);
           // $stmt->execute();
           // $publicName = $stmt->fetchColumn();
            $usermeta = $wpdb->get_results("SELECT b.* FROM `".$wpdb->prefix."usermeta` as a, `".$wpdb->prefix."usermeta` as b  WHERE a.meta_key LIKE 'wpwlc_address' AND a.meta_value LIKE '$address' AND b.meta_key LIKE 'nickname' AND a.user_id LIKE b.user_id");    
            
            $user_id = $usermeta[0]->user_id;
            $publicName = $usermeta[0]->meta_value;
            $publicName = htmlspecialchars($publicName, ENT_QUOTES, 'UTF-8');

            // Create a new random nonce for the next login
            $nonce = uniqid();
            update_user_meta($user_id,'wpwlc_nonce',$nonce);
            //$sql = "UPDATE $tablename SET nonce = '".$nonce."' WHERE address = '".$address."'";
            //$conn->query($sql);

            // Create JWT Token
            //$token = array();
            //$token['address'] = $address;
            //$JWT = JWT::encode($token, $GLOBALS['JWT_secret']);

            /**Login the user */
             clean_user_cache( $user_id );
             wp_clear_auth_cookie();
             wp_set_current_user( $user_id );
             wp_set_auth_cookie( $user_id, false );
             update_user_caches( get_user_by('ID',$user_id) );


            echo(json_encode(["Success", $publicName]));
        } else {
            echo "Fail";
        }
        exit;
    }

    if ($request == "updatePublicName") {
    $publicName = $data->publicName;
    $address = $data->address;

    // Check if the user is logged in
    if(!is_user_logged_in()){
        //$JWT = JWT::decode($data->JWT, $GLOBALS['JWT_secret']); 
        echo 'Authentication error'; exit; 
    }
    

    // Prepared statement to protect against SQL injections
    $usermeta = $wpdb->get_results("SELECT b.* FROM `".$wpdb->prefix."usermeta` as a, `".$wpdb->prefix."usermeta` as b  WHERE a.meta_key LIKE 'wpwlc_address' AND a.meta_value LIKE '$address' AND b.meta_key LIKE 'nickname' AND a.user_id LIKE b.user_id");                
    $user_id = $usermeta[0]->user_id;
    //$stmt = $conn->prepare("UPDATE $tablename SET publicName = ? WHERE address = '".$address."'");
    //$stmt->bindParam(1, $publicName);
        update_user_meta($user_id,'nickname',true);
    if (isset($usermeta[0])) {
        echo "Public name for $address updated to $publicName";
    }


    exit;
    }

     die();
 }


 add_action('wp_head','wpwlc_wp_head');
 function wpwlc_wp_head(){
     ?>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
    <!-- <script type="text/javascript" src="https://unpkg.com/web3modal@1.9.0/dist/index.js"></script> -->
    <script type="text/javascript" src="https://unpkg.com/web3modal@1.9.5/dist/index.js"></script>
    <script type="text/javascript" src="https://unpkg.com/@walletconnect/web3-provider@1.2.1/dist/umd/index.min.js"></script>
    <!-- <script type="text/javascript" src="https://www.unpkg.com/walletlink@2.5.0/dist/provider/Web3Provider.js"></script> -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/fortmatic/dist/fortmatic.js"></script>
    <script type="text/javascript" src="https://unpkg.com/@toruslabs/torus-embed"></script>
    <script type="text/javascript" src="https://unpkg.com/@portis/web3@4.0.7/umd/index.js"></script>
    

    
    <?php
 }

 add_action('wp_footer','wpwlc_wp_footer');
 function wpwlc_wp_footer(){
    $wallet_connect_options = get_option( 'wallet_connect_option_name' ); // Array of All Options
    $fortmatic_rpcurl_0 = $wallet_connect_options['fortmatic_rpcurl_0']; // Fortmatic rpcURL
    $fortmatic_chainid_1 = $wallet_connect_options['fortmatic_chainid_1']; // Fortmatic chainID
    $fortmatic_key_2 = $wallet_connect_options['fortmatic_key_2']; // Fortmatic Key
    $wallet_connect_infuraid_3 = $wallet_connect_options['wallet_connect_infuraid_3']; // Wallet Connect infuraId
    $portis_id_4 = $wallet_connect_options['portis_id_4']; // Portis ID

    
     ?>
    <script>
        var fortmatic_rpcurl_0 = "<?php echo $fortmatic_rpcurl_0 ? $fortmatic_rpcurl_0 : 'https://rpc-mainnet.maticvigil.com' ?>";
        var fortmatic_chainid_1 = "<?php echo $fortmatic_chainid_1 ? $fortmatic_chainid_1 : '137' ?>";
        var fortmatic_key_2 = "<?php echo $fortmatic_key_2 ?  $fortmatic_key_2 : 'pk_test_34280F77D49163DC' ?>"; 
        var wallet_connect_infuraid_3 = "<?php echo $wallet_connect_infuraid_3 ? $wallet_connect_infuraid_3 : '8043bb2cf99347b1bfadfb233c5325c0' ?>";
        var portis_id_4 = "<?php echo $portis_id_4 ? $portis_id_4 : 'PORTIS_ID' ?>";
        </script>
     <script>var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>'; </script>
    <script src="<?php echo wpwlc_URL?>/js/web3-login.js?v=009"></script>
		<script src="<?php echo wpwlc_URL?>/js/web3-modal.js?v=0011"></script>
    <?php
 }


 add_action('wp_ajax_xumm_connect_wallet','xumm_connect_wallet_ajax_cb');
 add_action('wp_ajax_nopriv_xumm_connect_wallet','xumm_connect_wallet_ajax_cb');
 function xumm_connect_wallet_ajax_cb(){
     global $wpdb;
    // ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
     
    $entityBody = file_get_contents('php://input');
    $userToken = json_decode($entityBody,true);
    $uuid = $_POST['uuid'];
    $auth = $_POST['auth'];
    
    if(isset($userToken['userToken']['user_token'])){
        update_option('xumm_pending_login',$entityBody);
    }
    
//die(get_option('xumm_pending_login'));
    if(isset($_REQUEST['auth']) && isset($_REQUEST['uuid'])){
        $entityBody = get_option('xumm_pending_login');
        $userToken = json_decode($entityBody,true);
        if($uuid !== $userToken['payloadResponse']['payload_uuidv4']){
            die('NULLID');
        }
        $userToken = $userToken['userToken']['user_token'];
        
        $usermeta = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."usermeta` as a, `".$wpdb->prefix."usermeta` as b  WHERE a.meta_key LIKE 'wpwlc_address' AND a.meta_value LIKE '$userToken' AND b.meta_key LIKE 'wpwlc_nonce'  AND a.user_id LIKE b.user_id");
         
            if (isset($usermeta[0])) {
                $user_id =  $usermeta[0]->user_id;
                xumm_login_the_user_by_id($user_id);
                die($user_id);
            } else {
            /**
             * Create this new user
             */
            $user_id = wp_create_user( $userToken, wp_generate_password() );
            update_user_meta($user_id,'wpwlc_address',$userToken);
            update_user_meta($user_id,'wpwlc_nonce',$userToken);
            xumm_login_the_user_by_id($user_id);
            die($user_id);
            }
        
        
    }
    die('NULL');

 }
 function xumm_login_the_user_by_id($user_id){
     /**Login the user */
        clean_user_cache( $user_id );
        wp_clear_auth_cookie();
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id, false );
        update_user_caches( get_user_by('ID',$user_id) );
        //wp_redirect(site_url());
        return;
 }