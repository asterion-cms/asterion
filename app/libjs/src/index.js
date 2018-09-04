$(function(){

    activateBasicElements();
    activateSortable();
    activateNestedForms();
    activateMultipleActions();
    activateAutocomplete();
    activateDatePicker();
    activateCK();
    activateMaps();

});

/**
* Activate the basic elements for the administration area.
**/
function activateBasicElements() {

    /**
    * DELETE an element.
    * Function to show a message before deleting an element.
    **/
    $(document).on('click', '.iconDelete a', function(evt){
        evt.stopImmediatePropagation();
        let parentIcon = $(this).parents('.iconDelete').first();
        let parentContainer = $(this).parents('.lineAdmin').first();
        if (parentIcon.hasClass('iconDeleteAjax')) {
            if (confirm(info_translations.js_messageDelete)) {
                $.ajax($(this).attr('href')).done(function(htmlResponse) { parentContainer.slideUp(300, function() { parentContainer.remove(); }); });
            }
            return false;
        } else {
            return confirm(info_translations.js_messageDelete);
        }
    });

    /**
    * DELETE an image from an object.
    **/
    $(document).on('click', '.formFieldsImageDelete', function(evt){
        evt.stopImmediatePropagation();
        let parentContainer = $(this).parents('.formFieldsImage').first();
        $.ajax($(this).data('url')).done(function(htmlResponse) { if (htmlResponse.label && htmlResponse.label=='success') parentContainer.remove(); });
    });

    /**
    * RESET an object.
    * Function to show a message before resetting an object.
    **/
    $(document).on('click', 'a.resetObject', function(event){
        return confirm(info_translations.js_resetObjectMessage);
    });

    /**
    * ORDER elements in a list.
    **/
    $(document).on('change', '.orderActions select', function(evt){
        window.location = $(this).parents('.orderActions').data('url') + $(this).val();
    });

    /**
    * CHECKBOX for certain select elements.
    **/
    $('.selectCheckbox').each(function(index, ele){
        var selectItem = $(ele).find('select');
        var checkboxItem = $(ele).find('input[type=checkbox]');
        $(selectItem).attr('disabled', !$(checkboxItem).is(':checked'));
        $(checkboxItem).click(function(){
            $(selectItem).attr('disabled', !$(checkboxItem).is(':checked'));
        });
    });

    /**
    * SELECT2 for the select items in the forms
    **/
    $('.formAdmin .select2 select').select2();

}

/**
* MULTIPLE actions in a list.
**/
function activateMultipleActions() {

    // Activate the select/deselect all items.
    $(document).on('click', '.multipleActionCheckAll input', function(){
        $('.lineAdminCheckbox input').prop('checked', $(this).prop('checked'));
    });

    // Activate the action with the multiple items.
    $(document).on('click', '.multipleOption', function(event){
        event.stopImmediatePropagation();
        let postValues = [];
        $('.lineAdminCheckbox input').each(function(index, ele){
            if ($(ele).prop('checked')==true) postValues.push($(ele).attr('name'));
        });
        if (postValues.length > 0) {
            $.post($(this).data('url'), {'list-ids': postValues}).done(function() { location.reload(); });
        }
    });

}

/**
* NESTED elements in a form.
*/
function activateNestedForms() {

    // Disable multiple forms
    $('.nestedFormFieldEmpty :input').attr('disabled', true);

    // Action to add an element to the form.
    $(document).on('click', '.nestedFormFieldAdd', function(event){
        event.stopImmediatePropagation();
        var self = $(this);
        var container = self.parents('.nestedFormField');
        var newForm = container.find('.nestedFormFieldEmpty');
        var formsContainer = container.find('.nestedFormFieldIns');
        var newFormClone = newForm.clone();
        newFormClone.removeClass('nestedFormFieldEmpty');
        newFormClone.addClass('nestedFormFieldObject');
        newFormClone.find(':input').attr('disabled', false);
        newFormClone.html(newFormClone.html().replace(/\#ID_MULTIPLE#/g, randomString()));
        newFormClone.appendTo(formsContainer);
        $('.fieldOrd').each(function(index, ele){ $(ele).val(index + 1);});
    });

    // Action to delete an element of the form.
    $(document).on('click', '.nestedFormFieldDelete', function(event){
        event.stopImmediatePropagation();
        var self = $(this);
        var container = $(this).parents('.nestedFormFieldObject');
        var actionDelete = $(this).data('url');
        if (!actionDelete) {
            container.remove();
        } else {
            if (confirm(info_translations.js_messageDelete)) {
                $.ajax(actionDelete)
                .done(function(htmlResponse) {
                    container.remove();
                });
            }
        }
    });

}

/**
* SORT a list of elements.
*/
function activateSortable() {

    // Regular list
    $('.sortableList').each(function(index,ele){
        $(ele).sortable({
            handle:'.iconHandle',
            update: function() {
                $.post($(this).data('url'),{'newOrder[]': $(ele).find('.lineAdmin').toArray().map(item => $(item).data('id'))});
            }
        });
    });

    // Nested list
    $('.nestedFormFieldSortable').each(function(index, ele){
        $(ele).sortable({
            handle:'.nestedFormFieldOrder',
            update: function() {
                $('.fieldOrd').each(function(index, ele){ $(ele).val(index + 1);});
            }
        });
    });

}

/**
* AUTOCOMPLETE for certain elements in a form.
*/
function activateAutocomplete() {
    $('.autocompleteItem input').each(function(index, ele){
        $(ele).autocomplete({
            minLength: 2,
            source: function(request, response) { $.getJSON($(ele).parents('.autocompleteItem').data('url'), { term: split(request.term).pop() }, response ); },
            focus: function() { return false; },
            select: function(event, ui) {
                let terms = split(this.value);
                terms.pop();
                terms.push(ui.item.value);
                terms.push("");
                this.value = terms.join(", ");
                return false;
            }
        });
    });
}

/**
* DATE PICKER for certain elements in a form.
**/
function activateDatePicker() {
    $('.dateText input').each(function(index, ele){
        var dateFormatView = 'yy-mm-dd';
        $(ele).datepicker({ 'firstDay': 1, 'dateFormat': dateFormatView });
    });
}

/**
* CKEditor for certain elements in a form.
**/
function activateCK() {

    $('.ckeditorArea textarea').each(function(index, ele){
        if ($(ele).attr('rel') != 'ckeditor') {
            $(ele).attr('rel', 'ckeditor');
            if ($(ele).attr('id')=='' || $(ele).attr('id')==undefined) {
                $(ele).attr('id', randomString());
            }
            CKEDITOR.replace($(ele).attr('id'), {
                height: '450px',
                toolbar: [
                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
                    '/',
                    { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
                    { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                    { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                    { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
                    '/',
                    { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                    { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                    { name: 'tools', items: [ 'Maximize', 'ShowBlocks', 'CodeSnippet' ] },
                ]
            });
        }
    });

    $('.ckeditorAreaSimple textarea').each(function(index, ele){
        if ($(ele).attr('rel') != 'ckeditor') {
            $(ele).attr('rel', 'ckeditor');
            if ($(ele).attr('id')=='' || $(ele).attr('id')==undefined) {
                $(ele).attr('id', randomString());
            }
            CKEDITOR.replace($(ele).attr('id'), {
                height: '250px',
                toolbar: [
                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup', 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Link', 'Unlink', 'Image'] },
                    '/',
                    { name: 'styles', items: [ 'Format', 'Font', 'FontSize', '-', 'TextColor', 'BGColor'] }
                ]
            });
        }
    });

}

/**
* Activate the GoogleMaps selection for certain elements in a form.
**/
function activateMaps() {
    if ($('.pointMap').length > 0) {
        $('.pointMap').each(function(index, ele){
            var initLat = $(ele).data('initlat') * 1;
            var initLng = $(ele).data('initlng') * 1;
            var initZoom = $(ele).data('initzoom') * 1;
            var mapLat = $(ele).find('.map').data('lat') * 1;
            var mapLng = $(ele).find('.map').data('lng') * 1;
            var mapZoom = $(ele).find('.map').data('zoom') * 1;
            var mapIns = $(ele).find('.mapIns');
            var inputLat = $(ele).find('.inputLat');
            var inputLng = $(ele).find('.inputLng');
            var checkboxShowHide = $(ele).find('.showHide input[type=checkbox]');
            var activateSingleMap = function() {
                mapLat = (mapLat!=0) ? mapLat : initLat;
                mapLng = (mapLng!=0) ? mapLng : initLng;
                mapZoom = (mapZoom!=0) ? mapZoom : initZoom;
                inputLat.val(mapLat);
                inputLng.val(mapLng);
                var mapOptions = {
                    zoom: mapZoom,
                    center: new google.maps.LatLng(mapLat, mapLng)
                };
                var mapEle = new google.maps.Map(document.getElementById(mapIns.attr('id')), mapOptions);
                markerPort = new google.maps.Marker({
                    position: new google.maps.LatLng(mapLat, mapLng),
                    map: mapEle
                });
                google.maps.event.addListener(mapEle, 'click', function(newPosition) {
                    markerPort.setPosition(newPosition.latLng);
                    inputLat.val(newPosition.latLng.lat());
                    inputLng.val(newPosition.latLng.lng());
                });
            }
            if (checkboxShowHide.length > 0) {
                if (mapLat=='' || mapLng=='') {
                    checkboxShowHide.attr('checked', false);
                    $('.map').hide();
                } else {
                    checkboxShowHide.attr('checked', true);
                    activateSingleMap();
                }
                checkboxShowHide.click(function(){
                    if ($(this).is(':checked')) {
                        $('.map').show();
                        activateSingleMap();
                    } else {
                        $('.map').hide();
                        inputLat.val('');
                        inputLng.val('');
                    }
                });
            } else {
                activateSingleMap();
            }
        });
    }
}


function split(val) {
    return val.split( /,\s*/ );
}

function randomString() {
    return Math.random().toString(36).substring(7);
}
