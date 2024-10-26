/**
 * Product FAQ WooCommerce - Accordion Functionality
 *
 * Initializes accordion functionality for FAQ sections on the WooCommerce product page.
 * Listens for clicks on FAQ headers to toggle visibility of corresponding FAQ content.
 */

(() => {
  // Wait for the DOM to fully load before executing the code.
  document.addEventListener('DOMContentLoaded', () => {
    // Select all elements with the class 'pfw-faq-header' for accordion functionality.
    const pfwAccordions = document.querySelectorAll('.pfw-faq-header');

    // Check if there are any FAQ headers to apply accordion behavior.
    if (pfwAccordions.length > 0) {
      // Iterate over each accordion header element.
      pfwAccordions.forEach((accordion) => {
        // Add a click event listener to each FAQ header.
        accordion.addEventListener('click', function() {
          // Call the pfwAccordion function, passing the clicked header and target content class.
          pfwAccordion(this, 'pfw-faq-content');
        });
      });
    }
  });
})();
