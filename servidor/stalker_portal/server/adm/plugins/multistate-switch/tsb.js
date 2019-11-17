(function ($) {

    $.fn.threestatebutton = function (params) {
        return this.each(function () {
            var casing = $(document.createElement("div"))
                    .addClass("tsb-casing")
                    .attr("data-current","none")
                    //.css("width", params.statecount + "em")
                    .append($(document.createElement("span"))
                            .addClass("tsb-ball")
                            .css("left", params.state + "em")
                            .attr("data-tbsstate", params.state));

            var signs = ["fa-check","fa-times","fa-minus"];
            var source = $(this);
            source.append(casing);
            for (var i = 0; i < params.statecount; i++) {
                casing.append($(document.createElement("span"))
                        .addClass("tsb-state fa "+signs[i])
                        .attr("data-tbsstate", i)
                        .css("left", (24*i+16*i) + "px"));
            }

            casing.children(".tsb-state").click(function (e) {
                var newpos = $(this).css("left");
                var state = $(this).attr("data-tbsstate");
                var oldstate = $(this).siblings(".tsb-ball").attr("data-tbsstate");
                casing.attr("data-current",state);
                //if (state !== oldstate) {
                    $(this).siblings(".tsb-ball")
                    .animate({left: newpos}, 150, function () {
                        $(this).attr("data-tbsstate", state);
                        params.stateChanged(e, state, source);
                    });
                //}
            });
        });

    };

}(jQuery));