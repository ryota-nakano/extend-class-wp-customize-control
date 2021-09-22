<?php
/**
 * カスタマイザのコントロール拡張
 * - checkbox-multiple 複数選択できるチェックボックス
 *
 * @package awr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Extend_Class_WP_Customize_Control' ) ) {
	return;
}

/**
 * Extend_Class_WP_Customize_Control
 *
 * @link https://gist.github.com/shizhua/12fbfd10806c0bd91867dc6a61ea4278
 */
class Extend_Class_WP_Customize_Control extends WP_Customize_Control {

	/**
	 * タイプで処理を切り替える
	 *
	 * @var    string
	 */
	public $type = 'checkbox-multiple';

	/**
	 * Enqueue インラインスクリプト
	 */
	public function enqueue() {
		switch ( $this->type ) {

			// 複数選択可能なチェックボックス
			case 'checkbox-multiple':
				$script = <<<JQuery
				jQuery(function($) {
					$( window ).on( 'load', function() {
						$( '.customize-control-checkbox-multiple input[type="checkbox"]' ).on( 'change', function() {
							var checkbox_values = $( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
								function() {
									return this.value;
								}
							).get().join( ',' );
							$( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );
						});
					});
				});
				JQuery;
				break;
		}
		wp_add_inline_script( 'jquery', $script );
	}

	/**
	 * コントロールのレンダリング
	 */
	public function render_content() {
		switch ( $this->type ) {

			// 複数選択可能なチェックボックス
			case 'checkbox-multiple':
				if ( empty( $this->choices ) ) {
					return;
				} ?>

				<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>

				<?php $multi_values = ! is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value(); ?>

				<ul>
					<?php foreach ( $this->choices as $value => $label ) : ?>

						<li>
							<label>
								<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values, true ) ); ?> /> 
								<?php echo esc_html( $label ); ?>
							</label>
						</li>

					<?php endforeach; ?>
				</ul>

				<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />

				<?php
				break;
		}
	}

	/**
	 * マルチチェックボックス用のサニタイズ関数
	 *
	 * @param string $values Values.
	 * @return array Checked values.
	 */
	public function sanitize_checkbox_multiple( $values ) {
		$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
		return ! empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
	}

}
