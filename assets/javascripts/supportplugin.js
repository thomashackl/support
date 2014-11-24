STUDIP.SupportPlugin = {

    init: function () {
        $('#supportlinks').sortable({
            axis: 'y',

            stop: function () {
                var postData = {};
                postData.link_ids = {};
                $('#supportlinks').find('div.supportlink').each(function () {
                    postData.link_ids[$(this).attr('id')] = $(this).attr('id');
                });

                var url = $('#supportlinks').data('sort-url');
                url = url.substring(1, url.length-1);

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: postData
                });

            }
        });
        $('#supportfaqs').sortable({
            axis: 'y',

            stop: function () {
                var postData = {};
                postData.faq_ids = {};
                $('#supportfaqs').find('article').each(function () {
                    postData.faq_ids[$(this).attr('class')] = $(this).attr('class');
                });

                var url = $('#supportfaqs').data('sort-url');
                url = url.substring(1, url.length-1);

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: postData
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
