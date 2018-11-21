var wsServer = 'ws://ltfnevergiveup.cn:8811';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    websocket.send("hello!");
    console.log("Connected to WebSocket server.");
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onclose = function (evt) {
    console.log('closed');
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};