<?php
/*
Plugin Name: Comment Overload
Plugin URI: http://trepmal.com/plugins/comment-overload
Description: Alert commenters when they start writing too many paragraphs
Version: 1.3
Author: Kailey Lampert
Author URI: http://kaileylampert.com

Copyright (C) 2011-16  Kailey Lampert

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Comment_Overload {

	/**
	 *
	 */
	function __construct() {

		load_plugin_textdomain( 'comment-overload', false, dirname( plugin_basename( __FILE__ ) ) .  '/lang' );

		$basename = plugin_basename( __FILE__ );
		add_filter( "plugin_action_links_{$basename}", array( $this, 'add_action_links' ) );

		add_action( 'wp_enqueue_scripts',              array( $this, 'scripts' ) );
		add_filter( 'admin_init' ,                     array( $this, 'register_fields' ) );

	}

	/**
	 *
	 */
	function add_action_links( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'options-discussion.php#comment-overload' ) ) . '">' . esc_html__( 'Settings' ) . '</a>';
		return $links;
	}

	/**
	 *
	 */
	function scripts() {
		$options = $this->_options();
		$max     = $options['max'];
		$alert   = $options['alert'];
		$subtle  = $options['subtle'];

		wp_enqueue_script( 'comment-overload', plugins_url( 'js/comment-overload.js', __FILE__ ), array( 'jquery' ), '0.0.1' );
		wp_localize_script( 'comment-overload', 'commentOverload', array(
			'max'    => $max,
			'alert'  => $alert,
			'subtle' => $subtle,
		) );
	}

	/**
	 *
	 */
	function register_fields() {
		register_setting( 'discussion', 'comment_overload_options', array( $this, 'sanitize' ) );
		add_settings_field(
			'comment_overload',
			'<label id="comment-overload">' . esc_html__( 'Comment Overload' , 'comment-overload' ) . '</label>' ,
			array( $this, 'fields'),
			'discussion',
			'default',
			$this->_options()
		);
	}

	/**
	 *
	 */
	function fields( $options ) {

		$max     = esc_attr( $options['max'] );
		$alert   = esc_textarea( $options['alert'] );
		$subtle  = isset( $options['subtle'] );

		echo '<p>';
		esc_html_e( 'Maximum number of new lines' , 'comment-overload' );
		echo " <input class='small-text' type='text' name='comment_overload_options[max]' value='{$max}' />";
		echo '</p>';

		echo '<p>';
		esc_html_e( 'If maximum is reached, tell the commenter:' , 'comment-overload' );
		echo "<br /><textarea class='large-text code' name='comment_overload_options[alert]'>{$alert}</textarea>";

		printf( __( '%s number of paragraphs being written by user' , 'comment-overload' ), '<code>%total%</code> =' );

		echo '<br />';
		printf( __( '%s maximum number recommended' , 'comment-overload' ), '<code>%max%</code> = ' );
		echo '</p>';

		$label = esc_html__( 'Subtler alerts after the first' , 'comment-overload' );
		$chk = checked( $subtle, true, false );
		echo "<p><label><input type='checkbox' name='comment_overload_options[subtle]' {$chk} /> {$label}</label></p>";

	}

	/**
	 *
	 */
	function sanitize( $input ) {
		$input['max']    = intval( $input['max'] );
		$input['alert']  = esc_js( $input['alert'] );
		$input['subtle'] = isset( $input['subtle'] );
		return $input;
	}

	/**
	 *
	 */
	function _options() {
		$defaults = array(
			'max'    => 3,
			'alert'  => '%total% paragraphs? How about you write a blog post and link it here?',
			'subtle' => true,
		);
		return get_option( 'comment_overload_options', $defaults );
	}
}
$comment_overload = new Comment_Overload();
