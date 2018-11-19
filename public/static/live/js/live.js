var wsUrl = "ws://ltfnevergiveup.cn:8811";

var webSocket = new WebSocket(wsUrl);

webSocket.onopen = function (evt) {
    console.log("content-swoole-success");
}

webSocket.onmessage = function (evt) {
    console.log('ws-server-return-data'+evt.data);
}

webSocket.onclose = function (evt) {
    console.log('close');
}

webSocket.onerror = function (evt, e) {
    console.log('error:'+evt.data);
}