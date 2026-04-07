import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { User, Lock, AlertCircle } from 'lucide-react';
import { useAuth } from '../context/AuthContext';
import './Auth.css';

const Login = () => {
  const navigate = useNavigate();
  const { login, user } = useAuth();
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  // If already logged in, redirect
  if (user) {
    navigate(user.role === 'admin' ? '/admin' : '/petugas', { replace: true });
    return null;
  }

  const handleLogin = async (e) => {
    e.preventDefault();
    setError('');
    setIsLoading(true);

    try {
      const loggedUser = await login(username, password);
      if (loggedUser.role === 'admin') {
        navigate('/admin');
      } else {
        navigate('/petugas');
      }
    } catch (err) {
      setError(err.message || 'Login gagal');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="auth-wrapper">
      <div className="auth-bg-arch"></div>
      <div className="auth-bg-arch-inner"></div>

      {/* Top Left Logo */}
      <div className="login-top-logo">
        <img src="/logo-dishub.png" alt="Logo Dishub" className="logo-image-small" />
        <div className="logo-text" style={{ textAlign: 'left', margin: 0, fontSize: '0.8rem' }}>
          DINAS PERHUBUNGAN<br />KABUPATEN GIANYAR
        </div>
      </div>

      <div className="auth-content" style={{ marginTop: '2rem' }}>
        <h2 className="login-title">LOG IN</h2>

        {error && (
          <div className="login-error">
            <AlertCircle size={16} />
            <span>{error}</span>
          </div>
        )}

        <form className="login-form" onSubmit={handleLogin}>
          <div className="input-container">
            <User size={18} className="input-icon" />
            <input 
              type="text" 
              placeholder="USERNAME" 
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              required
              disabled={isLoading}
            />
          </div>
          
          <div className="input-container">
            <Lock size={18} className="input-icon" />
            <input 
              type="password" 
              placeholder="PASSWORD" 
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              disabled={isLoading}
            />
          </div>

          <button 
            type="submit" 
            className="submit-btn" 
            style={{ borderRadius: '4px', opacity: isLoading ? 0.7 : 1 }}
            disabled={isLoading}
          >
            {isLoading ? 'LOADING...' : 'LOGIN'}
          </button>

          <a href="#" className="forgot-link">Forgot password?</a>
        </form>
      </div>
    </div>
  );
};

export default Login;
