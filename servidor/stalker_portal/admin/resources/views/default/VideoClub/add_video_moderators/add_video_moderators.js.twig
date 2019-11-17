
        var conf = {
            form: '#form_',
            lang : '{{ app.js_validator_language }}',
            showHelpOnFocus : true,
            validateHiddenInputs: true,
            ignore: ['.ignore'],
            modules: 'jsconf',
            onSuccess: function () {
                $(this.form).get(0).submit();
                return false;
            },
            onError: function () {
                return false;
            }
        };

        function yelp() {
            $(document).ready(function () {

                $.validate(conf);

                $(document).on('change', '#form_mac', function (e) {
                    var _this = $(this);
                    _this.next('div').removeClass('bg-danger').css('visibility', 'hidden').html('&nbsp;');

                    if ($.trim(_this.val()) == '') {
                        return;
                    } else {
                        var mac_val = $.trim(_this.val());
                        var regExp = /([0-9a-fA-F]{2}([:]|$)){6}$/ig;
                        if (!regExp.test(mac_val)) {
                            _this.next('div').append('<i class="txt-danger fa fa-ban"></i> ' + "{{ 'Error: Not valid mac address'|trans }}").css('visibility', 'visible');
                            return;
                        }
                    }

                    $.ajax({
                        url: 'video-club/check-moderator-mac',
                        type: 'POST',
                        data: {mac: _this.val(), id: $("#form_id").val()},
                        success: function (data) {
                            if (data.success) {
                                _this.next('div').append('<i class="txt-success fa fa-check"></i> ' + data.chk_rezult).css('visibility', 'visible');
                            } else {
                                JSErrorModalBox(data);
                            }
                        },
                        error: function (data) {
                            if (typeof (data.responseJSON) != 'undefined' && typeof (data.responseJSON.error) != 'undefined') {
                                _this.next('div').append('<i class="txt-danger fa fa-ban"></i> ' + data.responseJSON.error).css('visibility', 'visible');
                            } else {
                                JSErrorModalBox(data.responseJSON);
                            }
                        },
                        dataType: "json",
                        async: false
                    });
                });

                $(document).on("click", "#form_save", function (e) {
                    e.stopPropagation();
                    e.preventDefault();

                    if ($(conf.form).isValid({}, conf, true)) {
                        conf.onSuccess();
                    } else {
                        conf.onError();
                    }
                    return false;
                });
            });
        }
        document.addEventListener("DOMContentLoaded", yelp, false);
