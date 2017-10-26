function getSelectizedCombo(selector, url, callback)
{
    var options = getSelectizedBaseOptions(selector, url, callback);
    options.maxItems = 1;
    $(selector).selectize(options);
}

function getSelectizedMultiCombo(selector, url, callback)
{
    var options = getSelectizedBaseOptions(selector, url, callback);
    $(selector).selectize(options);
}

function getSelectizedTextBox(selector, url, associatedHiddenInputSelector)
{
    var options = getSelectizedBaseOptions(selector, url);
    options.maxItems = 1;
    options.create = true;
    options.onChange = function(value) {
       $(associatedHiddenInputSelector).val(value);
       $(associatedHiddenInputSelector).trigger('change');
    };
    options.type = function(value) {
       $(associatedHiddenInputSelector).val(-1);
       $(associatedHiddenInputSelector).trigger('change');
    };

    $(selector).selectize(options);
}

function getSelectizedBaseOptions(selector, url, callback)
{
    return {
        valueField: 'id',
        labelField: 'toString',
        searchField: 'toString',
        dropdownParent: 'body',
        loadThrottle: 700,
        loadingClass: 'itemsLoading',
        options: [
        ],
        create: false,
        load: function(query, callback) {
            if (!query.length) return callback();
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                data: {
                    query: query,
                    page_limit: 10,
                },
                error: function() {
                    callback();
                },
                success: function(res) {
                    callback(res.data);
                }
            });
        }
    };
}


function addAndSelectToSelectizedCombo(selector, id, value)
{
    addSelectizeOption(selector, id, value);
    selectSelectizeOption(selector, id);
}

function clearSelectize(selector)
{
    var selectize_tags = $(selector)[0].selectize;
    selectize_tags.clear();
    selectize_tags.clearOptions();
    selectize_tags.refreshItems();
}

function addSelectizeOption(selector, id, value)
{
    var selectize_tags = $(selector)[0].selectize;
    selectize_tags.addOption({
        id: id,
        toString: value,
    });
}

function selectSelectizeOption(selector, id)
{   
    var selectize_tags = $(selector)[0].selectize;
    selectize_tags.addItem(id, true);
}

