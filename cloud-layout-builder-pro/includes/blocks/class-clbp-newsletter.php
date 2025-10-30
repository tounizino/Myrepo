<?php
/**
 * Cloud Layout Builder Pro - Newsletter Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Newsletter_Block extends CLBP_Block_Base {

    public function __construct() {
        $this->name  = 'newsletter';
        $this->title = __( 'Newsletter Signup', 'cloud-layout-builder-pro' );
        $this->icon  = 'email';
        $this->attributes = array(
            'title'       => array( 'type' => 'string', 'default' => __( 'Stay Updated', 'cloud-layout-builder-pro' ) ),
            'subtitle'    => array( 'type' => 'string', 'default' => __( 'Get the latest updates, guides, and insights delivered to your inbox', 'cloud-layout-builder-pro' ) ),
            'buttonText'  => array( 'type' => 'string', 'default' => __( 'Subscribe Now', 'cloud-layout-builder-pro' ) ),
            'provider'    => array( 'type' => 'string', 'default' => 'custom' ),
            'mailchimp'   => array( 'type' => 'string', 'default' => '' ),
            'brevo'       => array( 'type' => 'string', 'default' => '' ),
            'placeholder' => array( 'type' => 'string', 'default' => __( 'Your email address', 'cloud-layout-builder-pro' ) ),
            'gdpr'        => array( 'type' => 'boolean', 'default' => true ),
            'gdprText'    => array( 'type' => 'string', 'default' => __( 'I agree to receive emails and accept the privacy policy', 'cloud-layout-builder-pro' ) ),
        );
    }

    public function render( $attributes ) {
        $attributes = wp_parse_args(
            $attributes,
            array(
                'title'       => __( 'Stay Updated', 'cloud-layout-builder-pro' ),
                'subtitle'    => __( 'Get the latest updates, guides, and insights delivered to your inbox', 'cloud-layout-builder-pro' ),
                'buttonText'  => __( 'Subscribe Now', 'cloud-layout-builder-pro' ),
                'placeholder' => __( 'Your email address', 'cloud-layout-builder-pro' ),
                'gdpr'        => true,
                'provider'    => 'custom',
            )
        );

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?>>
            <div class="clbp-newsletter">
                <div class="clbp-newsletter__content">
                    <?php echo $this->get_block_heading( $attributes['title'], $attributes['subtitle'] ); ?>
                </div>
                <form class="clbp-newsletter__form" data-provider="<?php echo esc_attr( $attributes['provider'] ); ?>" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post">
                    <input type="hidden" name="action" value="clbp_newsletter_signup">
                    <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'clbp_newsletter' ) ); ?>">
                    
                    <div class="clbp-newsletter__input-wrapper">
                        <input type="email" name="email" class="clbp-newsletter__input" placeholder="<?php echo esc_attr( $attributes['placeholder'] ); ?>" required>
                        <button type="submit" class="clbp-newsletter__button">
                            <?php echo esc_html( $attributes['buttonText'] ); ?>
                        </button>
                    </div>

                    <?php if ( $attributes['gdpr'] ) : ?>
                        <label class="clbp-newsletter__gdpr">
                            <input type="checkbox" name="gdpr_consent" required>
                            <span><?php echo esc_html( $attributes['gdprText'] ?? __( 'I agree to receive emails and accept the privacy policy', 'cloud-layout-builder-pro' ) ); ?></span>
                        </label>
                    <?php endif; ?>

                    <div class="clbp-newsletter__message"></div>
                </form>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
