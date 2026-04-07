import React, { useState, useEffect } from 'react';
import { Search, Edit2, Trash2, X, Check } from 'lucide-react';
import { apiCall } from '../../context/AuthContext';
import { useToast } from '../../context/DataContext';

const RiwayatPetugas = () => {
  const { addToast } = useToast();
  const [pengajuan, setPengajuan] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [editingId, setEditingId] = useState(null);
  const [editForm, setEditForm] = useState({});
  const [deleteConfirm, setDeleteConfirm] = useState(null);
  const [processing, setProcessing] = useState(false);

  const fetchData = async () => {
    try {
      const data = await apiCall('/api/pengajuan');
      setPengajuan(data.pengajuan);
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const filtered = pengajuan.filter(p => {
    if (!searchTerm) return true;
    const term = searchTerm.toLowerCase();
    return p.nama.toLowerCase().includes(term) ||
           p.alamat.toLowerCase().includes(term) ||
           p.status.toLowerCase().includes(term);
  });

  const startEdit = (row) => {
    setEditingId(row.id);
    setEditForm({ nama: row.nama, alamat: row.alamat });
  };

  const saveEdit = async (id) => {
    setProcessing(true);
    try {
      await apiCall(`/api/pengajuan/${id}`, {
        method: 'PUT',
        body: JSON.stringify(editForm),
      });
      addToast('Data berhasil diperbarui.', 'success');
      setEditingId(null);
      fetchData();
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setProcessing(false);
    }
  };

  const confirmDelete = async () => {
    setProcessing(true);
    try {
      await apiCall(`/api/pengajuan/${deleteConfirm}`, { method: 'DELETE' });
      addToast('Pengajuan berhasil dihapus.', 'success');
      setDeleteConfirm(null);
      fetchData();
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setProcessing(false);
    }
  };

  const getStatusBadge = (status) => {
    const map = {
      pending: { label: 'PENDING', cls: 'status-pending' },
      diterima: { label: 'DITERIMA', cls: 'status-accepted' },
      ditolak: { label: 'DITOLAK', cls: 'status-rejected' },
    };
    const s = map[status] || map.pending;
    return <span className={`status-badge ${s.cls}`}>{s.label}</span>;
  };

  return (
    <div>
      <h2 className="page-title">RIWAYAT PENGAJUAN SK</h2>
      
      <div className="card">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.5rem', flexWrap: 'wrap', gap: '1rem' }}>
          <h3 style={{ fontSize: '1.1rem', textTransform: 'uppercase' }}>Data Pengajuan Saya</h3>
          <div style={{ position: 'relative', width: '250px' }}>
            <input 
              type="text" 
              placeholder="Cari pengajuan..." 
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              style={{
                width: '100%', padding: '0.5rem 1rem 0.5rem 2.5rem',
                borderRadius: '999px', border: '1px solid #e5e7eb',
                background: '#f9fafb', outline: 'none'
              }}
            />
            <Search size={16} color="#9ca3af" style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)' }} />
          </div>
        </div>

        {loading ? (
          <div className="empty-state"><div className="spinner" /></div>
        ) : filtered.length === 0 ? (
          <div className="empty-state">
            <div className="empty-state-icon">📂</div>
            <p className="empty-state-text">{searchTerm ? 'Tidak ada data yang cocok.' : 'Belum ada pengajuan.'}</p>
            <p className="empty-state-sub">{searchTerm ? 'Coba kata kunci lain.' : 'Silakan input data pengajuan terlebih dahulu.'}</p>
          </div>
        ) : (
          <div style={{ overflowX: 'auto' }}>
            <table className="custom-table">
              <thead>
                <tr>
                  <th>NO</th>
                  <th>NAMA</th>
                  <th>ALAMAT</th>
                  <th>TANGGAL</th>
                  <th>STATUS</th>
                  <th style={{ textAlign: 'center' }}>AKSI</th>
                </tr>
              </thead>
              <tbody>
                {filtered.map((row, idx) => (
                  <tr key={row.id} className="table-row-animate" style={{ animationDelay: `${idx * 0.05}s` }}>
                    <td>{idx + 1}</td>
                    <td>
                      {editingId === row.id ? (
                        <input className="inline-edit" value={editForm.nama} onChange={(e) => setEditForm({ ...editForm, nama: e.target.value })} />
                      ) : row.nama}
                    </td>
                    <td>
                      {editingId === row.id ? (
                        <input className="inline-edit" value={editForm.alamat} onChange={(e) => setEditForm({ ...editForm, alamat: e.target.value })} />
                      ) : row.alamat}
                    </td>
                    <td>{row.tanggal}</td>
                    <td>{getStatusBadge(row.status)}</td>
                    <td style={{ display: 'flex', gap: '0.75rem', justifyContent: 'center' }}>
                      {editingId === row.id ? (
                        <>
                          <Check size={18} color="#22c55e" style={{ cursor: 'pointer' }} onClick={() => saveEdit(row.id)} />
                          <X size={18} color="#9ca3af" style={{ cursor: 'pointer' }} onClick={() => { setEditingId(null); setEditForm({}); }} />
                        </>
                      ) : row.status === 'pending' ? (
                        <>
                          <Edit2 size={16} color="#d39f28" style={{ cursor: 'pointer' }} onClick={() => startEdit(row)} />
                          <Trash2 size={16} color="#ef4444" style={{ cursor: 'pointer' }} onClick={() => setDeleteConfirm(row.id)} />
                        </>
                      ) : (
                        <span style={{ color: '#9ca3af', fontSize: '0.8rem' }}>—</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        <div className="table-footer">Total {filtered.length} pengajuan</div>
      </div>

      {deleteConfirm && (
        <div className="modal-overlay" onClick={() => !processing && setDeleteConfirm(null)}>
          <div className="modal-content" onClick={e => e.stopPropagation()}>
            <div className="modal-icon">🗑️</div>
            <h3 className="modal-title">Hapus Pengajuan?</h3>
            <p className="modal-text">Pengajuan ini akan dihapus secara permanen.</p>
            <div className="modal-actions">
              <button className="btn" style={{ background: '#e5e7eb', color: '#4b5563', padding: '0.6rem 2rem' }} onClick={() => setDeleteConfirm(null)} disabled={processing}>BATAL</button>
              <button className="btn btn-danger" style={{ padding: '0.6rem 2rem' }} onClick={confirmDelete} disabled={processing}>{processing ? 'MENGHAPUS...' : 'YA, HAPUS'}</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default RiwayatPetugas;
