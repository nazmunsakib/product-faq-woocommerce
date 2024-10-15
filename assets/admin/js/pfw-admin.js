;(() => {
    /**
     * pfw-multiSelect
     */
    class PwfMultiSelect {

        constructor(element, options = {}) {
            let defaults = {
                placeholder: 'Select FAQs',
                max: null,
                search: true,
                selectAll: true,
                listAll: true,
                closeListOnItemSelect: false,
                name: '',
                width: '',
                height: '',
                dropdownWidth: '',
                dropdownHeight: '',
                data: [],
                onChange: function(value) {},
                onSelect: function(value) {
                    pfwGetSet('add', value);
                },
                onUnselect: function(value) {
                   pfwGetSet('remove', value);
                }
            };
            this.options = Object.assign(defaults, options);
            this.selectElement = typeof element === 'string' ? document.querySelector(element) : element;
            for(const prop in this.selectElement.dataset) {
                if (this.options[prop] !== undefined) {
                    this.options[prop] = this.selectElement.dataset[prop];
                }
            }
            this.name = this.selectElement.getAttribute('name') ? this.selectElement.getAttribute('name') : 'pfw-multi-select-' + Math.floor(Math.random() * 1000000);
            if (!this.options.data.length) {
                let options = this.selectElement.querySelectorAll('option');
                for (let i = 0; i < options.length; i++) {
                    this.options.data.push({
                        value: options[i].value,
                        text: options[i].innerHTML,
                        selected: options[i].selected,
                        html: options[i].getAttribute('data-html')
                    });
                }
            }
            this.element = this._template();
            this.selectElement.replaceWith(this.element);
            this._updateSelected();
            this._eventHandlers();
        }
    
        _template() {
            let optionsHTML = '';
            for (let i = 0; i < this.data.length; i++) {
                optionsHTML += `
                    <div class="pfw-multi-select-option${this.selectedValues.includes(this.data[i].value) ? ' pfw-multi-select-selected' : ''}" data-value="${this.data[i].value}">
                        <span class="pfw-multi-select-option-radio"></span>
                        <span class="pfw-multi-select-option-text">${this.data[i].html ? this.data[i].html : this.data[i].text}</span>
                    </div>
                `;
            }
            let selectAllHTML = '';
            if (this.options.selectAll === true || this.options.selectAll === 'true') {
                selectAllHTML = `<div class="pfw-multi-select-all">
                    <span class="pfw-multi-select-option-radio"></span>
                    <span class="pfw-multi-select-option-text">Select all</span>
                </div>`;
            }
            let template = `
                <div class="pfw-multi-select ${this.name}"${this.selectElement.id ? ' id="' + this.selectElement.id + '"' : ''} style="${this.width ? 'width:' + this.width + ';' : ''}${this.height ? 'height:' + this.height + ';' : ''}">
                    ${this.selectedValues.map(value => `<input type="hidden" name="${this.name}[]" value="${value}">`).join('')}
                    <div class="pfw-multi-select-header" style="${this.width ? 'width:' + this.width + ';' : ''}${this.height ? 'height:' + this.height + ';' : ''}">
                        <span class="pfw-multi-select-header-max">${this.options.max ? this.selectedValues.length + '/' + this.options.max : ''}</span>
                        <span class="pfw-multi-select-header-placeholder">${this.placeholder}</span>
                    </div>
                    <div class="pfw-multi-select-options" style="${this.options.dropdownWidth ? 'width:' + this.options.dropdownWidth + ';' : ''}${this.options.dropdownHeight ? 'height:' + this.options.dropdownHeight + ';' : ''}">
                        ${this.options.search === true || this.options.search === 'true' ? '<input type="text" class="pfw-multi-select-search" placeholder="Search...">' : ''}
                        ${selectAllHTML}
                        ${optionsHTML}
                    </div>
                </div>
            `;
            let element = document.createElement('div');
            element.innerHTML = template;
            return element;
        }
    
        _eventHandlers() {
            let headerElement = this.element.querySelector('.pfw-multi-select-header');
            this.element.querySelectorAll('.pfw-multi-select-option').forEach(option => {
                option.onclick = () => {
                    let selected = true;
                    if (!option.classList.contains('pfw-multi-select-selected')) {
                        if (this.options.max && this.selectedValues.length >= this.options.max) {
                            return;
                        }
                        option.classList.add('pfw-multi-select-selected');
                        if (this.options.listAll === true || this.options.listAll === 'true') {
                            if (this.element.querySelector('.pfw-multi-select-header-option')) {
                                let opt = Array.from(this.element.querySelectorAll('.pfw-multi-select-header-option')).pop();
                                opt.insertAdjacentHTML('afterend', `<span class="pfw-multi-select-header-option" data-value="${option.dataset.value}">${option.querySelector('.pfw-multi-select-option-text').innerHTML}</span>`);
                            } else {
                                headerElement.insertAdjacentHTML('afterbegin', `<span class="pfw-multi-select-header-option" data-value="${option.dataset.value}">${option.querySelector('.pfw-multi-select-option-text').innerHTML}</span>`);
                            }
                        }
                        this.element.querySelector('.pfw-multi-select').insertAdjacentHTML('afterbegin', `<input type="hidden" name="${this.name}[]" value="${option.dataset.value}">`);
                        this.data.filter(data => data.value == option.dataset.value)[0].selected = true;
                    } else {
                        option.classList.remove('pfw-multi-select-selected');
                        this.element.querySelectorAll('.pfw-multi-select-header-option').forEach(headerOption => headerOption.dataset.value == option.dataset.value ? headerOption.remove() : '');
                        this.element.querySelector(`input[value="${option.dataset.value}"]`) ?  this.element.querySelector(`input[value="${option.dataset.value}"]`).remove() : '';
                        this.data.filter(data => data.value == option.dataset.value)[0].selected = false;
                        selected = false;
                    }
                    if (this.options.listAll === false || this.options.listAll === 'false') {
                        if (this.element.querySelector('.pfw-multi-select-header-option')) {
                            this.element.querySelector('.pfw-multi-select-header-option').remove();
                        }
                        headerElement.insertAdjacentHTML('afterbegin', `<span class="pfw-multi-select-header-option">${this.selectedValues.length} selected</span>`);
                    }
                    if (!this.element.querySelector('.pfw-multi-select-header-option')) {
                        headerElement.insertAdjacentHTML('afterbegin', `<span class="pfw-multi-select-header-placeholder">${this.placeholder}</span>`);
                    } else if (this.element.querySelector('.pfw-multi-select-header-placeholder')) {
                        this.element.querySelector('.pfw-multi-select-header-placeholder').remove();
                    }
                    if (this.options.max) {
                        this.element.querySelector('.pfw-multi-select-header-max').innerHTML = this.selectedValues.length + '/' + this.options.max;
                    }
                    if (this.options.search === true || this.options.search === 'true') {
                        this.element.querySelector('.pfw-multi-select-search').value = '';
                    }
                    this.element.querySelectorAll('.pfw-multi-select-option').forEach(option => option.style.display = 'flex');
                    if (this.options.closeListOnItemSelect === true || this.options.closeListOnItemSelect === 'true') {
                        headerElement.classList.remove('pfw-multi-select-header-active');
                    }
                    this.options.onChange(option.dataset.value, option.querySelector('.pfw-multi-select-option-text').innerHTML, option);
                    if (selected) {
                        this.options.onSelect(option.dataset.value, option.querySelector('.pfw-multi-select-option-text').innerHTML, option);
                    } else {
                        this.options.onUnselect(option.dataset.value, option.querySelector('.pfw-multi-select-option-text').innerHTML, option);
                    }
                };
            });
            headerElement.onclick = () => headerElement.classList.toggle('pfw-multi-select-header-active');  
            if (this.options.search === true || this.options.search === 'true') {
                let search = this.element.querySelector('.pfw-multi-select-search');
                search.oninput = () => {
                    this.element.querySelectorAll('.pfw-multi-select-option').forEach(option => {
                        option.style.display = option.querySelector('.pfw-multi-select-option-text').innerHTML.toLowerCase().indexOf(search.value.toLowerCase()) > -1 ? 'flex' : 'none';
                    });
                };
            }
            if (this.options.selectAll === true || this.options.selectAll === 'true') {
                let selectAllButton = this.element.querySelector('.pfw-multi-select-all');
                selectAllButton.onclick = () => {
                    let allSelected = selectAllButton.classList.contains('pfw-multi-select-selected');
                    this.element.querySelectorAll('.pfw-multi-select-option').forEach(option => {
                        let dataItem = this.data.find(data => data.value == option.dataset.value);
                        if (dataItem && ((allSelected && dataItem.selected) || (!allSelected && !dataItem.selected))) {
                            option.click();
                        }
                    });
                    selectAllButton.classList.toggle('pfw-multi-select-selected');
                };
            }
            if (this.selectElement.id && document.querySelector('label[for="' + this.selectElement.id + '"]')) {
                document.querySelector('label[for="' + this.selectElement.id + '"]').onclick = () => {
                    headerElement.classList.toggle('pfw-multi-select-header-active');
                };
            }
            document.addEventListener('click', event => {
                if (!event.target.closest('.' + this.name) && !event.target.closest('label[for="' + this.selectElement.id + '"]')) {
                    headerElement.classList.remove('pfw-multi-select-header-active');
                }
            });
        }
    
        _updateSelected() {
            if (this.options.listAll === true || this.options.listAll === 'true') {
                this.element.querySelectorAll('.pfw-multi-select-option').forEach(option => {
                    if (option.classList.contains('pfw-multi-select-selected')) {
                        this.element.querySelector('.pfw-multi-select-header').insertAdjacentHTML('afterbegin', `<span class="pfw-multi-select-header-option" data-value="${option.dataset.value}">${option.querySelector('.pfw-multi-select-option-text').innerHTML}</span>`);
                    }
                });
            } else {
                if (this.selectedValues.length > 0) {
                    this.element.querySelector('.pfw-multi-select-header').insertAdjacentHTML('afterbegin', `<span class="pfw-multi-select-header-option">${this.selectedValues.length} selected</span>`);
                }
            }
            if (this.element.querySelector('.pfw-multi-select-header-option')) {
                this.element.querySelector('.pfw-multi-select-header-placeholder').remove();
            }
        }
    
        get selectedValues() {
            return this.data.filter(data => data.selected).map(data => data.value);
        }
    
        get selectedItems() {
            return this.data.filter(data => data.selected);
        }
    
        set data(value) {
            this.options.data = value;
        }
    
        get data() {
            return this.options.data;
        }
    
        set selectElement(value) {
            this.options.selectElement = value;
        }
    
        get selectElement() {
            return this.options.selectElement;
        }
    
        set element(value) {
            this.options.element = value;
        }
    
        get element() {
            return this.options.element;
        }
    
        set placeholder(value) {
            this.options.placeholder = value;
        }
    
        get placeholder() {
            return this.options.placeholder;
        }
    
        set name(value) {
            this.options.name = value;
        }
    
        get name() {
            return this.options.name;
        }
    
        set width(value) {
            this.options.width = value;
        }
    
        get width() {
            return this.options.width;
        }
    
        set height(value) {
            this.options.height = value;
        }
    
        get height() {
            return this.options.height;
        }
    
    }
    document.querySelectorAll('[data-pfw-multi-select]').forEach(select => new PwfMultiSelect(select));

    const pfwSetRequest = async (data) =>{
        const pfwTabFaqWrapper = document.getElementById('pfw-tab-faq-list');
        if( !pfwTabFaqWrapper ) return false;
        const endpoint = `product-faq-woocommerce/v1/set-faq`;
        const response = await pfwFetch(endpoint, data);
        
        if(response){
            pfwTabFaqWrapper.innerHTML = '';
            let faqMarkup = '';
            const faqs = response.faq_posts;
            if(faqs.length > 0){
                faqs.forEach((faq) =>{
                    faqMarkup += `<div class="pfq-tab-faq">
                        ${ faq.title ? `<h3>${faq.title}</h3>` : '' }
                        ${ faq.content ? `<div>${faq.content}</div>` : '' }
                    </div>`;
                });
            }

            pfwTabFaqWrapper.innerHTML = faqMarkup;
        }
    }

    const pfwGetRequest = async () =>{
        let pfwFaqSorting = document.querySelector('.pfw-tab-faq-sorting');
        const selectHead  = document.querySelector('.pfw-multi-select-header');
        if( !pfwFaqSorting ) return false;
        let productId   = pfwFaqSorting.getAttribute('data-product-id') ?? 0;

        let data = {
            product_id: Number(productId)
        }

        const endpoint = `product-faq-woocommerce/v1/get-faq`;
        const response = await pfwFetch(endpoint, data);

        if( response && selectHead ){
            selectHead.innerText = '';
            let data = response.faq_posts;
            if( data && data.length > 0 ){
                const pfwSelectItems    = document.getElementsByClassName('pfw-multi-select-option');
                if( pfwSelectItems.length > 0 ){
                    let selectedMarkup = '';
                    for( let option of pfwSelectItems ){
                        let pfwFaqId = option.getAttribute('data-value') ? Number( option.getAttribute('data-value') ) : 0;
                        //const selected = data.some( item => item.id == pfwFaqId );
                        const selected = data.find(item => item.id == pfwFaqId)
                        if(selected.id && selected.id == pfwFaqId ){
                            option.classList.add('pfw-multi-select-selected');
                            selectedMarkup +=`<span class="pfw-multi-select-header-option" data-value="${selected.id}">${selected.title}</span>`;
                        }
                    }

                    selectHead.innerHTML = selectedMarkup;
                }
            } 
        }

    }

    const pfwGetSet = (action = 'add', faqId) =>{
        let selectTabFaqs = document.querySelector('.pfw-tab-faq-sorting');
        if( selectTabFaqs ){
            let productId = selectTabFaqs.getAttribute('data-product-id') ?? 0;

            let data = {
                action: action,
                product_id: Number(productId),
                faq_id:  Number(faqId)
            }

            pfwSetRequest(data);
        }
    }

    const pfwMain = () =>{
        pfwGetRequest();
        // const selectTabFaqs = document.getElementById('pfw-tab-faq-select');

        // if( selectTabFaqs ){
        //     const productId = selectTabFaqs.getAttribute('data-product-id');

        //     let data = {
        //         action: 'add',
        //         product_id: Number(productId)
        //     }

        //     selectTabFaqs.addEventListener('change', function(e){
        //         const faqId = Number(this.value);
        //         if(faqId){
        //             data.faq_id = faqId;
        //             pfwSetFaq(data);
        //             console.log(data);
        //         }
        //     })
        // }
    }

    const pfwLoader = () => '<span class="spinner is-active"></span>';

    document.addEventListener('DOMContentLoaded', () => {
      pfwMain();
    });

})();