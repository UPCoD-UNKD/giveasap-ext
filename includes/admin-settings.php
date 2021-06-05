<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VLSettingsPage {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Simple Giveaways Web Hook',
            'manage_options',
            'giveasap-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'giveasap_webhook' );
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'giveasap-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {
        register_setting(
            'my_option_group', // Option group
            'giveasap_webhook', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Simple Giveaways Web Hook', // Title
            false, // Callback
            'giveasap-setting-admin' // Page
        );

        add_settings_field(
            'url',
            'Url',
            array( $this, 'url_callback' ),
            'giveasap-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();

        if ( isset( $input['url'] ) ) {
            $new_input['url'] = sanitize_text_field( $input['url'] );
        }

        return $new_input;
    }


    /**
     * Get the settings option array and print one of its values
     */
    public function url_callback() {
        printf(
            '<textarea id="url" name="giveasap_webhook[url]" style="min-width: 400px">%s</textarea>',
            isset( $this->options['url'] ) ? esc_attr( $this->options['url'] ) : ''
        );
    }
}

if ( is_admin() ) {
    $my_settings_page = new VLSettingsPage();
}


function vl_update_report_page( $report ) {
    if ( strpos( home_url(), '.dev' ) ) {
        $page               = get_page_by_title( 'report' );
        $page->post_content = $report;

        wp_update_post( $page );
    }
}
