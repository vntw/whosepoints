(function ($) {
    $.fn.tableShortener = function () {
        var maxWidth = 296,
            lblMore = 'Show More',
            lblLess = 'Show Less',
            footer = '<div class="panel-footer active text-center"><a href="" class="tbl-show-all">Show More</a></div>';

        return this.each(function () {
            var $panel = $(this),
                $body = $('div.panel-body', $panel),
                $footer = $(footer),
                tblHeight = $('table', $panel).height();

            if (tblHeight > maxWidth) {
                $panel.addClass('ts-enabled');
                $body.after($footer);

                $('a', $footer).click(function (e) {
                    e.preventDefault();

                    var active = $footer.hasClass('active'),
                        $link = $(this);

                    if (active) {
                        $body.animate({height: tblHeight}, 350, function () {
                            $footer.removeClass('active');
                            $link.text(lblLess);
                        });
                    } else {
                        $body.animate({height: maxWidth + 'px'}, 350, function () {
                            $footer.addClass('active');
                            $link.text(lblMore);
                        });
                    }
                });
            }
        });
    };
}(jQuery));