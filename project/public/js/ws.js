var socket = new WebSocket('wss://ws.human-connection.social:8085');

socket.onopen = function(event) {
    console.log('WebSocket is connected.');
    var msg = {
        method : 'followChannel',
        channel : 'HC_channel',
        message : '',
        id : 0
    };
    socket.send(JSON.stringify(msg));
};
socket.onclose = function(event) {
    console.log("WebSocket is closed now.");
    $('.log').prepend('<div style="color: #f00"> '+ message +' </div>')
};

socket.onmessage = function(event) {
    var message = event.data;
    $('.log').prepend('<div class="resived"> '+ message +' </div>')
    console.log('revived:' + message);
};

$(document).ready(function(){
    $('#send').click(function(){
        var text = $('#message').val();
        let time = Date.now();
        $('.log').html('<div class="sended">  '+ text +' '+ time +'</div>');
        var msg = {
            method : 'messageToChannel',
            channel : 'HC_channel',
            message : text,
            id : 0
        };
        socket.send(JSON.stringify(msg));

    });
});
