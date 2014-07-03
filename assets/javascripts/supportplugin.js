STUDIP.SupportPlugin = {

    selectSearchResult: function(id, name) {
        var textSrc = $('#supportsearch').data('redirect-url').split('?');
        var url = textSrc[0]+'/'+id;
        window.location.href = url;
    }

}
