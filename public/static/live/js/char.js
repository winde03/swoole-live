var wsServer = 'ws://ltfnevergiveup.cn:8812';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    push_c(evt.data);
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onclose = function (evt) {
    console.log('closed');
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};

function push_c(data) {
    data = JSON.parse(data);
    html = '';
    html += ' <div class="comment">\n' +
        '                <span>' + data.user + '</span>\n' +
        '                <span>' + data.content + '</span>\n' +
        '            </div>';

    $('#comments').append(html);
}
