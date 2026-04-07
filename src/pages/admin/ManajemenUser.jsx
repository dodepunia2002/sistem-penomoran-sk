import React, { useState, useEffect } from 'react';
import { UserPlus, Edit2, Trash2, X, Check, Shield, User } from 'lucide-react';
import { apiCall } from '../../context/AuthContext';
import { useToast } from '../../context/DataContext';

const ManajemenUser = () => {
  const { addToast } = useToast();
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState(null);
  const [deleteConfirm, setDeleteConfirm] = useState(null);
  const [processing, setProcessing] = useState(false);
  const [formData, setFormData] = useState({
    username: '', password: '', nama_lengkap: '', role: 'petugas'
  });

  const fetchUsers = async () => {
    try {
      const data = await apiCall('/api/users');
      setUsers(data.users);
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchUsers(); }, []);

  const resetForm = () => {
    setFormData({ username: '', password: '', nama_lengkap: '', role: 'petugas' });
    setShowForm(false);
    setEditingId(null);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setProcessing(true);
    try {
      if (editingId) {
        const payload = { ...formData };
        if (!payload.password) delete payload.password;
        await apiCall(`/api/users/${editingId}`, {
          method: 'PUT',
          body: JSON.stringify(payload),
        });
        addToast('User berhasil diperbarui', 'success');
      } else {
        await apiCall('/api/users', {
          method: 'POST',
          body: JSON.stringify(formData),
        });
        addToast('User baru berhasil ditambahkan', 'success');
      }
      resetForm();
      fetchUsers();
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setProcessing(false);
    }
  };

  const startEdit = (user) => {
    setEditingId(user.id);
    setFormData({
      username: user.username,
      password: '',
      nama_lengkap: user.nama_lengkap,
      role: user.role,
    });
    setShowForm(true);
  };

  const handleDelete = async () => {
    setProcessing(true);
    try {
      await apiCall(`/api/users/${deleteConfirm}`, { method: 'DELETE' });
      addToast('User berhasil dihapus', 'success');
      setDeleteConfirm(null);
      fetchUsers();
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setProcessing(false);
    }
  };

  return (
    <div>
      <h2 className="page-title">MANAJEMEN USER</h2>

      {/* Add/Edit Form */}
      <div className="card" style={{ marginBottom: '1.5rem' }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: showForm ? '1.5rem' : 0 }}>
          <h3 style={{ fontSize: '1.1rem' }}>{editingId ? 'Edit User' : 'Tambah User Baru'}</h3>
          {!showForm ? (
            <button className="btn btn-primary" onClick={() => setShowForm(true)} style={{ display: 'flex', gap: '0.5rem' }}>
              <UserPlus size={16} /> TAMBAH USER
            </button>
          ) : (
            <button className="btn" style={{ background: '#e5e7eb', color: '#4b5563' }} onClick={resetForm}>
              <X size={16} />
            </button>
          )}
        </div>

        {showForm && (
          <form onSubmit={handleSubmit} style={{ maxWidth: '600px' }}>
            <div className="form-row">
              <div className="form-group" style={{ flex: 1 }}>
                <label>Username</label>
                <input
                  type="text"
                  className="form-input"
                  placeholder="Username"
                  value={formData.username}
                  onChange={(e) => setFormData({ ...formData, username: e.target.value })}
                  required
                />
              </div>
              <div className="form-group" style={{ flex: 1 }}>
                <label>Password {editingId && <span style={{ color: '#9ca3af', fontSize: '0.75rem' }}>(kosongkan jika tidak diubah)</span>}</label>
                <input
                  type="password"
                  className="form-input"
                  placeholder={editingId ? "Biarkan kosong" : "Min. 4 karakter"}
                  value={formData.password}
                  onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                  {...(!editingId ? { required: true, minLength: 4 } : {})}
                />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group" style={{ flex: 1 }}>
                <label>Nama Lengkap</label>
                <input
                  type="text"
                  className="form-input"
                  placeholder="Nama lengkap"
                  value={formData.nama_lengkap}
                  onChange={(e) => setFormData({ ...formData, nama_lengkap: e.target.value })}
                  required
                />
              </div>
              <div className="form-group" style={{ flex: 1 }}>
                <label>Role</label>
                <select
                  className="form-input"
                  value={formData.role}
                  onChange={(e) => setFormData({ ...formData, role: e.target.value })}
                >
                  <option value="petugas">Petugas</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
            </div>
            <div style={{ display: 'flex', gap: '1rem', marginTop: '1rem' }}>
              <button type="button" className="btn" style={{ background: '#e5e7eb', color: '#4b5563', padding: '0.6rem 2rem' }} onClick={resetForm}>
                BATAL
              </button>
              <button type="submit" className="btn btn-primary" style={{ padding: '0.6rem 2rem' }} disabled={processing}>
                {processing ? 'MENYIMPAN...' : editingId ? 'UPDATE' : 'SIMPAN'}
              </button>
            </div>
          </form>
        )}
      </div>

      {/* User Table */}
      <div className="card">
        <h3 style={{ fontSize: '1.1rem', marginBottom: '1.5rem', textTransform: 'uppercase' }}>Daftar User</h3>

        {loading ? (
          <div className="empty-state"><div className="spinner" /></div>
        ) : (
          <div style={{ overflowX: 'auto' }}>
            <table className="custom-table">
              <thead>
                <tr>
                  <th>NO</th>
                  <th>USERNAME</th>
                  <th>NAMA LENGKAP</th>
                  <th>ROLE</th>
                  <th>TERDAFTAR</th>
                  <th style={{ textAlign: 'center' }}>AKSI</th>
                </tr>
              </thead>
              <tbody>
                {users.map((u, idx) => (
                  <tr key={u.id} className="table-row-animate" style={{ animationDelay: `${idx * 0.05}s` }}>
                    <td>{idx + 1}</td>
                    <td><strong>{u.username}</strong></td>
                    <td>{u.nama_lengkap}</td>
                    <td>
                      <span className={`role-badge ${u.role}`}>
                        {u.role === 'admin' ? <Shield size={12} /> : <User size={12} />}
                        {u.role.toUpperCase()}
                      </span>
                    </td>
                    <td>{new Date(u.created_at).toLocaleDateString('id-ID')}</td>
                    <td style={{ display: 'flex', gap: '0.75rem', justifyContent: 'center' }}>
                      <Edit2 size={16} color="#d39f28" style={{ cursor: 'pointer' }} onClick={() => startEdit(u)} />
                      <Trash2 size={16} color="#ef4444" style={{ cursor: 'pointer' }} onClick={() => setDeleteConfirm(u.id)} />
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        <div className="table-footer">Total {users.length} user terdaftar</div>
      </div>

      {/* Delete Confirmation */}
      {deleteConfirm && (
        <div className="modal-overlay" onClick={() => !processing && setDeleteConfirm(null)}>
          <div className="modal-content" onClick={e => e.stopPropagation()}>
            <div className="modal-icon">🗑️</div>
            <h3 className="modal-title">Hapus User?</h3>
            <p className="modal-text">User ini akan dihapus beserta semua data pengajuannya.</p>
            <div className="modal-actions">
              <button className="btn" style={{ background: '#e5e7eb', color: '#4b5563', padding: '0.6rem 2rem' }} onClick={() => setDeleteConfirm(null)} disabled={processing}>BATAL</button>
              <button className="btn btn-danger" style={{ padding: '0.6rem 2rem' }} onClick={handleDelete} disabled={processing}>{processing ? 'MENGHAPUS...' : 'YA, HAPUS'}</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default ManajemenUser;
