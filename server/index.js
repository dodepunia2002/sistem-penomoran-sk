import express from 'express';
import cors from 'cors';
import path from 'path';
import { fileURLToPath } from 'url';

// Import database (initializes tables + seed data)
import './database.js';

// Import routes
import authRoutes from './routes/auth.js';
import userRoutes from './routes/users.js';
import pengajuanRoutes from './routes/pengajuan.js';
import riwayatRoutes from './routes/riwayat.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(cors());
app.use(express.json());

// Log requests in dev
app.use((req, res, next) => {
  const start = Date.now();
  res.on('finish', () => {
    const ms = Date.now() - start;
    if (req.url.startsWith('/api')) {
      console.log(`${req.method} ${req.url} ${res.statusCode} - ${ms}ms`);
    }
  });
  next();
});

// API Routes
app.use('/api/auth', authRoutes);
app.use('/api/users', userRoutes);
app.use('/api/pengajuan', pengajuanRoutes);
app.use('/api/riwayat', riwayatRoutes);

// Health check
app.get('/api/health', (req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

// Start server
app.listen(PORT, () => {
  console.log(`\n🚀 Backend API running at http://localhost:${PORT}`);
  console.log(`📦 Database: ${path.join(__dirname, 'data.db')}`);
  console.log(`\n🔑 Default accounts:`);
  console.log(`   Admin:   admin / admin123`);
  console.log(`   Petugas: petugas / petugas123\n`);
});
