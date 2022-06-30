<?php

/**
 * Generated by the WordPress Meta Box Generator
 * https://jeremyhixon.com/tool/wordpress-meta-box-generator/
 * 
 * Retrieving the values:
 * Select Users = get_post_meta( get_the_ID(), 'wpwlcselect-users', true )
 */
class wpwlc_Page_lock {
	private $config = '{"title":"Page Access","description":"Select Users to allow them to access this page.","prefix":"wpwlc","domain":"wpwalletlogincustom","class_name":"wpwlc_Page_lock","post-type":["page"],"context":"normal","priority":"default","fields":[{"type":"select","label":"Select Users","options":"123 : username1\r\n234 : username2\r\n345 : username3","id":"wpwlcselect-users"}]}';

	public function __construct() {
		$this->config = json_decode( $this->config, true );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
	}

	public function add_meta_boxes() {
		foreach ( $this->config['post-type'] as $screen ) {
			add_meta_box(
				sanitize_title( $this->config['title'] ),
				$this->config['title'],
				[ $this, 'add_meta_box_callback' ],
				$screen,
				$this->config['context'],
				$this->config['priority']
			);
		}
	}

	public function admin_head() {
		global $typenow;
		if ( in_array( $typenow, $this->config['post-type'] ) ) {
			?><?php
		}
	}

	public function save_post( $post_id ) {
        $wplmc_users_access =  isset( $_POST[ 'wplmc_users_access' ] ) ? sanitize_text_field($_POST[ 'wplmc_users_access' ]) : array();
		$wplmc_limit_access =  isset( $_POST[ 'wplmc_limit_access' ] ) ? sanitize_text_field($_POST[ 'wplmc_limit_access' ]) : 0;
		update_post_meta( $post_id, 'wplmc_users_access', $wplmc_users_access);
        update_post_meta( $post_id, 'wplmc_limit_access', $wplmc_limit_access);
					
	}

	public function add_meta_box_callback() {
        global $post;
        $selected_users = get_post_meta($post->ID,'wplmc_users_access',true);
		$selected_users = is_array($selected_users) ? $selected_users : array();
        $limit_access = get_post_meta($post->ID,'wplmc_limit_access',true);?>
        <p><input type="checkbox" value="1" <?php echo ($limit_access == '1' ? 'checked' : '')?> name="wplmc_limit_access" /> <?php esc_html_e("Restrict Page Access",'wpwalletlogincustom')?></p>
        <div class="rwp-description" style="height: 200px;overflow: scroll;"><?php echo $this->config['description'] ?>
        <?php
        $users = get_users();
        foreach($users as $user){?>
            <p><input name="wplmc_users_access[]" type="checkbox" value="<?php echo $user->ID ?>" <?php echo (in_array($user->ID,$selected_users) ? 'checked' : '') ?> /> <?php echo $user->data->user_nicename ?></p>
    <?php } ?>
    	</div>
	<?php
	}

	

}
new wpwlc_Page_lock;