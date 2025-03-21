import { Server } from 'socket.io';
import Redis from "ioredis";
import {createServer} from "http";

const server = createServer();

const io = new Server(server, {
    cors : {
        origin: "*",
    }
});

const redis = new Redis();

redis.subscribe('notifications', (err, count) => {
    if (err)
    {
        console.error('failed to subscribe : %s', err.message);
    } else {
        console.log(`Subscribed successfully! This client is currently subscribed to ${count} channels.`);
    }
});

redis.on('message', (channel, message) => {
    const event = JSON.parse(message);
    console.log(`Message received from channel ${event.event}: ${channel}`);
    io.emit(event.event, channel, event.data);
});

io.on('connection', (socket) => {
    console.log('a user connected');

    socket.on('disconnect', () => {
        console.log('user disconnected');
    });
});

server.listen(6001, () => {
    console.log('listening on *:6001');
});

