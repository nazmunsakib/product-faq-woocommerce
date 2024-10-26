<div class="pfw-faq-wrapper pfw-faq-layout-1">
    <?php
    if(!empty($faqs)):
        foreach( $faqs as $faq ) : 
            ?>
            <div class="pfw-faq-item">
                <div class="pfw-faq-header">
                    <span class="pfw-faq-question"><?php echo esc_html($faq->post_title); ?></span>
                    <span class="pfw-faq-icon"></span>
                </div>
                <div class="pfw-faq-content" style="overflow: hidden; height: 0; transition: height 0.3s ease;">
                    <div class="pfw-faq-answer"><?php echo wp_kses_post($faq->post_content); ?></div>
                </div>
            </div>
        <?php 
        endforeach;
    else:
        echo esc_html__('No FQA Found!', 'product-faq-woocommerce');
    endif;
    ?>
</div>
