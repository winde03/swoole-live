$(function () {
    $('#discuss-box').keydown(function (event) {
        if (event.keyCode == 13) {
            //    回车
            var text = $(this).val();
            var url = "http://ltfnevergiveup.cn:8811/?s=index/chart/index";
            var data = {'content': text, 'game_id': 1};

            $.post(url, data, function (result) {
                $(this).val("");

            }, 'json');
        }
    })
})