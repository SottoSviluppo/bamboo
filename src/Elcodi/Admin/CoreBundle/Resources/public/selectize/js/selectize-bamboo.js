function getSelectizedCombo(selector, url, id, value)
{
    $(selector).selectize({
        maxItems: 1,
        valueField: 'id',
        labelField: 'toString',
        searchField: 'toString',
        loadingClass: 'itemsLoading',
        loadThrottle: 700,
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
    });
}

function getSelectizedMultiCombo(selector, url, id, value)
{
    $(selector).selectize({
        valueField: 'id',
        labelField: 'toString',
        searchField: 'toString',
        loadingClass: 'itemsLoading',
        loadThrottle: 700,
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
    });
}

function addAndSelectToSelectizedCombo(selector, id, value)
{
    var selectize_tags = $(selector)[0].selectize;
    selectize_tags.clear();
    selectize_tags.clearOptions();
    selectize_tags.refreshItems();
    selectize_tags.addOption({
        id: id,
        toString: value,
    });
    selectize_tags.addItem(id);
    selectize_tags.setValue(id, false);
}