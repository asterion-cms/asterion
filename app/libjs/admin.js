var loadingDiv = false;

$(document).ready(function() {
    ajaxAdmin();
    activateCK();
    activateMaps();
    $('form').areYouSure();
});

$(window).load(function() {
});

$(window).resize(function() {
});

function ajaxAdmin() {

    //NESTED FORMS
    //Disable multiple forms
    $('.nestedFormFieldEmpty :input').attr('disabled', true);
    //Add
    $('.nestedFormFieldAdd').click(function(event){
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
        ajaxAdmin();
        updateFieldOrd();
    });
    //Delete
    $('.nestedFormFieldDelete').click(function(event){
        event.stopImmediatePropagation();
        var self = $(this);
        var container = $(this).parents('.nestedFormFieldObject');
        var actionDelete = $(this).attr('rel');
        if (actionDelete==undefined || actionDelete=='') {
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

    //SIMPLE DELETE
    $('.iconDelete a').click(function(evt){
        evt.stopImmediatePropagation();
        return confirm(info_translations.js_messageDelete);
    });

    //RESET OBJECT
    $('a.resetObject').click(function(event){
        return confirm(info_translations.js_resetObjectMessage);
    });

    //MULTIPLE ACTIONS
    $('.multipleActionCheckAll input').click(function(){
        $('.lineAdminCheckbox input').prop('checked', $(this).prop('checked'));
    });
    $('.multipleOption').click(function(event){
        event.stopImmediatePropagation();
        var postValues = [];
        $('.lineAdminCheckbox input').each(function(index, ele){
            if ($(ele).prop('checked')==true) {
                postValues.push($(ele).attr('name'));
            }
        });
        if (postValues.length > 0) {
            $.post($(this).attr('rel'), {'list-ids': postValues})
            .done(function( data ) {
                location.reload();
            });
        }
    });


    //SORT DIVS
    $('.sortableList .listContent').each(function(index,ele){
        $(ele).sortable({
            handle:'.iconHandle',
            update: function() {
                var urlLoad = $(this).parents('.sortableList').first().attr('rel');
                var newOrder = Array();
                $(ele).find('.lineAdmin').each(function(indexIns,eleIns){
                    newOrder.push($(eleIns).attr('rel'));
                });
                $.post(urlLoad,{'newOrder[]':newOrder});
            }
        });
    });
    $('.nestedFormFieldSortable').each(function(index, ele){
        $(ele).sortable({
            handle:'.nestedFormFieldOrder',
            update: function() {
                updateFieldOrd();
            }
        });
    });

    //CHANGE ORDER
    $('.orderActions select').change(function(evt){
        var url = $(this).parents('.orderActions').attr('rel') + $(this).val();
        window.location = url;
    });

    //AUTOCOMPLETE
    $('.autocompleteItem input').each(function(index, ele){
        $(ele).autocomplete({
            minLength: 2,
            source: function(request, response) {
                                $.getJSON($(ele).parents('.autocompleteItem').attr('rel'), {
                                        term: extractLast(request.term)
                                }, response );
                        },
            focus: function() {
                return false;
            },
            select: function(event, ui) {
                var terms = split(this.value);
                terms.pop();
                terms.push(ui.item.value);
                terms.push("");
                this.value = terms.join(", ");
                return false;
            }
        });
    });

    //DATE PICKER
    $('.dateText input').each(function(index, ele){
        var dateFormatView = 'yy-mm-dd';
        if ($(ele).attr('rel')=='dayMonth') {
            var dateFormatView = 'dd-mm';
        }
        $(ele).datepicker({
            'firstDay': 1,
            'dateFormat': dateFormatView
        });
    })

    //SELECT CHECKBOX
    $('.selectCheckbox').each(function(index, ele){
        var selectItem = $(ele).find('select');
        var checkboxItem = $(ele).find('input[type=checkbox]');
        $(selectItem).attr('disabled', !$(checkboxItem).is(':checked'));
        $(checkboxItem).click(function(){
            $(selectItem).attr('disabled', !$(checkboxItem).is(':checked'));
        });
    });

    //SELECT TRIGGERS
    $('.selectChange').each(function(index, ele){
        var trigger = $(ele).find('.selectTrigger');
        var change = $(ele).find('.selectTarget');
        trigger.find('select').change(function(evt) {
            var optionsPost = {};
            trigger.find('select').each(function(indexIns, eleIns){
                optionsPost[$(eleIns).attr('name')] = $(eleIns).val();
            });
            $.post(trigger.attr('rel'), optionsPost)
            .done(function(data) {
                change.html(data);
            });
        });
    });

    //ANNOTATIONS
    var activateAnnotations = function() {
        $('.annotationAdminOption').click(function(evt){
            evt.stopImmediatePropagation();
            var container = $(this).parents('.annotationAdmin');
            $.get($(this).attr('rel'), function(response){
                container.replaceWith(response);
                activateAnnotations();
            });
        });
        $('.responsesAdminOption').click(function(evt){
            evt.stopImmediatePropagation();
            var container = $(this).parents('.responsesAdmin');
            $.get($(this).attr('rel'), function(response){
                container.replaceWith(response);
                activateAnnotations();
            });
        });
    }
    activateAnnotations();

    //ACCORDION
    var accordionSelectSetup = function(ele) {
        var eleSelect = $(ele).find('.accordionTrigger select').first();
        var eleSwitchValue = $(ele).find('.accordionTrigger').first().attr('rel');
        var eleContent = $(ele).find('.accordionContent').first();
        if (eleSelect.val() == eleSwitchValue) {
            eleContent.show();
        } else {
            eleContent.hide();
        }
    }
    $('.accordionSelect').each(function(index, ele){
        accordionSelectSetup($(ele));
        $(ele).on('change', function(evt){
            evt.stopImmediatePropagation();
            accordionSelectSetup($(ele));
        });
    });

    $('.orderModifyTrigger').click(function(evt){
        evt.stopImmediatePropagation();
        evt.preventDefault();
        $('.orderModifyContent').toogle();
    });

}

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

function updateFieldOrd() {
    $('.fieldOrd').each(function(index, ele){
        $(ele).val(index + 1);
    });
}

function split( val ) {
    return val.split( /,\s*/ );
}

function extractLast( term ) {
    return split( term ).pop();
}

function equalHeights(elements) {
    var maxHeight = 0;
    elements.each(function(index,ele){
        if ($(ele).height() > maxHeight) {
            maxHeight = $(ele).height();
        }
    });
    elements.height(maxHeight);
}

function randomString() {
    return Math.random().toString(36).substring(7);
}
