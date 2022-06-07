<?php 
if ( ! defined( 'ABSPATH' ) ) 
	exit; // Exit if accessed directly.


    class Connect_Wallet_Widget extends \Elementor\Widget_Base { 
  

        /**
       * Get widget name.
       *
       * Retrieve Connect_Wallet_Widget widget name.
       *
       * @since 1.0.0
       * @access public
       * @return string Widget name.
       */
      public function get_name() {
          return 'Connect_Wallet_Widget';
      }
  
  
      /**
       * Get widget title.
       *
       * Retrieve Connect_Wallet_Widget widget title.
       *
       * @since 1.0.0
       * @access public
       * @return string Widget title.
       */
      public function get_title() {
          return esc_html__( 'Connect Wallet Widget', 'wpwalletlogincustom' );
      }
  
      /**
       * Get widget icon.
       *
       * Retrieve Connect_Wallet_Widget widget icon.
       *
       * @since 1.0.0
       * @access public
       * @return string Widget icon.
       */
      public function get_icon() {
          return 'eicon-header';
      }
  
  
      /**
       * Get custom help URL.
       *
       * Retrieve a URL where the user can get more information about the widget.
       *
       * @since 1.0.0
       * @access public
       * @return string Widget help URL.
       */
      public function get_custom_help_url() {
          return 'https://essentialwebapps.com/category/elementor-tutorial/';
      }
  
      /**
       * Get widget categories.
       *
       * Retrieve the list of categories the Connect_Wallet_Widget widget belongs to.
       *
       * @since 1.0.0
       * @access public
       * @return array Widget categories.
       */
      public function get_categories() {
          return [ 'general' ];
      }
  
      /**
       * Get widget keywords.
       *
       * Retrieve the list of keywords the Connect_Wallet_Widget widget belongs to.
       *
       * @since 1.0.0
       * @access public
       * @return array Widget keywords.
       */
      public function get_keywords() {
          return ['connect', 'wallet'];
      }
  
  
  
      /**
       * Register Card widget controls.
       *
       * Add input fields to allow the user to customize the widget settings.
       *
       * @since 1.0.0
       * @access protected
       */
      protected function register_controls() { 
          // our control function code goes here.
  
          $this->start_controls_section(
              'content_section',
              [
                  'label' => esc_html__( 'Content', 'wpwalletlogincustom' ),
                  'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
              ]
          );
  
          $this->add_control(
              'Button_Background_Color',
              [
                  'label' => esc_html__( 'Button Background Color', 'wpwalletlogincustom' ),
                  'type' => \Elementor\Controls_Manager::COLOR,
                  'label_block' => true,
              ]
          );
  
  
          $this->add_control(
            'Button_Text_Color',
            [
                'label' => esc_html__( 'Button Text Color', 'wpwalletlogincustom' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'label_block' => true,
            ]
        );
  
          $this->end_controls_section();
  
      }
  
      /**
       * Render Card widget output on the frontend.
       *
       * Written in PHP and used to generate the final HTML.
       *
       * @since 1.0.0
       * @access protected
       */
      protected function render() { 
  
          // get our input from the widget settings.
          $settings = $this->get_settings_for_display();
  
          // get the individual values of the input
          $card_title = $settings['Button_Background_Color'];
          $card_description = $settings['Button_Text_Color'];
  
          ?>
  
          <!-- Start rendering the output -->
          <div class="card">
              <h3 class="card_title"><?php echo $card_title;  ?></h3>
              <p class= "card__description"><?php echo $card_description;  ?></p>
          </div>
          <!-- End rendering the output -->
  
          <?php
          
  
      }						
  
  
  }
?>