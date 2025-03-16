<?php
/**
 * Plugin Name: Content Protection Desheecode
 * Plugin URI: https://wordpress.org/plugins/content-protection-desheecode/
 * Description: Protect your website content with various security features like right-click disable, text copy protection, and image download prevention
 * Version: 1.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Deshee Code
 * Author URI: https://profiles.wordpress.org/desheecode/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: content-protection-desheecode
 * Domain Path: /languages
 */

// डायरेक्ट एक्सेस को रोकें
if (!defined('ABSPATH')) {
    exit;
}

/**
 * प्लगइन का मुख्य क्लास
 */
class SecurityFeaturesPlugin {
    /**
     * प्लगइन के सभी ऑप्शन्स
     */
    private $plugin_options = [
        'disable_right_click_enabled' => 'off',
        'disable_text_selection_enabled' => 'off',
        'disable_image_download_enabled' => 'off',
        'disable_ctrl_s_enabled' => 'off',
        'disable_ctrl_u_enabled' => 'off',
        'disable_ctrl_p_enabled' => 'off',
        'disable_watermark_enabled' => 'off',
        'watermark_text' => 'Protected Content',
        'disable_f12_enabled' => 'off',
        'disable_print_screen_enabled' => 'off',
        'disable_ctrl_shift_i_enabled' => 'off',
        'disable_ctrl_c_enabled' => 'off',
        'show_error_messages' => 'off',
        'right_click_message' => 'Right click is disabled',
        'copy_message' => 'Copying is disabled',
        'print_screen_message' => 'Screenshot is disabled',
        'dev_tools_message' => 'Developer tools are disabled',
        'error_message_background' => '#ff0000',
        'error_message_text_color' => '#ffffff',
        'error_message_font_size' => '16',
        'error_message_padding' => '15',
        'error_message_border_radius' => '5',
        'error_message_duration' => '2000',
        'error_message_position' => 'center',
        'error_message_distance' => '20'
    ];

    /**
     * कंस्ट्रक्टर - सभी हुक्स को रजिस्टर करें
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * एडमिन मेनू में सेटिंग्स पेज जोड़ें
     */
    public function add_admin_menu() {
        add_options_page(
            __('Content Protection Desheecode Settings', 'content-protection-desheecode'),
            __('Content Protection Desheecode', 'content-protection-desheecode'),
            'manage_options',
            'content-protection-desheecode',
            [$this, 'render_settings_page']
        );
    }

    /**
     * सभी सेटिंग्स को रजिस्टर करें
     */
    public function register_settings() {
        // Register checkbox settings
        register_setting('content-protection-desheecode-group', 'disable_right_click_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_text_selection_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_image_download_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_ctrl_s_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_ctrl_u_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_ctrl_p_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_watermark_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_f12_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_print_screen_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_ctrl_shift_i_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'disable_ctrl_c_enabled', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'show_error_messages', 'sanitize_text_field');

        // Register text message settings
        register_setting('content-protection-desheecode-group', 'watermark_text', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'right_click_message', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'copy_message', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'print_screen_message', 'sanitize_text_field');
        register_setting('content-protection-desheecode-group', 'dev_tools_message', 'sanitize_text_field');

        // Register color settings
        register_setting('content-protection-desheecode-group', 'error_message_background', 'sanitize_hex_color');
        register_setting('content-protection-desheecode-group', 'error_message_text_color', 'sanitize_hex_color');

        // Register number settings
        register_setting('content-protection-desheecode-group', 'error_message_font_size', 'absint');
        register_setting('content-protection-desheecode-group', 'error_message_padding', 'absint');
        register_setting('content-protection-desheecode-group', 'error_message_border_radius', 'absint');
        register_setting('content-protection-desheecode-group', 'error_message_duration', 'absint');
        register_setting('content-protection-desheecode-group', 'error_message_distance', 'absint');

        // Register position setting
        register_setting('content-protection-desheecode-group', 'error_message_position', array($this, 'sanitize_position'));
    }

    /**
     * Sanitize position value
     *
     * @param string $position The position value to sanitize
     * @return string Sanitized position value
     */
    public function sanitize_position($position) {
        $allowed = array('center', 'top', 'bottom');
        return in_array($position, $allowed, true) ? $position : 'center';
    }

    /**
     * सेटिंग्स पेज का HTML रेंडर करें
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h2><?php esc_html_e('Content Protection Desheecode Settings', 'content-protection-desheecode'); ?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('content-protection-desheecode-group');
                do_settings_sections('content-protection-desheecode-group');
                ?>
                <table class="form-table">
                    <?php $this->render_settings_fields(); ?>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * सेटिंग्स फील्ड्स को रेंडर करें
     */
    private function render_settings_fields() {
        $fields = [
            'disable_right_click_enabled' => __('Disable Right Click', 'content-protection-desheecode'),
            'disable_text_selection_enabled' => __('Disable Text Selection', 'content-protection-desheecode'),
            'disable_image_download_enabled' => __('Disable Image Download', 'content-protection-desheecode'),
            'disable_ctrl_s_enabled' => __('Disable Ctrl+S Save', 'content-protection-desheecode'),
            'disable_ctrl_u_enabled' => __('Disable Ctrl+U Source View', 'content-protection-desheecode'),
            'disable_ctrl_p_enabled' => __('Disable Ctrl+P Print', 'content-protection-desheecode'),
            'disable_watermark_enabled' => __('Enable Watermark', 'content-protection-desheecode'),
            'watermark_text' => __('Watermark Text', 'content-protection-desheecode'),
            'disable_f12_enabled' => __('Disable F12 Developer Tools', 'content-protection-desheecode'),
            'disable_print_screen_enabled' => __('Disable Screenshot', 'content-protection-desheecode'),
            'disable_ctrl_shift_i_enabled' => __('Disable Ctrl+Shift+I Developer Tools', 'content-protection-desheecode'),
            'disable_ctrl_c_enabled' => __('Disable Ctrl+C Copy', 'content-protection-desheecode'),
            'show_error_messages' => __('Show Error Messages', 'content-protection-desheecode'),
            'right_click_message' => __('Right Click Error Message', 'content-protection-desheecode'),
            'copy_message' => __('Copy Error Message', 'content-protection-desheecode'),
            'print_screen_message' => __('Screenshot Error Message', 'content-protection-desheecode'),
            'dev_tools_message' => __('Developer Tools Error Message', 'content-protection-desheecode'),
            'error_message_background' => __('Error Message Background Color', 'content-protection-desheecode'),
            'error_message_text_color' => __('Error Message Text Color', 'content-protection-desheecode'),
            'error_message_font_size' => __('Error Message Font Size (px)', 'content-protection-desheecode'),
            'error_message_padding' => __('Error Message Padding (px)', 'content-protection-desheecode'),
            'error_message_border_radius' => __('Error Message Border Radius (px)', 'content-protection-desheecode'),
            'error_message_duration' => __('Error Message Duration (ms)', 'content-protection-desheecode'),
            'error_message_position' => __('Error Message Position', 'content-protection-desheecode'),
            'error_message_distance' => __('Error Message Distance (px)', 'content-protection-desheecode')
        ];

        foreach ($fields as $option => $label) {
            if (strpos($option, 'message') !== false && $option !== 'show_error_messages') {
                if (strpos($option, 'error_message_') === 0) {
                    if ($option === 'error_message_position') {
                        $this->render_position_field($option, $label);
                    } elseif (strpos($option, 'color') !== false) {
                        $this->render_color_field($option, $label);
                    } else {
                        $this->render_number_field($option, $label);
                    }
                } else {
                    $this->render_text_field($option, $label);
                }
            } else {
                $this->render_checkbox_field($option, $label);
            }
        }
    }

    /**
     * चेकबॉक्स फील्ड को रेंडर करें
     */
    private function render_checkbox_field($option, $label) {
        $value = get_option($option, 'off');
        ?>
        <tr>
            <th scope="row"><?php echo esc_html($label); ?></th>
            <td>
                <label class="switch">
                    <input type="checkbox" name="<?php echo esc_attr($option); ?>" <?php checked($value, 'on'); ?>>
                    <span class="slider round"></span>
                </label>
            </td>
        </tr>
        <?php
    }

    /**
     * टेक्स्ट फील्ड को रेंडर करें
     */
    private function render_text_field($option, $label) {
        $value = get_option($option, '');
        ?>
        <tr>
            <th scope="row"><?php echo esc_html($label); ?></th>
            <td>
                <input type="text" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
            </td>
        </tr>
        <?php
    }

    /**
     * कलर फील्ड को रेंडर करें
     */
    private function render_color_field($option, $label) {
        $value = get_option($option, '#ff0000');
        ?>
        <tr>
            <th scope="row"><?php echo esc_html($label); ?></th>
            <td>
                <input type="color" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_attr($value); ?>">
            </td>
        </tr>
        <?php
    }

    /**
     * नंबर फील्ड को रेंडर करें
     */
    private function render_number_field($option, $label) {
        $value = get_option($option, '');
        $min = 0;
        $max = $option === 'error_message_duration' ? 10000 : 100;
        ?>
        <tr>
            <th scope="row"><?php echo esc_html($label); ?></th>
            <td>
                <input type="number" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_attr($value); ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" class="regular-text">
            </td>
        </tr>
        <?php
    }

    /**
     * पोजीशन फील्ड को रेंडर करें
     */
    private function render_position_field($option, $label) {
        $value = get_option($option, 'center');
        ?>
        <tr>
            <th scope="row"><?php echo esc_html($label); ?></th>
            <td>
                <select name="<?php echo esc_attr($option); ?>" class="regular-text">
                    <option value="center" <?php selected($value, 'center'); ?>><?php esc_html_e('Center', 'content-protection-desheecode'); ?></option>
                    <option value="top" <?php selected($value, 'top'); ?>><?php esc_html_e('Top', 'content-protection-desheecode'); ?></option>
                    <option value="bottom" <?php selected($value, 'bottom'); ?>><?php esc_html_e('Bottom', 'content-protection-desheecode'); ?></option>
                </select>
            </td>
        </tr>
        <?php
    }

    /**
     * फ्रंट-एंड स्क्रिप्ट्स और स्टाइल्स को लोड करें
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        
        wp_enqueue_script(
            'content-protection-desheecode',
            plugins_url('js/disable-functions.js', __FILE__),
            ['jquery'],
            '1.0',
            true
        );

        // JavaScript वेरिएबल्स को पास करें
        wp_localize_script('content-protection-desheecode', 'disableFunctions', [
            'rightClick' => get_option('disable_right_click_enabled', 'off'),
            'textSelection' => get_option('disable_text_selection_enabled', 'off'),
            'imageDownload' => get_option('disable_image_download_enabled', 'off'),
            'ctrlS' => get_option('disable_ctrl_s_enabled', 'off'),
            'ctrlU' => get_option('disable_ctrl_u_enabled', 'off'),
            'ctrlP' => get_option('disable_ctrl_p_enabled', 'off'),
            'watermark' => get_option('disable_watermark_enabled', 'off'),
            'watermarkText' => get_option('watermark_text', __('Protected Content', 'content-protection-desheecode')),
            'f12' => get_option('disable_f12_enabled', 'off'),
            'printScreen' => get_option('disable_print_screen_enabled', 'off'),
            'ctrlShiftI' => get_option('disable_ctrl_shift_i_enabled', 'off'),
            'ctrlC' => get_option('disable_ctrl_c_enabled', 'off'),
            'showErrors' => get_option('show_error_messages', 'off'),
            'messages' => [
                'rightClick' => get_option('right_click_message', __('Right click is disabled', 'content-protection-desheecode')),
                'copy' => get_option('copy_message', __('Copying is disabled', 'content-protection-desheecode')),
                'printScreen' => get_option('print_screen_message', __('Screenshot is disabled', 'content-protection-desheecode')),
                'devTools' => get_option('dev_tools_message', __('Developer tools are disabled', 'content-protection-desheecode'))
            ],
            'errorStyle' => [
                'background' => get_option('error_message_background', '#ff0000'),
                'textColor' => get_option('error_message_text_color', '#ffffff'),
                'fontSize' => get_option('error_message_font_size', '16'),
                'padding' => get_option('error_message_padding', '15'),
                'borderRadius' => get_option('error_message_border_radius', '5'),
                'duration' => get_option('error_message_duration', '2000'),
                'position' => get_option('error_message_position', 'center'),
                'distance' => get_option('error_message_distance', '20')
            ]
        ]);

        // वॉटरमार्क स्टाइल्स
        if (get_option('disable_watermark_enabled', 'off') === 'on') {
            wp_enqueue_style(
                'content-protection-desheecode',
                plugins_url('css/style.css', __FILE__),
                [],
                '1.0'
            );
        }
    }
}

// प्लगिन को इनिशियलाइज करें
$security_features = new SecurityFeaturesPlugin();

/**
 * नए फीचर्स को जोड़ने के लिए फिल्टर्स और एक्शन्स
 * उदाहरण:
 * add_filter('content-protection-desheecode_watermark_text', function($text) {
 *     return 'My Custom Watermark';
 * });
 */
