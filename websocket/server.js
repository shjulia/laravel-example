var path = require('path');
require('dotenv').config({path: path.join(__dirname, '../.env')});
var http = require('http').Server({ port: 8000 });
var io = require('socket.io')(http);
var Redis = require('ioredis');

var redis = new Redis({
    'host': process.env.REDIS_HOST,
    'port': process.env.REDIS_PORT
});
redis.psubscribe('job-chanel.*');
redis.psubscribe('job-chanel.provider-notification.*');
redis.psubscribe('job-chanel.provider.*');
redis.psubscribe('job-chanel.provider-location.*');
redis.psubscribe('job-chanel.provider.decline.*');
redis.on('pmessage', function(pattern, chanel, message) {
    /*console.log('pattern', pattern);
    console.log('chanel', chanel);
    console.log('message', message);*/
    message = JSON.parse(message);
    io.emit(chanel + ':' + message.event, message.data);
});
http.listen(8000, function() {
    console.log('listen 8000');
});
