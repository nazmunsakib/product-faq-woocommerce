<div class="pfw-faq-wrapper pfw-faq-layout-1">
    <?php 
    $ids =  get_post_meta( 136, 'pfw_product_faqs');

    var_dump( $ids );
    foreach( $faqs as $faq ) : ?>
        <div class="pfw-faq-item">
            <div class="pfw-faq-header">
                <span class="pfw-faq-question">What is the purpose of this pen?</span>
                <span class="pfw-faq-icon"></span>
            </div>
            <div class="pfw-faq-content">
                <p class="pfw-faq-answer">This pen is designed to provide web developers with boilerplate code for a FAQ Accordion.</p>
            </div>
        </div>
    <?php endforeach; ?>
</div>