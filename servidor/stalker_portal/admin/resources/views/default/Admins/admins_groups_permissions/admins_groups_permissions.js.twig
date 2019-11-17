
        function yelp() {
            $(document).ready(function () {
                $('[data-toggle="tooltip"]').tooltip();
                $('.collapse-link').each( function (e) {
                    var box = $(this).closest('div.box');
                    var button = $(this).find('i');
                    var content = box.find('div.box-content');
                    content.slideToggle('fast');
                    button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
                    setTimeout(function () {
                            box.resize();
                            box.find('[id^=map-]').resize();
                    }, 50);
		        });
                
                $(".box-header :checkbox").each(function(){
                    checkBlock(this);
                });
                
                $(document).on('change', '.box-header :checkbox', function(e){
                    var permission_poin = $(this).attr('name').match(/\[([^\]]*)\]/ig)[1].replace(/[\[\]\'\"]/ig, '');
                    $(this).closest('.box').children('.box-content').find(":checkbox[name*='" + permission_poin + "']").filter(':not(:disabled)').prop('checked', $(this).prop('checked'));
                });
                
                $(document).on('change', '.box-content :checkbox', function(e){
                    var permission_poin = $(this).attr('name').match(/\[([^\]]*)\]/ig);
                    permission_poin = permission_poin[permission_poin.length - 1].replace(/[\[\]\'\"]/ig, '');
                    var topChkBx = $(this).closest('.box').children('.box-header').find(":checkbox[name*='" + permission_poin + "']").get(0);
                    checkBlock(topChkBx);
                });
                
                $(document).on('click', "#save_button", function(e){
                    e.stopPropagation();
                    e.preventDefault();
                    var sendData = {'adminGropID': {{ app['adminGropID'] }}};
                    $("#adm_grp_perm > div").each(function(){
                        $(this).find("input").each(function(){
                            var name = $(this).attr('name').replace('controller', '').match(/[\w,-]+/ig);
                            var controller = name[0];
                            var action = (name.length < 3)? 'index': name[1];
                            var permission_poin = name[name.length - 1];
                            /*var val = $(this).val();*/
                            
                            if (typeof(sendData[controller]) == 'undefined' ) {
                               sendData[controller] = {}; 
                            }
                            
                            if (typeof(sendData[controller][action]) == 'undefined') {
                                sendData[controller][action] = {};
                            }
                            if ($(this).attr('type') == 'checkbox') {
                                sendData[controller][action][permission_poin] = ($(this).prop('checked') ? 1: 0);
                            } else {
                                sendData[controller][action][permission_poin] = ($(this).val('on') ? 1: 0);
                            }
                        });
                    });
                    ajaxPostSend('{{app.request_context.baseUrl}}/{{app.controller_alias}}/save-admins-group-permissions', {data: JSON.stringify(sendData)}, false, true);
                })
            });
        }
        
        document.addEventListener("DOMContentLoaded", yelp, false);
        
        function checkBlock(obj){
            var permission_poin = $(obj).attr('name').match(/\[([^\]]*)\]/ig)[1].replace(/[\[\]\'\"]/ig, '');
            var checkVal = 0;
            var colCheckBox = $(obj).closest('.box').children('.box-content').find(":checkbox[name*='" + permission_poin + "']");
            if (colCheckBox.find(':not(:disabled)').length > 0) {
                colCheckBox.each(function(){
                    checkVal += $(this).prop('checked') ? 1: 0;
                });
                $(obj).prop('checked', checkVal != 0);
            }
        }
        
        var managePermissions = function(data){
            JSSuccessModalBox(data);
        };

        var managePermissionsError = function(data){
            JSErrorModalBox(data);
        }
