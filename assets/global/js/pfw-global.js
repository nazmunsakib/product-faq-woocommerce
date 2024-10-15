
/**
 * Product faqs global script
 */
const pfwFetch = async ( endpoint = null, bodyData = {} ) =>{
    if( !endpoint ){
        return false;
    }

    try{
        const response = await fetch( pfwObj.api_url + endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': pfwObj.nonce
            },
            body: JSON.stringify(bodyData),
        });
        
        if( !response.ok ){
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data;

    } catch(error){
        console.error('Error fetching:', error);
    }
}

const pfwAccordion = (header, contentSelector) =>{
    const content = header.nextElementSibling;
    
    if (content.style.height === "0px" || content.style.height === "") {
        content.style.height = content.scrollHeight + "px";
        header.classList.add('pfw-open');
    } else {
        content.style.height = "0px";
        header.classList.remove('pfw-open');
    }
    
    const allItems = document.querySelectorAll(`.${contentSelector}`);
    allItems.forEach(item => {
      if (item !== content && item.style.height !== "0px") {
        item.style.height = "0px";
        item.previousElementSibling.classList.remove('pfw-open');
      }
    });
}