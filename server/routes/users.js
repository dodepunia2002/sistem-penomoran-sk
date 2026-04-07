import { Router } from 'express';
import bcrypt from 'bcryptjs';
import db from '../database.js';
import { authenticateToken, requireRole } from '../middleware/auth.js';

const router = Router();

// All routes require admin
router.use(authenticateToken, requireRole('admin'));

// GET /api/users — list all users
router.get('/', (req, res) => {
  const users = db.prepare(
    'SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY created_at DESC'
  ).all();
  res.json({ users });
});

// POST /api/users — create new user
router.post('/', (req, res) => {
  const { username, password, nama_lengkap, role } = req.body;

  if (!username || !password || !nama_lengkap || !role) {
    return res.status(400).json({ error: 'Semua field wajib diisi' });
  }

  if (!['admin', 'petugas'].includes(role)) {
    return res.status(400).json({ error: 'Role harus admin atau petugas' });
  }

  if (password.length < 4) {
    return res.status(400).json({ error: 'Password minimal 4 karakter' });
  }

  // Check if username already exists
  const existing = db.prepare('SELECT id FROM users WHERE username = ?').get(username);
  if (existing) {
    return res.status(409).json({ error: 'Username sudah digunakan' });
  }

  const hashed = bcrypt.hashSync(password, 10);

  const result = db.prepare(
    'INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)'
  ).run(username, hashed, nama_lengkap, role);

  const newUser = db.prepare(
    'SELECT id, username, nama_lengkap, role, created_at FROM users WHERE id = ?'
  ).get(result.lastInsertRowid);

  res.status(201).json({ user: newUser, message: 'User berhasil ditambahkan' });
});

// PUT /api/users/:id — update user
router.put('/:id', (req, res) => {
  const { id } = req.params;
  const { username, password, nama_lengkap, role } = req.body;

  const user = db.prepare('SELECT * FROM users WHERE id = ?').get(id);
  if (!user) {
    return res.status(404).json({ error: 'User tidak ditemukan' });
  }

  // Don't allow deleting/modifying the last admin
  if (user.role === 'admin' && role !== 'admin') {
    const adminCount = db.prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin'").get();
    if (adminCount.count <= 1) {
      return res.status(400).json({ error: 'Tidak bisa mengubah role admin terakhir' });
    }
  }

  // Check for duplicate username
  if (username && username !== user.username) {
    const existing = db.prepare('SELECT id FROM users WHERE username = ? AND id != ?').get(username, id);
    if (existing) {
      return res.status(409).json({ error: 'Username sudah digunakan' });
    }
  }

  const updates = {
    username: username || user.username,
    nama_lengkap: nama_lengkap || user.nama_lengkap,
    role: role || user.role,
  };

  if (password && password.length >= 4) {
    updates.password = bcrypt.hashSync(password, 10);
    db.prepare(
      'UPDATE users SET username = ?, password = ?, nama_lengkap = ?, role = ? WHERE id = ?'
    ).run(updates.username, updates.password, updates.nama_lengkap, updates.role, id);
  } else {
    db.prepare(
      'UPDATE users SET username = ?, nama_lengkap = ?, role = ? WHERE id = ?'
    ).run(updates.username, updates.nama_lengkap, updates.role, id);
  }

  const updated = db.prepare(
    'SELECT id, username, nama_lengkap, role, created_at FROM users WHERE id = ?'
  ).get(id);

  res.json({ user: updated, message: 'User berhasil diperbarui' });
});

// DELETE /api/users/:id — delete user
router.delete('/:id', (req, res) => {
  const { id } = req.params;

  const user = db.prepare('SELECT * FROM users WHERE id = ?').get(id);
  if (!user) {
    return res.status(404).json({ error: 'User tidak ditemukan' });
  }

  // Cannot delete yourself
  if (parseInt(id) === req.user.id) {
    return res.status(400).json({ error: 'Tidak bisa menghapus akun sendiri' });
  }

  // Don't allow deleting the last admin
  if (user.role === 'admin') {
    const adminCount = db.prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin'").get();
    if (adminCount.count <= 1) {
      return res.status(400).json({ error: 'Tidak bisa menghapus admin terakhir' });
    }
  }

  db.prepare('DELETE FROM users WHERE id = ?').run(id);
  res.json({ message: 'User berhasil dihapus' });
});

export default router;
