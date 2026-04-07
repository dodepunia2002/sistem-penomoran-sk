import React, { useState } from 'react';
import { Calendar } from 'lucide-react';
import { apiCall } from '../../context/AuthContext';
import { useToast } from '../../context/DataContext';

const InputData = () => {
  const { addToast } = useToast();
  const [formData, setFormData] = useState({
    nama: '',
    alamat: '',
    tanggal: ''
  });
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);

    try {
      const data = await apiCall('/api/pengajuan', {
        method: 'POST',
        body: JSON.stringify(formData),
      });
      addToast(data.message, 'success');
      setFormData({ nama: '', alamat: '', tanggal: '' });
    } catch (err) {
      addToast(err.message, 'danger');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div>
      <h2 className="page-title">DASHBOARD PETUGAS</h2>
      
      <div className="card">
        <h3 style={{ fontSize: '1.1rem', marginBottom: '1.5rem', textTransform: 'uppercase' }}>
          Form Input Data Pengajuan Nomor SK
        </h3>
        
        <form onSubmit={handleSubmit} style={{ maxWidth: '800px' }}>
          <div className="form-group">
            <label>Nama</label>
            <input 
              type="text" 
              className="form-input" 
              placeholder="Masukan Nama Lokasi"
              value={formData.nama}
              onChange={(e) => setFormData({...formData, nama: e.target.value})}
              required
              disabled={isSubmitting}
            />
          </div>
          
          <div className="form-group">
            <label>Alamat</label>
            <input 
              type="text" 
              className="form-input" 
              placeholder="Masukan Alamat"
              value={formData.alamat}
              onChange={(e) => setFormData({...formData, alamat: e.target.value})}
              required
              disabled={isSubmitting}
            />
          </div>
          
          <div className="form-group">
            <label>Tanggal</label>
            <div style={{ position: 'relative' }}>
              <input 
                type="date" 
                className="form-input" 
                style={{ width: '100%', appearance: 'none' }}
                value={formData.tanggal}
                onChange={(e) => setFormData({...formData, tanggal: e.target.value})}
                required
                disabled={isSubmitting}
              />
              <Calendar size={20} color="#6b7280" style={{ position: 'absolute', right: '1rem', top: '50%', transform: 'translateY(-50%)', pointerEvents: 'none' }} />
            </div>
          </div>
          
          <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '1rem', marginTop: '2rem' }}>
            <button 
              type="button" 
              className="btn" 
              style={{ background: '#e5e7eb', color: '#4b5563', padding: '0.6rem 2rem' }} 
              onClick={() => setFormData({nama:'', alamat:'', tanggal:''})}
              disabled={isSubmitting}
            >BATAL</button>
            <button 
              type="submit" 
              className="btn btn-primary" 
              style={{ background: '#2544c2', padding: '0.6rem 2rem', opacity: isSubmitting ? 0.7 : 1 }}
              disabled={isSubmitting}
            >
              {isSubmitting ? 'MENGIRIM...' : 'KIRIM'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default InputData;
