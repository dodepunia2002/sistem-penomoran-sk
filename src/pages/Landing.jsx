import React from 'react';
import { useNavigate } from 'react-router-dom';
import './Auth.css';

const Landing = () => {
  const navigate = useNavigate();

  return (
    <div className="auth-wrapper">
      <div className="auth-bg-arch"></div>
      <div className="auth-bg-arch-inner"></div>
      
      <div className="auth-content">
        <div className="auth-logo">
          <img src="/logo-dishub.png" alt="Logo Dishub Gianyar" className="logo-image" />
          <div className="logo-text">
            DINAS PERHUBUNGAN<br />KABUPATEN GIANYAR
          </div>
        </div>

        <h1 className="landing-title">
          CEK PENOMORAN <span>SURAT KEPUTUSAN</span><br />
          ANDA DENGAN MUDAH DAN CEPAT
        </h1>

        <button 
          className="btn btn-accent" 
          onClick={() => navigate('/login')}
          style={{ padding: '0.75rem 2.5rem', borderRadius: '999px' }}
        >
          KLIK DISINI
        </button>
      </div>
    </div>
  );
};

export default Landing;
