<?php
/*
Plugin Name: Genesis Design Palette Pro - Entry Content
Plugin URI: https://genesisdesignpro.com/
Description: Adds more granular settings for individual post / page content
Author: Reaktiv Studios
Version: 0.0.1
Requires at least: 3.7
Author URI: http://andrewnorcross.com
*/
/*  Copyright 2014 Andrew Norcross

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License (GPL v2) only.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if( !defined( 'GPECN_BASE' ) )
	define( 'GPECN_BASE', plugin_basename(__FILE__) );

if( !defined( 'GPECN_DIR' ) )
	define( 'GPECN_DIR', dirname( __FILE__ ) );

if( !defined( 'GPECN_VER' ) )
	define( 'GPECN_VER', '0.0.1' );


class GP_Pro_Entry_Content
{

	/**
	 * Static property to hold our singleton instance
	 * @var GP_Pro_Post_Content
	 */
	static $instance = false;

	/**
	 * This is our constructor
	 *
	 * @return GP_Pro_Post_Content
	 */
	private function __construct() {

		// general backend
		add_action		(	'plugins_loaded',						array(	$this,	'textdomain'					)			);
		add_action		(	'admin_notices',						array(	$this,	'gppro_active_check'			),	10		);

		// GP Pro specific
		add_filter		(	'gppro_admin_block_add',				array(	$this,	'entry_content_block'			),	1		);
		add_filter		(	'gppro_section_inline_post_content',	array(	$this,	'entry_inline_post_content'		),	15,	2	);
		add_filter		(	'gppro_sections',						array(	$this,	'entry_content_sections'		),	10,	2	);
		add_filter		(	'gppro_css_builder',					array(	$this,	'entry_content_builder'			),	10,	3	);
	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return GP_Pro_Post_Content
	 */

	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	/**
	 * load textdomain
	 *
	 * @return
	 */

	public function textdomain() {

		load_plugin_textdomain( 'gppro-entry-content', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * check for GP Pro being active
	 *
	 * @return GP_Pro_Post_Content
	 */

	public function gppro_active_check() {

		$screen = get_current_screen();

		if ( $screen->parent_file !== 'plugins.php' )
			return;

		// look for our flag
		$coreactive	= get_option( 'gppro_core_active' );

		// not active. show message
		if ( ! $coreactive ) :

			echo '<div id="message" class="error fade below-h2"><p><strong>'.__( 'This plugin requires Genesis Design Palette Pro to function and cannot be activated.', 'gppro-entry-content' ).'</strong></p></div>';

			// hide activation method
			unset( $_GET['activate'] );

			// deactivate YOURSELF
			deactivate_plugins( plugin_basename( __FILE__ ) );

		endif;

		return;

	}



	/**
	 * add and filter options in the post content area
	 *
	 * @return array|string $sections
	 */

	static function entry_inline_post_content( $sections, $class ) {

		// remove the default post content settings in favor of our new ones
		unset( $sections['post-entry-color-setup']['title'] );
		unset( $sections['post-entry-type-setup'] );


		// info about new area
		$sections['post-entry-color-setup']['data']	= array(
			'post-entry-plugin-active'	=> array(
				'input'	=> 'description',
				'desc'	=> __( 'You are currently using the Entry Content add on, so all settings are now available there.', 'gppro-entry-content' ),
			),
		);


		// send it back
		return $sections;

	}

	/**
	 * add block to side
	 *
	 * @return
	 */

	public function entry_content_block( $blocks ) {

		$blocks['entry-content'] = array(
			'tab'		=> __( 'Entry Content', 'gppro-entry-content' ),
			'title'		=> __( 'Entry Content', 'gppro-entry-content' ),
			'intro'		=> __( 'Fine tune the look of the content inside posts and pages.', 'gppro-entry-content' ),
			'slug'		=> 'entry_content',
		);

		return $blocks;

	}

	/**
	 * add section to side
	 *
	 * @return
	 */

	public function entry_content_sections( $sections, $class ) {

		$sections['entry_content']	= array(

			'section-break-entry-content-h1'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'H1 Headers', 'gppro-entry-content' ),
				),
			),

			'entry-content-h1-color-setup'	=> array(
				'title'		=> __( 'Colors', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h1-color-text'	=> array(
						'label'		=> __( 'Base Color', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h1',
						'selector'	=> 'color'
					),
					'entry-content-h1-color-link'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h1 a',
						'selector'	=> 'color'
					),
					'entry-content-h1-color-link-hov'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h1 a:hover',
						'selector'	=> 'color'
					),
				),
			),

			'entry-content-h1-type-setup'	=> array(
				'title'		=> __( 'Typography', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h1-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gppro-entry-content' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .content .entry-content h1',
						'selector'	=> 'font-family'
					),
					'entry-content-h1-size'	=> array(
						'label'		=> __( 'Font Size', 'gppro-entry-content' ),
						'input'		=> 'font-size',
						'scale'		=> 'title',
						'target'	=> $class.' .content .entry-content h1',
						'selector'	=> 'font-size',
					),
					'entry-content-h1-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gppro-entry-content' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .content .entry-content h1',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gppro-entry-content' )
					),
				),
			),

			'entry-content-h1-appearance-setup'	=> array(
				'title'		=> __( 'Text Appearance', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h1-margin-bottom'	=> array(
						'label'		=> __( 'Bottom Margin', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h1',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h1-padding-bottom'	=> array(
						'label'		=> __( 'Bottom Padding', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h1',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h1-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gppro-entry-content' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .content .entry-content h1',
						'selector'	=> 'text-transform'
					),
					'entry-content-h1-align'	=> array(
						'label'		=> __( 'Text Alignment', 'gppro-entry-content' ),
						'input'		=> 'text-align',
						'target'	=> $class.' .content .entry-content h1',
						'selector'	=> 'text-align'
					),
					'entry-content-h1-link-dec' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h1 a',
						'selector'	=> 'text-decoration'
					),
					'entry-content-h1-link-dec-hov' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h1 a:hover',
						'selector'	=> 'text-decoration'
					),
				),
			),

			'section-break-entry-content-h2'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'H2 Headers', 'gppro-entry-content' ),
				),
			),

			'entry-content-h2-color-setup'	=> array(
				'title'		=> __( 'Colors', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h2-color-text'	=> array(
						'label'		=> __( 'Base Color', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h2',
						'selector'	=> 'color'
					),
					'entry-content-h2-color-link'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h2 a',
						'selector'	=> 'color'
					),
					'entry-content-h2-color-link-hov'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h2 a:hover',
						'selector'	=> 'color'
					),
				),
			),

			'entry-content-h2-type-setup'	=> array(
				'title'		=> __( 'Typography', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h2-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gppro-entry-content' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .content .entry-content h2',
						'selector'	=> 'font-family'
					),
					'entry-content-h2-size'	=> array(
						'label'		=> __( 'Font Size', 'gppro-entry-content' ),
						'input'		=> 'font-size',
						'scale'		=> 'title',
						'target'	=> $class.' .content .entry-content h2',
						'selector'	=> 'font-size',
					),
					'entry-content-h2-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gppro-entry-content' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .content .entry-content h2',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gppro-entry-content' )
					),
				),
			),

			'entry-content-h2-appearance-setup'	=> array(
				'title'		=> __( 'Text Appearance', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h2-margin-bottom'	=> array(
						'label'		=> __( 'Bottom Margin', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h2',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h2-padding-bottom'	=> array(
						'label'		=> __( 'Bottom Padding', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h2',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h2-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gppro-entry-content' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .content .entry-content h2',
						'selector'	=> 'text-transform'
					),
					'entry-content-h2-align'	=> array(
						'label'		=> __( 'Text Alignment', 'gppro-entry-content' ),
						'input'		=> 'text-align',
						'target'	=> $class.' .content .entry-content h2',
						'selector'	=> 'text-align'
					),
					'entry-content-h2-link-dec' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h2 a',
						'selector'	=> 'text-decoration'
					),
					'entry-content-h2-link-dec-hov' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h2 a:hover',
						'selector'	=> 'text-decoration'
					),
				),
			),

			'section-break-entry-content-h3'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'H3 Headers', 'gppro-entry-content' ),
				),
			),

			'entry-content-h3-color-setup'	=> array(
				'title'		=> __( 'Colors', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h3-color-text'	=> array(
						'label'		=> __( 'Base Color', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h3',
						'selector'	=> 'color'
					),
					'entry-content-h3-color-link'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h3 a',
						'selector'	=> 'color'
					),
					'entry-content-h3-color-link-hov'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h3 a:hover',
						'selector'	=> 'color'
					),
				),
			),

			'entry-content-h3-type-setup'	=> array(
				'title'		=> __( 'Typography', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h3-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gppro-entry-content' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .content .entry-content h3',
						'selector'	=> 'font-family'
					),
					'entry-content-h3-size'	=> array(
						'label'		=> __( 'Font Size', 'gppro-entry-content' ),
						'input'		=> 'font-size',
						'scale'		=> 'title',
						'target'	=> $class.' .content .entry-content h3',
						'selector'	=> 'font-size',
					),
					'entry-content-h3-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gppro-entry-content' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .content .entry-content h3',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gppro-entry-content' )
					),
				),
			),

			'entry-content-h3-appearance-setup'	=> array(
				'title'		=> __( 'Text Appearance', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h3-margin-bottom'	=> array(
						'label'		=> __( 'Bottom Margin', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h3',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h3-padding-bottom'	=> array(
						'label'		=> __( 'Bottom Padding', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h3',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h3-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gppro-entry-content' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .content .entry-content h3',
						'selector'	=> 'text-transform'
					),
					'entry-content-h3-align'	=> array(
						'label'		=> __( 'Text Alignment', 'gppro-entry-content' ),
						'input'		=> 'text-align',
						'target'	=> $class.' .content .entry-content h3',
						'selector'	=> 'text-align'
					),
					'entry-content-h3-link-dec' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h3 a',
						'selector'	=> 'text-decoration'
					),
					'entry-content-h3-link-dec-hov' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h3 a:hover',
						'selector'	=> 'text-decoration'
					),
				),
			),

			'section-break-entry-content-h4'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'H4 Headers', 'gppro-entry-content' ),
				),
			),

			'entry-content-h4-color-setup'	=> array(
				'title'		=> __( 'Colors', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h4-color-text'	=> array(
						'label'		=> __( 'Base Color', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h4',
						'selector'	=> 'color'
					),
					'entry-content-h4-color-link'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h4 a',
						'selector'	=> 'color'
					),
					'entry-content-h4-color-link-hov'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h4 a:hover',
						'selector'	=> 'color'
					),
				),
			),

			'entry-content-h4-type-setup'	=> array(
				'title'		=> __( 'Typography', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h4-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gppro-entry-content' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .content .entry-content h4',
						'selector'	=> 'font-family'
					),
					'entry-content-h4-size'	=> array(
						'label'		=> __( 'Font Size', 'gppro-entry-content' ),
						'input'		=> 'font-size',
						'scale'		=> 'title',
						'target'	=> $class.' .content .entry-content h4',
						'selector'	=> 'font-size',
					),
					'entry-content-h4-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gppro-entry-content' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .content .entry-content h4',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gppro-entry-content' )
					),
				),
			),

			'entry-content-h4-appearance-setup'	=> array(
				'title'		=> __( 'Text Appearance', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h4-margin-bottom'	=> array(
						'label'		=> __( 'Bottom Margin', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h4',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h4-padding-bottom'	=> array(
						'label'		=> __( 'Bottom Padding', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h4',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h4-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gppro-entry-content' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .content .entry-content h4',
						'selector'	=> 'text-transform'
					),
					'entry-content-h4-align'	=> array(
						'label'		=> __( 'Text Alignment', 'gppro-entry-content' ),
						'input'		=> 'text-align',
						'target'	=> $class.' .content .entry-content h4',
						'selector'	=> 'text-align'
					),
					'entry-content-h4-link-dec' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h4 a',
						'selector'	=> 'text-decoration'
					),
					'entry-content-h4-link-dec-hov' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h4 a:hover',
						'selector'	=> 'text-decoration'
					),
				),
			),

			'section-break-entry-content-h5'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'H5 Headers', 'gppro-entry-content' ),
				),
			),

			'entry-content-h5-color-setup'	=> array(
				'title'		=> __( 'Colors', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h5-color-text'	=> array(
						'label'		=> __( 'Base Color', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h5',
						'selector'	=> 'color'
					),
					'entry-content-h5-color-link'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h5 a',
						'selector'	=> 'color'
					),
					'entry-content-h5-color-link-hov'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h5 a:hover',
						'selector'	=> 'color'
					),
				),
			),

			'entry-content-h5-type-setup'	=> array(
				'title'		=> __( 'Typography', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h5-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gppro-entry-content' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .content .entry-content h5',
						'selector'	=> 'font-family'
					),
					'entry-content-h5-size'	=> array(
						'label'		=> __( 'Font Size', 'gppro-entry-content' ),
						'input'		=> 'font-size',
						'scale'		=> 'title',
						'target'	=> $class.' .content .entry-content h5',
						'selector'	=> 'font-size',
					),
					'entry-content-h5-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gppro-entry-content' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .content .entry-content h5',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gppro-entry-content' )
					),
				),
			),

			'entry-content-h5-appearance-setup'	=> array(
				'title'		=> __( 'Text Appearance', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h5-margin-bottom'	=> array(
						'label'		=> __( 'Bottom Margin', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h5',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h5-padding-bottom'	=> array(
						'label'		=> __( 'Bottom Padding', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h5',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h5-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gppro-entry-content' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .content .entry-content h5',
						'selector'	=> 'text-transform'
					),
					'entry-content-h5-align'	=> array(
						'label'		=> __( 'Text Alignment', 'gppro-entry-content' ),
						'input'		=> 'text-align',
						'target'	=> $class.' .content .entry-content h5',
						'selector'	=> 'text-align'
					),
					'entry-content-h5-link-dec' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h5 a',
						'selector'	=> 'text-decoration'
					),
					'entry-content-h5-link-dec-hov' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h5 a:hover',
						'selector'	=> 'text-decoration'
					),
				),
			),

			'section-break-entry-content-h6'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'H6 Headers', 'gppro-entry-content' ),
				),
			),

			'entry-content-h6-color-setup'	=> array(
				'title'		=> __( 'Colors', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h6-color-text'	=> array(
						'label'		=> __( 'Base Color', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h6',
						'selector'	=> 'color'
					),
					'entry-content-h6-color-link'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h6 a',
						'selector'	=> 'color'
					),
					'entry-content-h6-color-link-hov'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content h6 a:hover',
						'selector'	=> 'color'
					),
				),
			),

			'entry-content-h6-type-setup'	=> array(
				'title'		=> __( 'Typography', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h6-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gppro-entry-content' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .content .entry-content h6',
						'selector'	=> 'font-family'
					),
					'entry-content-h6-size'	=> array(
						'label'		=> __( 'Font Size', 'gppro-entry-content' ),
						'input'		=> 'font-size',
						'scale'		=> 'title',
						'target'	=> $class.' .content .entry-content h6',
						'selector'	=> 'font-size',
					),
					'entry-content-h6-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gppro-entry-content' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .content .entry-content h6',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gppro-entry-content' )
					),
				),
			),

			'entry-content-h6-appearance-setup'	=> array(
				'title'		=> __( 'Text Appearance', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-h6-margin-bottom'	=> array(
						'label'		=> __( 'Bottom Margin', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h6',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h6-padding-bottom'	=> array(
						'label'		=> __( 'Bottom Padding', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content h6',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-h6-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gppro-entry-content' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .content .entry-content h6',
						'selector'	=> 'text-transform'
					),
					'entry-content-h6-align'	=> array(
						'label'		=> __( 'Text Alignment', 'gppro-entry-content' ),
						'input'		=> 'text-align',
						'target'	=> $class.' .content .entry-content h6',
						'selector'	=> 'text-align'
					),
					'entry-content-h6-link-dec' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h6 a',
						'selector'	=> 'text-decoration'
					),
					'entry-content-h6-link-dec-hov' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content h6 a:hover',
						'selector'	=> 'text-decoration'
					),
				),
			),

			'section-break-entry-content-p'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'Paragraphs', 'gppro-entry-content' ),
				),
			),

			'entry-content-p-color-setup'	=> array(
				'title'		=> __( 'Colors', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-p-color-text'	=> array(
						'label'		=> __( 'Base Color', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content p',
						'selector'	=> 'color'
					),
					'entry-content-p-color-link'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content p a',
						'selector'	=> 'color'
					),
					'entry-content-p-color-link-hov'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content p a:hover',
						'selector'	=> 'color'
					),
				),
			),

			'entry-content-p-type-setup'	=> array(
				'title'		=> __( 'Typography', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-p-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gppro-entry-content' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .content .entry-content p',
						'selector'	=> 'font-family'
					),
					'entry-content-p-size'	=> array(
						'label'		=> __( 'Font Size', 'gppro-entry-content' ),
						'input'		=> 'font-size',
						'scale'		=> 'title',
						'target'	=> $class.' .content .entry-content p',
						'selector'	=> 'font-size',
					),
					'entry-content-p-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gppro-entry-content' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .content .entry-content p',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gppro-entry-content' )
					),
				),
			),

			'entry-content-p-appearance-setup'	=> array(
				'title'		=> __( 'Text Appearance', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-p-margin-bottom'	=> array(
						'label'		=> __( 'Bottom Margin', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content p',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-p-padding-bottom'	=> array(
						'label'		=> __( 'Bottom Padding', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content p',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-p-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gppro-entry-content' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .content .entry-content p',
						'selector'	=> 'text-transform'
					),
					'entry-content-p-align'	=> array(
						'label'		=> __( 'Text Alignment', 'gppro-entry-content' ),
						'input'		=> 'text-align',
						'target'	=> $class.' .content .entry-content p',
						'selector'	=> 'text-align'
					),
					'entry-content-p-link-dec' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content p a',
						'selector'	=> 'text-decoration'
					),
					'entry-content-p-link-dec-hov' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content p a:hover',
						'selector'	=> 'text-decoration'
					),
				),
			),

			'section-break-entry-content-cap'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'Image Captions', 'gppro-entry-content' ),
				),
			),

			'entry-content-cap-color-setup'	=> array(
				'title'		=> __( 'Colors', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-cap-color-text'	=> array(
						'label'		=> __( 'Base Color', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content .wp-caption-text',
						'selector'	=> 'color'
					),
					'entry-content-cap-color-link'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content .wp-caption-text a',
						'selector'	=> 'color'
					),
					'entry-content-cap-color-link-hov'	=> array(
						'label'		=> __( 'Link Color', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'color',
						'target'	=> $class.' .content .entry-content .wp-caption-text a:hover',
						'selector'	=> 'color'
					),
				),
			),

			'entry-content-cap-type-setup'	=> array(
				'title'		=> __( 'Typography', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-cap-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gppro-entry-content' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .content .entry-content .wp-caption-text',
						'selector'	=> 'font-family'
					),
					'entry-content-cap-size'	=> array(
						'label'		=> __( 'Font Size', 'gppro-entry-content' ),
						'input'		=> 'font-size',
						'scale'		=> 'title',
						'target'	=> $class.' .content .entry-content .wp-caption-text',
						'selector'	=> 'font-size',
					),
					'entry-content-cap-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gppro-entry-content' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .content .entry-content .wp-caption-text',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gppro-entry-content' )
					),
				),
			),

			'entry-content-cap-appearance-setup'	=> array(
				'title'		=> __( 'Text Appearance', 'gppro-entry-content' ),
				'data'		=> array(
					'entry-content-cap-margin-bottom'	=> array(
						'label'		=> __( 'Bottom Margin', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content .wp-caption-text',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-cap-padding-bottom'	=> array(
						'label'		=> __( 'Bottom Padding', 'gppro-entry-content' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .content .entry-content .wp-caption-text',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '60',
						'step'		=> '1'
					),
					'entry-content-cap-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gppro-entry-content' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .content .entry-content .wp-caption-text',
						'selector'	=> 'text-transform'
					),
					'entry-content-cap-align'	=> array(
						'label'		=> __( 'Text Alignment', 'gppro-entry-content' ),
						'input'		=> 'text-align',
						'target'	=> $class.' .content .entry-content .wp-caption-text',
						'selector'	=> 'text-align'
					),
					'entry-content-cap-link-dec' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Base', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content .wp-caption-text a',
						'selector'	=> 'text-decoration'
					),
					'entry-content-cap-link-dec-hov' => array(
						'label'		=> __( 'Link Style', 'gppro-entry-content' ),
						'sub'		=> __( 'Hover', 'gppro-entry-content' ),
						'input'		=> 'text-decoration',
						'target'	=> $class.' .content .entry-content .wp-caption-text a:hover',
						'selector'	=> 'text-decoration'
					),
				),
			),

		); // end section

		return $sections;

	}


	/**
	 * add freeform CSS to builder file
	 *
	 * @return
	 */

	public function entry_content_builder( $custom, $data, $class ) {

		$custom	= '/* custom freeform CSS */'."\n";


		return $custom;

	}

/// end class
}

// Instantiate our class
$GP_Pro_Entry_Content = GP_Pro_Entry_Content::getInstance();

