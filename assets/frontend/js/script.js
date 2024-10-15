;(() => {

  document.addEventListener('DOMContentLoaded', () => {
    const pfwAccordions = document.querySelectorAll('.pfw-faq-header');
    if( pfwAccordions.length > 0  ){
      pfwAccordions.forEach((accordion, index) => {
        accordion.addEventListener('click', function(e){
          pfwAccordion(this, 'pfw-faq-content');
        });
      });
    }
    //pfwAccordion('pfw-faq-item', 'pfw-faq-content');
  });


})();