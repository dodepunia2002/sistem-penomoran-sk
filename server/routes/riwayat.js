import { Router } from 'express';
import db from '../database.js';
import { authenticateToken, requireRole } from '../middleware/auth.js';

const router = Router();

router.use(authenticateToken);

// GET /api/riwayat — list all riwayat with optional search
router.get('/', (req, res) => {
  const { search, page = 1, limit = 50 } = req.query;
  const offset = (parseInt(page) - 1) * parseInt(limit);

  let query = `
    SELECT r.*, u.nama_lengkap as processed_by_name
    FROM riwayat r
    LEFT JOIN users u ON r.processed_by = u.id
  `;
  let countQuery = 'SELECT COUNT(*) as total FROM riwayat r';
  const params = [];
  const countParams = [];

  if (search) {
    const searchClause = ` WHERE r.nama LIKE ? OR r.alamat LIKE ? OR r.nomor_sk LIKE ?`;
    const searchParam = `%${search}%`;
    query += searchClause;
    countQuery += searchClause;
    params.push(searchParam, searchParam, searchParam);
    countParams.push(searchParam, searchParam, searchParam);
  }

  const total = db.prepare(countQuery).get(...countParams).total;

  query += ' ORDER BY r.created_at DESC LIMIT ? OFFSET ?';
  params.push(parseInt(limit), offset);

  const riwayat = db.prepare(query).all(...params);

  res.json({
    riwayat,
    pagination: {
      total,
      page: parseInt(page),
      limit: parseInt(limit),
      totalPages: Math.ceil(total / parseInt(limit)),
    },
  });
});

// PUT /api/riwayat/:id — update riwayat (admin only)
router.put('/:id', requireRole('admin'), (req, res) => {
  const { id } = req.params;
  const { nama, alamat } = req.body;

  const existing = db.prepare('SELECT * FROM riwayat WHERE id = ?').get(id);
  if (!existing) {
    return res.status(404).json({ error: 'Data riwayat tidak ditemukan' });
  }

  db.prepare('UPDATE riwayat SET nama = ?, alamat = ? WHERE id = ?').run(
    (nama || existing.nama).toUpperCase(),
    (alamat || existing.alamat).toUpperCase(),
    id
  );

  const updated = db.prepare(`
    SELECT r.*, u.nama_lengkap as processed_by_name
    FROM riwayat r
    LEFT JOIN users u ON r.processed_by = u.id
    WHERE r.id = ?
  `).get(id);

  res.json({ riwayat: updated, message: 'Data berhasil diperbarui' });
});

// DELETE /api/riwayat/:id — delete riwayat (admin only)
router.delete('/:id', requireRole('admin'), (req, res) => {
  const { id } = req.params;

  const existing = db.prepare('SELECT * FROM riwayat WHERE id = ?').get(id);
  if (!existing) {
    return res.status(404).json({ error: 'Data riwayat tidak ditemukan' });
  }

  db.prepare('DELETE FROM riwayat WHERE id = ?').run(id);
  res.json({ message: 'Data riwayat berhasil dihapus' });
});

export default router;
