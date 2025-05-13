class DataViewer
{
    constructor(className, activeClassName) {
        this.className = className;
        this.activeClassName = activeClassName;
    }

    handleEvent(e) {
        e.preventDefault();
        
        switch (e.currentTarget.id) {
            case 'themes_view':
                if (e.target.dataset.clickable && !e.target.classList.contains(this.activeClassName)) {
                    this.chooseMenuItem(e.target);
                }
                break;
            case 'subthemes_view':
                let item = false;
                
                if (e.target.tagName == 'IMG') {
                    let action = e.target.parentNode.dataset.actionType;
                    item = e.target.parentNode.parentNode.parentNode;
                    if (item.dataset.itemId && action == 'del') {
                        let callback = this.refreshListItems.bind(this, {});
        
                        if (confirm('Вы действительно хотите удалить ' + appCategory.categoryName)) {
                            this.sendRequest(appCategory.getUrl(action), {id: item.dataset.itemId}, callback);
                        }
                    }

                } else if (e.target.tagName == 'DIV') {
                    if (!e.isTrusted) {
                        item = e.target;
                    } else if (e.target.parentNode.dataset.clickable && !e.target.parentNode.classList.contains(this.activeClassName)) {
                        item = e.target.parentNode;
                    }

                    if (item) {
                        this.chooseListItem(item);
                    }
                }
                break;
        }
    }

    sendRequest(url, data, callback) {
        $.ajax({        
            url: url,
            data: data,
            type: 'POST',
            success: callback,
            error: function(jqXHR, errMsg) {
                console.log(errMsg);
                appCategory.restoreActive();
            }
        });
    }

    chooseMenuItem(elem) {
        appCategory.changeActive(elem.dataset.categoryKey);
        let callback = this.updateSubthemesView.bind(this, elem, false);
        this.sendRequest(appCategory.getUrl('list'), {}, callback);
    }

    updateSubthemesView(elem, reload, data) {
        if (!reload) {
            this.setAddButtonTitle();
            this.activeClear();
            elem.classList.add(this.activeClassName);
        }

        document.getElementById('subthemes_view').innerHTML = data;
        let event = new Event('click', {bubbles: true, cancelable: true});
        let selectedItem = document.getElementsByClassName(this.className + ' ' + this.activeClassName)[0];
        selectedItem.dispatchEvent(event);
    }

    setAddButtonTitle() {
        document.getElementById('item_edit_form').title = appCategory.buttonTitle;
    }

    chooseListItem(elem) {
        let callback = this.updateContentView.bind(this, elem);
        this.sendRequest(appCategory.getUrl('item'), {id: elem.dataset.itemId}, callback);
    }

    updateContentView(elem, data) {
        if (data.fail) {
            notifyMessageShow(data.message);
            return false;
        }
        
        document.getElementById('content_view').innerHTML = data;
        if (!elem.classList.contains(this.activeClassName)) {
            this.activeClear(this.className + ' ' + this.activeClassName);
            elem.classList.add(this.activeClassName);
        }
    }

    activeClear(selector=this.activeClassName) {
        for (let element of document.getElementsByClassName(selector)) {
            element.classList.remove(this.activeClassName);
        }
    }

    refreshListItems(obj) {
        let callback = this.updateSubthemesView.bind(this, null, true);
        this.sendRequest(appCategory.getUrl('list'), obj, callback);
    }
}

class appMenuCategories
{
    constructor() {
        this.categories = false;
        this.active = false;
        this.previous = false;
    }

    init(categories, active) {
        this.categories = categories;
        this.changeActive(active);
        dataHandler.setAddButtonTitle();
    }

    setActive(active) {
        if (this.categories[active]) {
            return active;
        } 
        return false;
    }

    changeActive(active) {
        this.previous = this.active;
        this.active = this.setActive(active);
    }

    restoreActive() {
        this.active = this.setActive(this.previous);
        this.previous = false;
    }

    get buttonTitle() {
        if (this.active) {
            return this.categories[this.active].add_btn_title + this.categories[this.active].name;
        }
        return '';
    }

    getUrl(key) {
        if (this.active) {
            return this.categories[this.active].urls[key];
        }
        return '';
    }

    getFormTitle(key) {
        if (this.active) {
            return this.categories[this.active].modal_titles[key];
        }
        return '';
    }

    get categoryName() {
        return this.categories[this.active].name;
    }
}

let appCategory = new appMenuCategories();
let dataHandler = new DataViewer("list-item", "active-item");

function notifyMessageShow(message) {
    $('#wmInfo-text').html(message);
    $('#wmInfo').modal('show');
    const timerId = setTimeout(() => { $('#wmInfo').modal('hide'); }, 3500);
}

document.addEventListener('DOMContentLoaded', () => {  
    document.getElementById('themes_view').addEventListener('click', dataHandler);
    document.getElementById('subthemes_view').addEventListener('click', dataHandler);
    
    $('#wModal').on('show.bs.modal', function(e) {
        let data = {};
        const url = appCategory.getUrl(e.relatedTarget.dataset.actionType);
        if (e.relatedTarget.dataset.itemId) {
            data.id = e.relatedTarget.dataset.itemId;
        }
        
        $('#wModal-label').text(appCategory.getFormTitle(e.relatedTarget.dataset.actionType));
        $('#wModal-form').load(url, data, function(data) {
            
        });
    });
})