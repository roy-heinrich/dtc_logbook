import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import { createClient } from 'redis';
import cors from 'cors';
import dotenv from 'dotenv';
import crypto from 'crypto';

dotenv.config({ override: true });

const app = express();
const server = http.createServer(app);

const redisHost = process.env.REDIS_HOST || '127.0.0.1';
const redisPort = Number(process.env.REDIS_PORT || 6379);
const redisPassword = process.env.REDIS_PASSWORD || undefined;
const redisUsername = process.env.REDIS_USERNAME || undefined;
const redisFamily = Number(process.env.REDIS_FAMILY || 4);
const hasExplicitHostConfig = !!process.env.REDIS_HOST || !!process.env.REDIS_PORT;
const redisUrl = hasExplicitHostConfig ? undefined : (process.env.REDIS_URL || undefined);
const redisTls = String(process.env.REDIS_TLS || '').toLowerCase() === 'true'
    || (redisUrl ? redisUrl.startsWith('rediss://') : false);

const buildRedisOptions = () => {
    if (redisUrl) {
        return {
            url: redisUrl,
            socket: {
                family: redisFamily,
                connectTimeout: 10000,
                ...(redisTls
                    ? {
                        tls: true,
                        servername: redisHost,
                    }
                    : {}),
            },
        };
    }

    return {
        socket: {
            host: redisHost,
            port: redisPort,
            family: redisFamily,
            connectTimeout: 10000,
            ...(redisTls ? { tls: true, servername: redisHost } : {}),
        },
        username: redisUsername,
        password: redisPassword,
    };
};

const allowedOrigins = (process.env.WEBSOCKET_ALLOWED_ORIGINS || process.env.APP_URL || 'http://localhost:8000')
    .split(',')
    .map((origin) => origin.trim())
    .filter(Boolean);

const allowedChannels = new Set(['dashboard']);

const decodeAppKey = () => {
    const appKey = process.env.APP_KEY || '';

    if (!appKey) {
        return '';
    }

    if (appKey.startsWith('base64:')) {
        return Buffer.from(appKey.slice(7), 'base64');
    }

    return Buffer.from(appKey, 'utf8');
};

const appKey = decodeAppKey();

const base64UrlDecode = (value) => {
    const normalized = value.replace(/-/g, '+').replace(/_/g, '/');
    const padding = '='.repeat((4 - (normalized.length % 4)) % 4);
    return Buffer.from(normalized + padding, 'base64');
};

const verifyRealtimeToken = (token, expectedChannel) => {
    if (!token || typeof token !== 'string' || !appKey) {
        return false;
    }

    const parts = token.split('.');
    if (parts.length !== 2) {
        return false;
    }

    const [payloadEncoded, signatureEncoded] = parts;

    const expectedSignature = crypto
        .createHmac('sha256', appKey)
        .update(payloadEncoded)
        .digest();

    const providedSignature = base64UrlDecode(signatureEncoded);
    if (providedSignature.length !== expectedSignature.length) {
        return false;
    }

    if (!crypto.timingSafeEqual(providedSignature, expectedSignature)) {
        return false;
    }

    const payload = JSON.parse(base64UrlDecode(payloadEncoded).toString('utf8'));
    const now = Math.floor(Date.now() / 1000);

    return payload?.ch === expectedChannel && Number(payload?.exp || 0) >= now && Number(payload?.sub || 0) > 0;
};

const io = new Server(server, {
    cors: {
        origin: allowedOrigins,
        methods: ['GET', 'POST'],
        credentials: true
    }
});

// Redis setup for pub/sub
const redisClient = createClient(buildRedisOptions());

const redisSubscriber = createClient(buildRedisOptions());

redisClient.on('error', (err) => console.log('Redis Client Error', err));
redisSubscriber.on('error', (err) => console.log('Redis Subscriber Error', err));

try {
    await redisClient.connect();
    await redisSubscriber.connect();
} catch (error) {
    console.error('Unable to connect to Redis. Check REDIS_HOST/REDIS_PORT/REDIS_PASSWORD and REDIS_TLS settings.');
    console.error(error);
    process.exit(1);
}

const subscribedChannels = new Set();

app.use(cors({ origin: allowedOrigins, credentials: true }));
app.use(express.json());

// Health check endpoint
app.get('/ping', (req, res) => {
    res.json({ message: 'pong' });
});

// Socket.io connection handling
io.on('connection', (socket) => {
    socket.on('subscribe', ({ channel, token }) => {
        if (!allowedChannels.has(channel)) {
            socket.emit('subscribe_error', { message: 'Invalid channel.' });
            return;
        }

        if (!verifyRealtimeToken(token, channel)) {
            socket.emit('subscribe_error', { message: 'Unauthorized subscription.' });
            return;
        }

        socket.join(channel);

        if (subscribedChannels.has(channel)) {
            return;
        }

        subscribedChannels.add(channel);
        redisSubscriber.subscribe(channel, (message) => {
            try {
                const payload = JSON.parse(message);
                io.to(channel).emit(payload.event, payload.data);
            } catch (error) {
                console.error(`Failed to parse broadcast payload for ${channel}:`, error);
            }
        });
    });
});

const PORT = process.env.WEBSOCKET_PORT || 6001;
server.listen(PORT, () => {
    console.log(`WebSocket server running on port ${PORT}`);
});
