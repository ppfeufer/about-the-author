<?php
/**
 * Plugin Name: About The Author
 * Plugin URI: http://ppfeufer.de/wordpress-plugin/about-the-author/
 * Description: Provides a sidebarwidget with some information about the author of a blogarticle.
 * Version: 1.3
 * Author: H.-Peter Pfeufer
 * Author URI: http://ppfeufer.de
 * Text Domain: about-the-author
 * Domain Path: /l10n
 */

namespace WordPress\Plugins\AboutTheAuthor;

class About_The_Author extends \WP_Widget {
	private $var_sTextdomain;
	private $var_sFlattrLink;

	/**
	 * Cunstructor // Intit functions and actions
	 */
	function __construct() {
		$this->var_sTextdomain = 'about-the-author';
		$this->var_sFlattrLink = 'http://flattr.com/thing/601539/WordPress-Plugin-About-The-Author';

		if(\function_exists('load_plugin_textdomain')) {
			\load_plugin_textdomain($this->var_sTextdomain, \PLUGINDIR . '/' . \dirname(\plugin_basename(__FILE__)) . '/l10n', \dirname(\plugin_basename(__FILE__)) . '/l10n');
		}

		\add_image_size('about-the-author-userphoto', 250, 250, true);

		\add_action('admin_head', array(
			$this,
			'load_uploadscripts'
		));

		\add_action('wp_head', array(
			$this,
			'load_css'
		));

		\add_action('show_user_profile', array(
			$this,
			'userimage_in_profile'
		));

		\add_action('edit_user_profile', array(
			$this,
			'userimage_in_profile'
		));

		\add_action('profile_update', array(
			$this,
			'userimage_update'
		));

		$widget_ops = array(
			'classname' => 'about_the_author',
			'description' => \__('Provides a sidebarwidget with some information about the author of a blogarticle.', $this->var_sTextdomain)
		);

		$control_ops = array();

		parent::__construct('about_the_author', \__('About The Author', $this->var_sTextdomain), $widget_ops, $control_ops);
	} // END function About_The_Author()

	/**
	 * Widgetform
	 *
	 * @since 0.1
	 *
	 * @see WP_Widget::form()
	 */
	function form($instance) {
		$instance = \wp_parse_args((array) $instance, array(
			'about-the-author_title' => '',
			'about-the-author_imagesize' => '100'
		));

		// Titel
		echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . \__('Title', $this->var_sTextdomain) . '</strong></p>';
		echo '<p><input id="' . $this->get_field_id('about-the-author_title') . '" name="' . $this->get_field_name('about-the-author_title') . '" type="text" value="' . $instance['about-the-author_title'] . '" /></p>';
		echo '<p style="clear:both;"></p>';

		// Imagesize
		echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . \__('Imagesize (in Pixel)', $this->var_sTextdomain) . '</strong></p>';
		echo '<p><input id="' . $this->get_field_id('about-the-author_imagesize') . '" name="' . $this->get_field_name('about-the-author_imagesize') . '" type="text" value="' . $instance['about-the-author_imagesize'] . '" /></p>';
		echo '<p style="clear:both;"></p>';

		// Donate
		echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . \__('Like this Widget?', $this->var_sTextdomain) . '</strong></p>';
		echo '<p><a href="' . $this->var_sFlattrLink . '" target="_blank"><img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a></p>';
		echo '<p style="clear:both;"></p>';
	} // END function form($instance)

	/**
	 * Updating the widgetsettings
	 *
	 * @since 0.1
	 *
	 * @see WP_Widget::update()
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$new_instance = \wp_parse_args((array) $new_instance, array(
			'about-the-author_title' => '',
			'about-the-author_imagesize' => '100'
		));

		$instance['about-the-author_title'] = (string) \strip_tags($new_instance['about-the-author_title']);
		$instance['about-the-author_imagesize'] = '100';

		if(!empty($new_instance['about-the-author_imagesize'])) {
			$instance['about-the-author_imagesize'] = (string) \strip_tags($new_instance['about-the-author_imagesize']);
		}

		return $instance;
	} // END function update($new_instance, $old_instance)

	/**
	 * Display the widget in frontend
	 *
	 * @since 0.1
	 *
	 * @see WP_Widget::widget()
	 */
	function widget($args, $instance) {
		if(\is_singular()) {
			echo $args['before_widget'];

			$title = (empty($instance['about-the-author_title'])) ? '' : \apply_filters('widget_title', $instance['about-the-author_title']);

			if(!empty($title)) {
				echo $args['before_title'] . $title . $args['after_title'];
			} // END if(!empty($title))

			echo $this->about_the_author_output($instance, 'widget');
			echo $args['after_widget'];
		}
	} // END function widget($args, $instance)

	/**
	 * Generating the HTML
	 *
	 * @since 0.1
	 *
	 * @param unknown_type $args
	 * @param unknown_type $position
	 */
	private function about_the_author_output($args = array(), $position) {
		global $post;

		$obj_User = \get_user_by('id', $post->post_author);

		$array_Userphoto = \get_user_meta($post->post_author, 'userphoto');
		$array_UserDescription = \get_user_meta($post->post_author, 'description');

		echo '<ul class="about-the-author author-info clearfix"><li>';

		if(!empty($array_Userphoto['0']) || !empty($array_UserDescription['0'])) {
			if(isset($array_Userphoto['0'])) {
				echo '<img class="about-the-author author-photo" src="' . $array_Userphoto['0'] . '" alt="' . \__('Authorimage: ', $this->var_sTextdomain) . $obj_User->display_name . '" title="' . \__('Authorimage: ', $this->var_sTextdomain) . $obj_User->display_name . '" width="' . $args['about-the-author_imagesize'] . '" height="auto" />';
			} // END if(isset($array_Userphoto['0']))

			if(!empty($array_UserDescription['0'])) {
				echo '<span class="about-the-author author-name">' . $obj_User->display_name . '</span>';
				echo '<span class="about-the-author author-description">' . $array_UserDescription['0'] . '</span>';
			} // END if(isset($array_UserDescription['0']))
		} else {
			echo \__('We are sorry, but the author did\'t entered any information in his profile.', $this->var_sTextdomain);
		} // END if(isset($array_Userphoto['0']) || isset($array_UserDescription['0']))

		echo '</li></ul>';
	} // END private function about_the_author_output($args = array(), $position)

	/**
	 * Insert a field to userdetails to upload an author user photo.
	 *
	 * @since 0.1
	 */
	function userimage_in_profile() {
		global $current_screen;

		if($current_screen->id == 'profile' || $current_screen->id == 'user-edit') {
			global $profileuser;

			$array_UserPhoto = \get_user_meta($profileuser->ID, 'userphoto');
			$var_sUserPhoto = (isset($array_UserPhoto['0'])) ? $array_UserPhoto['0'] : '';

			echo '<h3>' . \__('Avatar', $this->var_sTextdomain) . '</h3>';
			echo '<table class="form-table">
					<tbody>
						<tr>
							<th><label for="user_login">' . \__('Your Photo', $this->var_sTextdomain) . '</label></th>
							<td>
								<input type="text" class="regular-text" value="' . $var_sUserPhoto . '" id="userphoto" name="userphoto">
								<input id="upload_userphoto_button" type="button" value="' . \__('Upload Image', $this->var_sTextdomain) . '" /><br />
								<span class="description">' . \__('Upload a foto for your user profile. If you don\'t want to use a photo, leave this field blank.', $this->var_sTextdomain) . '</span>
							</td>
						</tr>
					</tbody>
				</table>';
		} // END if($current_screen->id == 'profile' || $current_screen->id == 'user-edit')
	} // END function userimage_in_profile()

	/**
	 * Updating user_meta
	 *
	 * @since 0.1
	 */
	function userimage_update() {
		global $current_screen;

		if($current_screen->id == 'profile' || $current_screen->id == 'user-edit') {
			if(!empty($_REQUEST['userphoto'])) {
				$array_ImageMeta = $this->get_thumbnail_by_guid($_REQUEST['userphoto'], 'about-the-author-userphoto');

				if($array_ImageMeta) {
					$var_sUserPhoto = (string) $array_ImageMeta['url'];
				} else {
					$var_sUserPhoto = (string) $_REQUEST['userphoto'];
				} // END if($array_ImageMeta)

				\update_user_meta($_REQUEST['user_id'], 'userphoto', $var_sUserPhoto);
			} else {
				\delete_user_meta($_REQUEST['user_id'], 'userphoto');
			} // END if(!empty($_REQUEST['userphoto']))
		} // END if($current_screen->id == 'profile' || $current_screen->id == 'user-edit')
	} // END function userimage_update()

	/**
	 * Adding uploadscript to users profile page
	 *
	 * @since 0.1
	 */
	function load_uploadscripts() {
		global $current_screen;

		if($current_screen->id == 'profile' || $current_screen->id == 'user-edit') {
			$javaScript = (\WP_DEBUG === true) ? $this->get_url('/js/jquery-upload.js') : $this->get_url('/js/jquery-upload.min.js');

			\wp_enqueue_script('media-upload');
			\wp_enqueue_script('thickbox');
			\wp_register_script('about-the-author-upload', $javaScript, array(
				'jquery',
				'media-upload',
				'thickbox'
			));
			\wp_enqueue_script('about-the-author-upload');
			\wp_localize_script('about-the-author-upload', 'about_the_author_localizing_upload_js', array(
				'use_this_image' => __('Use This Image', $this->var_sTextdomain)
			));
			\wp_enqueue_style('thickbox');
		} // END if($current_screen->id == 'profile' || $current_screen->id == 'user-edit')
	} // END function load_uploadscripts()

	/**
	 * Adding the CSS
	 *
	 * @since 0.1
	 */
	function load_css() {
		$css = (\WP_DEBUG === true) ? $this->get_url('/css/about-the-author.css') : $this->get_url('/css/about-the-author.min.css');

		\wp_register_style('about-the-author-css', $css);
		\wp_enqueue_style('about-the-author-css');
	} // END function load_css()

	/**
	 * Getting the right thumbnailsize
	 *
	 * @since 0.1
	 *
	 * @param unknown_type $var_sGuid
	 * @param unknown_type $var_sThumbnail
	 * @return boolean|multitype:string NULL
	 */
	function get_thumbnail_by_guid($var_sGuid, $var_sThumbnail) {
		global $_wp_additional_image_sizes;
		/**
		 * Check if we have a thumbnailimage and not the original.
		 * If we do, remove the dimensions to get the original file.
		 *
		 * @since 0.1
		 *
		 * @var regex $var_sPattern
		 */
		$var_sPattern = '/-[0-9\/]+x[0-9\/]+/';
		if(\preg_match($var_sPattern, $var_sGuid)) {
			$var_sGuid = \preg_replace($var_sPattern, '', $var_sGuid);
		} // END if(preg_match($var_sPattern, $var_sGuid))

		/**
		 * Asking the DB
		 *
		 * @since 0.1
		 */
		global $wpdb;
		$var_qry = '
					SELECT
					' . $wpdb->postmeta . '.meta_value as post_meta_value
					FROM
					' . $wpdb->posts . ',
					' . $wpdb->postmeta . '
					WHERE
					' . $wpdb->posts . '.guid = "' . $var_sGuid . '"
					AND ' . $wpdb->postmeta . '.post_id = ' . $wpdb->posts . '.ID
					AND ' . $wpdb->postmeta . '.meta_key = "_wp_attachment_metadata";';
		$array_ImageMeta = \unserialize($wpdb->get_var($var_qry));

		/**
		 * Check if the returned thumbnail has the right dimensions.
		 * If not, return false.
		 *
		 * @since 0.1
		 */
		if($_wp_additional_image_sizes[$var_sThumbnail]['width'] == $array_ImageMeta['sizes'][$var_sThumbnail]['width']) {
			$array_Logo = array(
				'url' => \substr($var_sGuid, 0, \strrpos($var_sGuid, '/')) . '/' . $array_ImageMeta['sizes'][$var_sThumbnail]['file'],
				'width' => $array_ImageMeta['sizes'][$var_sThumbnail]['width'],
				'height' => $array_ImageMeta['sizes'][$var_sThumbnail]['height']
			);
		} else {
			return false;
		} // END if($_wp_additional_image_sizes[$var_sThumbnail]['width'] == $array_ImageMeta['sizes'][$var_sThumbnail]['width'])

		return $array_Logo;
	} // END function get_thumbnail_by_guid($var_sGuid)

	/**
	 * Returning the url
	 *
	 * @since 0.1
	 *
	 * @param unknown_type $path
	 * @return Ambigous <string, mixed>
	 */
	function get_url($path = '') {
		return \plugins_url(ltrim($path, '/'), __FILE__);
	} // END function get_url( $path = '' )
} // END class About_The_Author extends \WP_Widget

\add_action('widgets_init', \create_function('', 'return register_widget("\WordPress\Plugins\AboutTheAuthor\About_The_Author");'));