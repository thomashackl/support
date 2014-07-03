STUDIP.SupportPlugin = {

    selectSearchResult: function(id, name) {
        window.location.href = $('#supportsearch').data('redirect-url')+'/'+id;
    }

}
