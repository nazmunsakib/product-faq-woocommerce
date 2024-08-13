;(function(){

  const pfwFAQs = () => {
    const pfwAccordions = document.querySelectorAll(".pfw-faq-item");

    if( !pfwAccordions ){
      return false;
    }

    pfwAccordions.forEach((accordion, index) => {
      const faqContent = accordion.querySelector(".pfw-faq-content");

      accordion.addEventListener('click', (e) =>{
        /**
         * Remove open class from other accordion
         */
        pfwAccordions.forEach((a, i) => {
          const content = a.querySelector(".pfw-faq-content");
          if( a.classList.contains('pfw-open') && i != index ){
            a.classList.remove('pfw-open');
            content.style.height = "0px";
          }
        });

        /**
         * Add open class on current accordion
         */
        const currentItem = pfwAccordions[index];
        if( currentItem ){
          if( currentItem.classList.contains('pfw-open') ){
            currentItem.classList.remove('pfw-open');
            faqContent.style.height = "0px";
          }else{
            currentItem.classList.add('pfw-open');
            faqContent.style.height = `${faqContent.scrollHeight}px`;
          }
        }
      });
    });
    
  }

  window.onload = () => {
    pfwFAQs();
  }

})();