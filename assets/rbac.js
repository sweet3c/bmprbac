/**
 * RBAC公共JS
 *
 * */
yii.bmprbac = (function ($) {

    //授权角色管理-分配任务
    var intiAssignTask = function () {
        $("#task_categorys").change(function () {
            $("#assign-task-frm").submit();
        });

        //添加权限
        $("#assignItems").click(function () {
            if ($("#unassign").val() == null) return false;
            var th = $(this);
            if (th.hasClass('disabled'))
                return false;
            else
                th.addClass('disabled');
            var items = $("#unassign").val();

            $.ajax({
                url: th.attr("ref"),
                type: 'POST',
                data: {authItems: items}
            })
                .done(function (msg) {
                    alert(msg);
                    th.removeClass('disabled');
                    location.reload();
                })
                .fail(function (xhr) {
                    alert(xhr.statusText)
                });
        });

        //移除权限
        $("#deleteAssignItems").click(function () {
            if ($("#assigned").val() == null) return false;
            var th = $(this);
            if (th.hasClass('disabled'))
                return false;
            else
                th.addClass('disabled');
            var items = $("#assigned").val();
            $.ajax({
                url: th.attr("ref"),
                type: 'POST',
                data: {authItems: items}
            })
                .done(function (msg) {
                    alert(msg);
                    th.removeClass('disabled');
                    location.reload();
                })
                .fail(function (xhr) {
                    alert(xhr.statusText)
                });
        });

    };
    //授权项目管理 更新
    var initAuthItems = function () {
        $("input[type='radio']").click(function () {
            var self = $(this)
            if (self.val() == 'custom') {
                $('#rbacauthitems-name').attr('readonly', false);
            } else if (self.val() == 'operation' || self.val() == 'data') {
                $('#rbacauthitems-name').attr('readonly', true);
            }

        });
    };

    //全选
    var initSelectAll = function () {
        $("#select_all").click(function () {
            var checked = this.checked;
            $("input[name='actions\[\]']:enabled").each(function () {
                this.checked = checked;
            });
        });
    };

    //角色已关联用户选中
    var initRelatedUserSelectAll = function () {
        if (undefined !== $("#roleRelatedUserIds").val()) {
            var user_ids = $("#roleRelatedUserIds").val();
            var user_ids_arr = user_ids.split(',');
            $(":checkbox[name='selection[]']").each(function () {
                if ($.inArray($(this).val(), user_ids_arr) != -1) $(this).attr('checked', true);
            });
        }
    };

    return {
        init: function () {
            initAuthItems();
            initSelectAll();
            intiAssignTask();
            initRelatedUserSelectAll();
        }
    }

})(jQuery);