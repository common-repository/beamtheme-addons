<?php
/*
Plugin Name: BeamTheme Addons
Plugin URI: https://www.beamtheme.com/beamtheme-addons/
Description: Beam Addons extends the functionality of <strong>Beam WordPress Theme</strong>.
Version: 1.0.1
Author: Emir Muracevic
Author URI: http://www.beamtheme.com/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Copyright: Emir Muracevic
*/

$theme_name = wp_get_theme(); // gets the current theme
if ('Beam' == $theme_name->name || 'Beam' == $theme_name->parent_theme) {

	/**
	 * Beam Recent Posts
	 * since 0.8.7
	 */
	add_action( 'widgets_init', function(){
		 register_widget( 'beam_posts_widget' );
	});	
	/**
	 * Adds beam_posts_widget widget.
	 */
	class beam_posts_widget extends WP_Widget {	
		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'beam_posts_widget', // Base ID
				__('Beam Posts Widget', 'beam'), // Name
				array( 'description' => __( 'Beam Recent Posts with Featured image', 'beam' ), ) // Args
			);
		}
		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}

			$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
			if ( ! $number )
				$number = 5;
			$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

			$arguments = array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'posts_per_page' => $number,
				'ignore_sticky_posts' => true
				//'has_password' => false,
			);

			$beam_posts = new WP_Query( $arguments );

			if ( $beam_posts->have_posts() ) : 
			?>
					<ul>
					<?php while ( $beam_posts->have_posts() ) : $beam_posts->the_post() ?>

						<li>
							<a href="<?php the_permalink(); ?>">
								<div class="clearfix">
									<?php 
									if ( has_post_thumbnail() ) :
										the_post_thumbnail( 'beam-small-thumbnail', array( 'class' => 'alignleft' ) );
									endif;

									get_the_title() ? the_title() : the_ID(); ?><br />

									<?php if ( $show_date ) : ?>
										<span class="post-date"><?php echo get_the_date(); ?></span>
									<?php endif; ?>
								</div>
							</a>
						</li>

					<?php endwhile ?>
				  </ul>  

			<?php 

				else : 
			?>

				<h2>Ooops, no posts here!</h2>

			<?php 

			wp_reset_postdata();

			endif; 	

			echo $args['after_widget'];
		}
			/**
			 * Handles updating the settings for the current Recent Posts widget instance.
			 *
			 * @since 2.8.0
			 * @access public
			 *
			 * @param array $new_instance New settings for this instance as input by the user via
			 *                            WP_Widget::form().
			 * @param array $old_instance Old settings for this instance.
			 * @return array Updated settings to save.
			 */
			public function update( $new_instance, $old_instance ) {
				$instance = $old_instance;
				$instance['title'] = sanitize_text_field( $new_instance['title'] );
				$instance['number'] = (int) $new_instance['number'];
				$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
				return $instance;
			}

			/**
			 * Outputs the settings form for the Recent Posts widget.
			 *
			 * @since 2.8.0
			 * @access public
			 *
			 * @param array $instance Current settings.
			 */
			public function form( $instance ) {
				$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
				$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
				$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
			?>
				<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

				<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

				<p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
		<?php
			} // function form
		} // class beam_posts_widget
	} 