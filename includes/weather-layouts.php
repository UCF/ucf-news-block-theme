<?php
/**
 * Custom layouts for the UCF Weather Shortcode plugin.
 *
 * @package UCF_Today_Block_Theme
 */

if ( ! function_exists( 'ucf_today_weather_default_today_nav' ) ) {
	/**
	 * Custom layout for the UCF Weather Shortcode plugin for
	 * displaying weather data in the site header.
	 *
	 * Adapted from the Today-Child-Theme.
	 *
	 * Registered against the `default` feed with the `today_nav` layout,
	 * i.e. `[ucf-weather feed="default" layout="today_nav"]` or the
	 * `ucf/weather` block with matching attributes.
	 *
	 * @since 1.0.0
	 * @param object $data   Weather data.
	 * @param string $output HTML output.
	 * @return string HTML markup.
	 */
	function ucf_today_weather_default_today_nav( $data, $output ) {
		if ( ! class_exists( 'UCF_Weather_Common' ) ) {
			return $output;
		}

		if ( ! is_object( $data ) || ! property_exists( $data, 'condition' ) ) {
			return $output;
		}

		$icon = UCF_Weather_Common::get_weather_icon_svg( $data->condition );

		ob_start();
	?>
		<div class="weather weather-today-nav">
			<span class="weather-date"><?php echo esc_html( wp_date( 'l, F j, Y' ) ); ?></span>
			<span class="weather-status">
				<span class="wi weather-icon" aria-hidden="true">
					<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup returned by UCF_Weather_Common::get_weather_icon_svg() is expected to be safe. ?>
				</span>
				<span class="weather-text">
					<span class="weather-temp"><?php echo esc_html( $data->temp ); ?>F</span>
					<span class="weather-condition"><?php echo esc_html( $data->condition ); ?></span>
				</span>
			</span>
		</div>
	<?php
		return ob_get_clean();
	}

	add_filter( 'ucf_weather_default_today_nav', 'ucf_today_weather_default_today_nav', 10, 2 );
}
