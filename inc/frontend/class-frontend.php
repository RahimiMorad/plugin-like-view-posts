<?php

namespace Like_Post_WPsultan\Inc\Frontend;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @see       https://wp-sultan.comcomcom
 * @since      1.0.0
 *
 * @author    Your Name or Your Company
 */
class Frontend
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the ID of this plugin
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the current version of this plugin
     */
    private $version;

    /**
     * The text domain of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the text domain of this plugin
     */
    private $plugin_text_domain;

    /**
     * Initialize the class and set its properties.
     *
     * @since       1.0.0
     *
     * @param string $plugin_name        the name of this plugin
     * @param string $version            the version of this plugin
     * @param string $plugin_text_domain the text domain of this plugin
     */
    public function __construct($plugin_name, $version, $plugin_text_domain)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /*
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__).'css/like-post-wpsultan-frontend.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /*
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__).'js/like-post-wpsultan-frontend.js', array('jquery'), $this->version, false);
    }

    public function is_post_like($content)
    {
        if (is_singular('post')) {
            ob_start(); ?>
			<ul class="likes">
				<li class="likes__item likes__item--like">
					<a href="<?php echo add_query_arg('post_action', 'like'); ?>">
					<?php esc_html_e('LIKE', 'like-post-wpsultan'); ?> (<?php echo $this->ip_get_like_count('likes'); ?>)
					</a>
				</li>
				<li class="likes__item likes__item--dislike">
					<a href="<?php echo add_query_arg('post_action', 'dislike'); ?>">
						<?php esc_html_e('Dislike', 'like-post-wpsultan'); ?> (<?php echo $this->ip_get_like_count('dislikes'); ?>)
					</a>
				</li>
			</ul>
<?php

            $output = ob_get_clean();

            return  $content.$output;
        } else {
            return $content;
        }
    }

    public function ip_get_like_count($type = 'likes')
    {
        $current_count = get_post_meta(get_the_id(), $type, true);

        return $current_count ? $current_count : 0;
    }

    public function ip_process_like()
    {
        $processed_like = false;
        $redirect = false;

        // Check if like or dislike
        if (is_singular('post')) {
            if (isset($_GET['post_action'])) {
                if ($_GET['post_action'] == 'like') {
                    // Like
                    $like_count = get_post_meta(get_the_id(), 'likes', true);

                    if ($like_count) {
                        $like_count = $like_count + 1;
                    } else {
                        $like_count = 1;
                    }

                    $processed_like = update_post_meta(get_the_id(), 'likes', $like_count);
                } elseif ($_GET['post_action'] == 'dislike') {
                    // Dislike
                    $dislike_count = get_post_meta(get_the_id(), 'dislikes', true);

                    if ($dislike_count) {
                        $dislike_count = $dislike_count + 1;
                    } else {
                        $dislike_count = 1;
                    }

                    $processed_like = update_post_meta(get_the_id(), 'dislikes', $dislike_count);
                }

                if ($processed_like) {
                    $redirect = get_the_permalink();
                }
            }
        }

        // Redirect
        if ($redirect) {
            wp_redirect($redirect);
            die;
        }
    }
}
