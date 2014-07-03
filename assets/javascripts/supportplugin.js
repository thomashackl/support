STUDIP.SupportPlugin = {

    init: function () {
        var textSrc = $('div#supportlinks').data('sort-url').split('?');
        var url = textSrc[0];
        $('div#supportlinks').sortable({
            axis: 'y',
            items: "div.supportlink",
            handle: 'tr.handle',

            stop: function () {
                // iterate over the statusgroups and collect the ids
                var link_ids = {};
                var link_ids.link_ids = {};
                $('div#supportlinks').find('div.supportlink').each(function () {
                    link_ids.link_ids[$(this).attr('id')] = $(this).attr('id');
                });

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: link_ids
                });
            }
        });
    },

    selectSearchResult: function(id, name) {
        var textSrc = $('#supportsearch').data('redirect-url').split('?');
        var url = textSrc[0]+'/'+id;
        window.location.href = url;
    }

};
