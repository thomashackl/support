STUDIP.SupportPlugin = {

    init: function () {
        $('#supportlinks').sortable({
            axis: 'y',

            stop: function () {
                var link_ids = {};
                link_ids.link_ids = {};
                $('#supportlinks').find('div.supportlink').each(function () {
                    link_ids.link_ids[$(this).attr('id')] = $(this).attr('id');
                });

                $.ajax({
                    type: 'POST',
                    url: $('#supportlinks').data('sort-url'),
                    data: link_ids
                });
                location.reload();
            }
        });
    },

    selectSearchResult: function(id, name) {
        var textSrc = $('#supportsearch').data('redirect-url').split('?');
        var url = textSrc[0]+'/'+id;
        window.location.href = url;
    }

};
