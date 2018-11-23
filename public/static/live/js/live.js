var wsServer = 'ws://ltfnevergiveup.cn:8811';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    push(evt.data);
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onclose = function (evt) {
    console.log('closed');
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};

function push(data) {
    data = JSON.parse(data);
    html = '';
    html += '<div class="frame">\n' +
        '                <h3 class="frame-header">\n' +
        '                    <i class="icon iconfont icon-shijian"></i>第'+data.type+'节 2：30\n' +
        '                </h3>\n' +
        '                <div class="frame-item">\n' +
        '                    <span class="frame-dot"></span>\n' +
        '                    <div class="frame-item-author">\n' +
        '                        <img src="./imgs/team1.png" width="20px" height="20px"/> 马刺\n' +
        '                    </div>\n' +
        '                    <p>'+data.content+'</p>\n' +
        '                </div>\n' +
        '            </div>';

    $('#match-result').prepend(html);
}