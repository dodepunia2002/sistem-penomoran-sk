import { Router } from 'express';
import db from '../database.js';
import { authenticateToken, requireRole } from '../middleware/auth.js';

const router = Router();

// All routes require authentication
router.use(authenticateToken);

// Generate SK number
function generateSKNumber() {
  const now = new Date();
  const seq = Math.floor(Math.random() * 9000) + 1000;
  return `SN/SK/${String(now.getMonth() + 1).padStart(2, '0')}/${seq}/${String(now.getFullYear()).slice(2)}`;
}

// Format date from YYYY-MM-DD to DD-MM-YYYY
function formatDate(dateStr) {
  if (!dateStr) return '';
  if (dateStr.includes('-') && dateStr.split('-')[0].length === 4) {
    const [y, m, d] = dateStr.split('-');
    return `${d}-${m}-${y}`;
  }
  return dateStr;
}

// GET /api/pengajuan — list pengajuan
router.get('/', (req, res) => {
  let pengajuan;
  if (req.user.role === 'admin') {
    pengajuan = db.prepare(`
      SELECT p.*, u.nama_lengkap as submitted_by_name
      FROM pengajuan p
      LEFT JOIN users u ON p.submitted_by = u.id
      WHERE p.status = 'pending'
      ORDER BY p.created_at DESC
    `).all();
  } else {
    pengajuan = db.prepare(`
      SELECT p.*, u.nama_lengkap as submitted_by_name
      FROM pengajuan p
      LEFT JOIN users u ON p.submitted_by = u.id
      WHERE p.submitted_by = ?
      ORDER BY p.created_at DESC
    `).all(req.user.id);
  }
  res.json({ pengajuan });
});

// POST /api/pengajuan — create new pengajuan
router.post('/', (req, res) => {
  const { nama, alamat, tanggal } = req.body;

  if (!nama || !alamat || !tanggal) {
    return res.status(400).json({ error: 'Nama, alamat, dan tanggal wajib diisi' });
  }

  const result = db.prepare(
    'INSERT INTO pengajuan (nama, alamat, tanggal, status, submitted_by) VALUES (?, ?, ?, ?, ?)'
  ).run(nama.toUpperCase(), alamat.toUpperCase(), formatDate(tanggal), 'pending', req.user.id);

  const newPengajuan = db.prepare('SELECT * FROM pengajuan WHERE id = ?').get(result.lastInsertRowid);
  res.status(201).json({ pengajuan: newPengajuan, message: 'Pengajuan berhasil dikirim' });
});

// PUT /api/pengajuan/:id — update pengajuan (petugas only, status pending)
router.put('/:id', (req, res) => {
  const { id } = req.params;
  const { nama, alamat, tanggal } = req.body;

  const pengajuan = db.prepare('SELECT * FROM pengajuan WHERE id = ?').get(id);
  if (!pengajuan) {
    return res.status(404).json({ error: 'Pengajuan tidak ditemukan' });
  }

  // Only petugas who submitted can edit, or admin
  if (req.user.role !== 'admin' && pengajuan.submitted_by !== req.user.id) {
    return res.status(403).json({ error: 'Tidak memiliki akses' });
  }

  if (pengajuan.status !== 'pending') {
    return res.status(400).json({ error: 'Hanya pengajuan dengan status pending yang bisa diedit' });
  }

  db.prepare(
    'UPDATE pengajuan SET nama = ?, alamat = ?, tanggal = ? WHERE id = ?'
  ).run(
    (nama || pengajuan.nama).toUpperCase(),
    (alamat || pengajuan.alamat).toUpperCase(),
    tanggal ? formatDate(tanggal) : pengajuan.tanggal,
    id
  );

  const updated = db.prepare('SELECT * FROM pengajuan WHERE id = ?').get(id);
  res.json({ pengajuan: updated, message: 'Pengajuan berhasil diperbarui' });
});

// DELETE /api/pengajuan/:id — delete pengajuan
router.delete('/:id', (req, res) => {
  const { id } = req.params;

  const pengajuan = db.prepare('SELECT * FROM pengajuan WHERE id = ?').get(id);
  if (!pengajuan) {
    return res.status(404).json({ error: 'Pengajuan tidak ditemukan' });
  }

  if (req.user.role !== 'admin' && pengajuan.submitted_by !== req.user.id) {
    return res.status(403).json({ error: 'Tidak memiliki akses' });
  }

  if (pengajuan.status !== 'pending' && req.user.role !== 'admin') {
    return res.status(400).json({ error: 'Hanya pengajuan pending yang bisa dihapus' });
  }

  db.prepare('DELETE FROM pengajuan WHERE id = ?').run(id);
  res.json({ message: 'Pengajuan berhasil dihapus' });
});

// POST /api/pengajuan/:id/terima — accept (admin only)
router.post('/:id/terima', requireRole('admin'), (req, res) => {
  const { id } = req.params;

  const pengajuan = db.prepare('SELECT * FROM pengajuan WHERE id = ?').get(id);
  if (!pengajuan) {
    return res.status(404).json({ error: 'Pengajuan tidak ditemukan' });
  }

  if (pengajuan.status !== 'pending') {
    return res.status(400).json({ error: 'Pengajuan sudah diproses' });
  }

  const nomorSK = generateSKNumber();

  // Update pengajuan status
  db.prepare('UPDATE pengajuan SET status = ? WHERE id = ?').run('diterima', id);

  // Create riwayat entry
  const result = db.prepare(
    'INSERT INTO riwayat (pengajuan_id, nama, alamat, tanggal, nomor_sk, processed_by) VALUES (?, ?, ?, ?, ?, ?)'
  ).run(id, pengajuan.nama, pengajuan.alamat, pengajuan.tanggal, nomorSK, req.user.id);

  const riwayat = db.prepare('SELECT * FROM riwayat WHERE id = ?').get(result.lastInsertRowid);

  res.json({ riwayat, nomorSK, message: `Pengajuan diterima dengan nomor SK: ${nomorSK}` });
});

// POST /api/pengajuan/:id/tolak — reject (admin only)
router.post('/:id/tolak', requireRole('admin'), (req, res) => {
  const { id } = req.params;

  const pengajuan = db.prepare('SELECT * FROM pengajuan WHERE id = ?').get(id);
  if (!pengajuan) {
    return res.status(404).json({ error: 'Pengajuan tidak ditemukan' });
  }

  if (pengajuan.status !== 'pending') {
    return res.status(400).json({ error: 'Pengajuan sudah diproses' });
  }

  db.prepare('UPDATE pengajuan SET status = ? WHERE id = ?').run('ditolak', id);
  res.json({ message: 'Pengajuan berhasil ditolak' });
});

export default router;
