require('dotenv').config();

const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const { Pool } = require('pg');

const PORT = Number(process.env.PORT || 3001);
const CLIENT_ORIGIN = process.env.CLIENT_ORIGIN || '*';

const pool = new Pool(process.env.DATABASE_URL ? {
  connectionString: process.env.DATABASE_URL,
} : {
  host: process.env.DB_HOST || '127.0.0.1',
  port: Number(process.env.DB_PORT || 5432),
  database: process.env.DB_DATABASE || 'AristoMarket',
  user: process.env.DB_USERNAME || 'market_user',
  password: process.env.DB_PASSWORD || 'market123',
});

const app = express();
app.use(express.json());

app.get('/health', async (_req, res) => {
  try {
    await pool.query('SELECT 1');
    res.json({ status: 'ok', database: 'connected' });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
});

const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: CLIENT_ORIGIN,
    methods: ['GET', 'POST'],
  },
});

function roomForUser(userId) {
  return `user:${userId}`;
}

function roomForConversation(conversationId) {
  return `conversation:${conversationId}`;
}

async function resolveConversationId(client, senderId, receiverId, productId, requestedConversationId) {
  if (requestedConversationId) {
    const { rows } = await client.query(
      `SELECT conversation_id
       FROM messages
       WHERE conversation_id = $1
         AND (sender_id = $2 OR receiver_id = $2)
       LIMIT 1`,
      [requestedConversationId, senderId]
    );

    if (rows.length) {
      return Number(rows[0].conversation_id);
    }
  }

  const { rows } = await client.query(
    `SELECT conversation_id
     FROM messages
     WHERE (
       (sender_id = $1 AND receiver_id = $2)
       OR (sender_id = $2 AND receiver_id = $1)
     )
     AND (
       ($3::bigint IS NULL AND product_id IS NULL)
       OR product_id = $3::bigint
     )
     ORDER BY conversation_id
     LIMIT 1`,
    [senderId, receiverId, productId]
  );

  if (rows.length) {
    return Number(rows[0].conversation_id);
  }

  const maxResult = await client.query('SELECT COALESCE(MAX(conversation_id), 0) + 1 AS next_id FROM messages');
  return Number(maxResult.rows[0].next_id);
}

async function saveMessage(payload) {
  const senderId = Number(payload.sender_id);
  const receiverId = Number(payload.receiver_id);
  const productId = payload.product_id ? Number(payload.product_id) : null;
  const body = String(payload.body || '').trim();
  const requestedConversationId = payload.conversation_id ? Number(payload.conversation_id) : null;

  if (!senderId || !receiverId || !body) {
    throw new Error('sender_id, receiver_id y body son obligatorios.');
  }

  const client = await pool.connect();
  try {
    await client.query('BEGIN');
    const conversationId = await resolveConversationId(client, senderId, receiverId, productId, requestedConversationId);
    const { rows } = await client.query(
      `INSERT INTO messages (conversation_id, sender_id, receiver_id, product_id, body, created_at)
       VALUES ($1, $2, $3, $4, $5, CURRENT_TIMESTAMP)
       RETURNING id, conversation_id, sender_id, receiver_id, product_id, body, read_at, created_at`,
      [conversationId, senderId, receiverId, productId, body]
    );
    await client.query('COMMIT');
    return rows[0];
  } catch (error) {
    await client.query('ROLLBACK');
    throw error;
  } finally {
    client.release();
  }
}

io.on('connection', (socket) => {
  socket.on('registerUser', ({ user_id }) => {
    const userId = Number(user_id);
    if (userId) {
      socket.join(roomForUser(userId));
    }
  });

  socket.on('joinConversation', ({ conversation_id }) => {
    const conversationId = Number(conversation_id);
    if (conversationId) {
      socket.join(roomForConversation(conversationId));
    }
  });

  socket.on('sendMessage', async (payload) => {
    try {
      const message = await saveMessage(payload);
      io.to(roomForConversation(message.conversation_id)).emit('messageSaved', message);
      io.to(roomForUser(message.sender_id)).emit('messageSaved', message);
      io.to(roomForUser(message.receiver_id)).emit('messageSaved', message);
    } catch (error) {
      socket.emit('chatError', { message: error.message });
    }
  });
});

server.listen(PORT, () => {
  console.log(`Servidor de chat escuchando en http://localhost:${PORT}`);
});
