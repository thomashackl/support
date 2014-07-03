STUDIP.SupportPlugin = {

    selectSearchResult: function(id, name) {
        window.location.href = $('input[name="searchterm_parameter"]').data('redirect-url')+'/'+id;
    }

}
