;(() => {

    /**
     * Sends a request to the REST API to add or remove an FAQ from a product.
     * 
     * @param {Object} data - Data to be sent in the request, including action, product_id, and faq_id.
     * @returns {Promise<void>} 
     */
    const pfwSetRequest = async (data) => {
        const pfwTabFaqWrapper = document.getElementById('pfw-tab-faq-list');
        const loaderWrap = document.querySelector('.pfw-product-loader');

        // Ensure target wrapper exists before proceeding
        if (!pfwTabFaqWrapper) return false;

        loaderWrap.style.display = 'block'; // Show loader while fetching data

        const endpoint = `product-faq-woocommerce/v1/set-faq`; // API endpoint for setting FAQ
        const response = await pfwFetch(endpoint, data); // Send data to the endpoint

        // Check for a valid response and populate the FAQ list
        if (response) {
            pfwTabFaqWrapper.innerHTML = ''; // Clear existing content
            let faqMarkup = '';

            // Check if FAQs are returned and construct HTML markup for each
            const faqs = response.faq_posts;
            if (faqs.length > 0) {
                faqs.forEach((faq) => {
                    faqMarkup += `<div class="pfw-tab-faq-item">
                        <div class="pfq-tab-faq">
                            ${faq.title ? `<h3 class="pfq-tab-faq-title">${faq.title}</h3>` : ''}
                        </div>
                    </div>`;
                });
            }

            // Insert the FAQ markup and hide the loader
            pfwTabFaqWrapper.innerHTML = faqMarkup;
            loaderWrap.style.display = 'none';
        }
    };

    /**
     * Prepares and sends data to add or remove an FAQ based on user selection.
     * 
     * @param {string} action - Action type, either 'add' or 'remove'.
     * @param {number} faqId - ID of the FAQ to be added or removed.
     * @param {number} productId - ID of the product associated with the FAQ.
     * @returns {boolean}
     */
    const pfwGetSet = (action = 'add', faqId, productId) => {
        // Validate required parameters
        if (!action || !faqId || !productId) {
            return false;
        }

        // Data payload for the API request
        let data = {
            action: action,
            product_id: Number(productId),
            faq_id: Number(faqId)
        };

        // Call the API request function with the prepared data
        pfwSetRequest(data);
    };

    /**
     * Main function to initialize the MultiSelect component and set up event listeners.
     */
    const pfwMain = () => {
        const pfwFaqsSelect = document.getElementById('pfw-faq-select');

        if (pfwFaqsSelect) {
            // Retrieve product ID from the data attribute of the select element
            let productId = pfwFaqsSelect.getAttribute('data-product-id') ?? 0;
            console.log(productId);

            // Initialize the MultiSelect dropdown with options and event handlers
            new MultiSelect(pfwFaqsSelect, {
                placeholder: 'Select FAQs',
                search: true,
                selectAll: false,
                onSelect: function(value, text, element) {
                    // Trigger add action when an FAQ is selected
                    pfwGetSet('add', value, productId);
                },
                onUnselect: function(value, text, element) {
                    // Trigger remove action when an FAQ is unselected
                    pfwGetSet('remove', value, productId);
                }
            });
        }
    };

    // Execute main function after the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', () => {
        pfwMain();
    });
})();
