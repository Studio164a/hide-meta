<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Hide_Meta_Admin' ) ) : 

/**
 * The Hide_Meta_Admin class adds the admin interface to change a post's hide meta setting and save the submitted values.
 * 
 * @return 	void
 * @since 	1.0.0
 */
class Hide_Meta_Admin {

	/**
	 * Private constructor. You can only create a class instance with the start() method below. 
	 *
	 * @return 	void
	 * @since 	1.0.0
	 */
	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Activates the class. 
	 *
	 * @return 	void
	 * @static
	 * @since 	1.0.0
	 */
	public static function start() {
		if ( 'hide_meta_start' !== current_filter() ) {
			return;
		}

		new Hide_Meta_Admin();
	}

	/**
     * Executes on the add_meta_boxes hook. 
     * 
     * @return  void
     * @since   1.0.0
     */
    public function add_meta_boxes() {
    	$post_types = apply_filters( 'hide_meta_post_types_supported', array( 'page', 'post' ) );

    	if ( ! is_array( $post_types ) ) {
    		return;
    	}

    	foreach ( $post_types as $post_type ) {
	        add_meta_box('hide_meta', __( 'Hide post/page meta', 'hide-meta' ), array( $this, 'hide_post_meta' ), $post_type );    		
    	}
    }

    /**
     * Hide post meta meta box.
     *
     * @return  void
     * @since   1.0.0
     */
    public function hide_post_meta($post) {
        // Use nonce for verification
        wp_nonce_field( 'hide_meta_save', '_hide_meta_nonce' );

        $value = get_post_meta( $post->ID, '_hide_meta', true );
        ?>
        <label for="_hide_meta">
            <?php _e( 'Hide post/page meta?', 'hide-meta' ) ?>
            <input type="checkbox" id="hide_meta" name="_hide_meta" <?php checked( $value ) ?>>
        </label>
        <?php
    }

    /**
     * Executes on the save_post hook. Used to save the custom meta. 
     * 
     * @return  void
     * @since   1.0.0
     */
    public function save_post($post_id, $post) {
        // Verify if this is an auto save routine. 
        // If it is our form has not been submitted, so we dont want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;        

        if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], apply_filters( 'hide_meta_post_types_supported', array( 'page', 'post' ) ) )  ) {
            // Verify this came from the our screen and with proper authorization,
            // because save_post can be triggered at other times
            if ( ! array_key_exists('_hide_meta_nonce', $_POST ) || !wp_verify_nonce( $_POST['_hide_meta_nonce'], 'hide_meta_save' ) ) {
                return;
            }

            // Ensure current user can edit pages
            if ( ! current_user_can( 'edit_page', $post_id ) && !current_user_can( 'edit_post', $post_id ) ) {
                return;
            }

            // Save custom fields found in our $settings variable
            $hide_post_meta = isset( $_POST['_hide_meta'] ) && $_POST['_hide_meta'] == 'on';

            update_post_meta( $post_id, '_hide_meta', $hide_post_meta );
        }
    }
}

endif; // End class_exists check