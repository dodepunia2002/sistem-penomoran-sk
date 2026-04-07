import React, { useState, useEffect } from 'react';
import { ChevronDown } from 'lucide-react';
import { apiCall } from '../../context/AuthContext';
import { useToast } from '../../context/DataContext';

const PemberianNomor = () => {
  const { addToast } = useToast();
  const [pengajuan, setPengajuan] = useState([]);
  const [loading, setLoading] = useState(true);
  const [confirmAction, setConfirmAction] = useState(null);
  const [processing, setProcessing] = useState(false);

  const fetchData = async () => {
    try {
      const data = await apiCall('/api/pengajuan');
      setPengajuan(data.pengajuan.filter(p => p.status === 'pending'));
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const handleTerima = (item) => {
    setConfirmAction({ item, type: 'terima' });
  };

  const handleTolak = (item) => {
    setConfirmAction({ item, type: 'tolak' });
  };

  const executeAction = async () => {
    if (!confirmAction) return;
    setProcessing(true);
    try {
      if (confirmAction.type === 'terima') {
        const data = await apiCall(`/api/pengajuan/${confirmAction.item.id}/terima`, { method: 'POST' });
        addToast(data.message, 'success');
      } else {
        const data = await apiCall(`/api/pengajuan/${confirmAction.item.id}/tolak`, { method: 'POST' });
        addToast(data.message, 'danger');
      }
      setConfirmAction(null);
      fetchData();
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setProcessing(false);
    }
  };

  return (
    <div>
      <h2 className="page-title">SISTEM PENOMORAN SK</h2>
      
      <div className="card">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.5rem' }}>
          <h3 style={{ fontSize: '1.25rem' }}>Daftar Penomoran</h3>
          <div className="badge-count">{pengajuan.length} pengajuan</div>
        </div>

        {loading ? (
          <div className="empty-state"><div className="spinner" /></div>
        ) : pengajuan.length === 0 ? (
          <div className="empty-state">
            <div className="empty-state-icon">📋</div>
            <p className="empty-state-text">Belum ada pengajuan baru.</p>
            <p className="empty-state-sub">Data pengajuan dari Petugas akan muncul di sini.</p>
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
                  <th>PETUGAS</th>
                  <th style={{ textAlign: 'center' }}>KONFIRMASI</th>
                </tr>
              </thead>
              <tbody>
                {pengajuan.map((row, idx) => (
                  <tr key={row.id} className="table-row-animate" style={{ animationDelay: `${idx * 0.05}s` }}>
                    <td>{idx + 1}</td>
                    <td>{row.nama}</td>
                    <td>{row.alamat}</td>
                    <td>{row.tanggal}</td>
                    <td><span className="badge-info">{row.submitted_by_name || '-'}</span></td>
                    <td style={{ display: 'flex', gap: '0.5rem', justifyContent: 'center' }}>
                      <button 
                        className="btn btn-danger" 
                        style={{ padding: '0.4rem 1rem', fontSize: '0.75rem', letterSpacing: '1px' }}
                        onClick={() => handleTolak(row)}
                      >TOLAK</button>
                      <button 
                        className="btn btn-success" 
                        style={{ padding: '0.4rem 1rem', fontSize: '0.75rem', letterSpacing: '1px', background: '#455cf5' }}
                        onClick={() => handleTerima(row)}
                      >TERIMA</button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Confirmation Modal */}
      {confirmAction && (
        <div className="modal-overlay" onClick={() => !processing && setConfirmAction(null)}>
          <div className="modal-content" onClick={e => e.stopPropagation()}>
            <div className="modal-icon">
              {confirmAction.type === 'terima' ? '✅' : '❌'}
            </div>
            <h3 className="modal-title">
              {confirmAction.type === 'terima' ? 'Terima Pengajuan?' : 'Tolak Pengajuan?'}
            </h3>
            <p className="modal-text">
              <strong>{confirmAction.item.nama}</strong><br/>
              {confirmAction.type === 'terima'
                ? 'Pengajuan ini akan diberi Nomor SK dan dipindahkan ke Riwayat.'
                : 'Pengajuan ini akan ditolak.'}
            </p>
            <div className="modal-actions">
              <button className="btn" style={{ background: '#e5e7eb', color: '#4b5563', padding: '0.6rem 2rem' }} onClick={() => setConfirmAction(null)} disabled={processing}>
                BATAL
              </button>
              <button
                className={`btn ${confirmAction.type === 'terima' ? 'btn-success' : 'btn-danger'}`}
                style={{ padding: '0.6rem 2rem', opacity: processing ? 0.7 : 1 }}
                onClick={executeAction}
                disabled={processing}
              >
                {processing ? 'PROSES...' : confirmAction.type === 'terima' ? 'YA, TERIMA' : 'YA, TOLAK'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default PemberianNomor;
