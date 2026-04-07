import React from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { PlusSquare, Clock, LogOut, Users } from 'lucide-react';
import { useAuth } from '../context/AuthContext';
import './Sidebar.css';

const Sidebar = ({ role }) => {
  const navigate = useNavigate();
  const { logout, user } = useAuth();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const adminLinks = [
    { name: 'PEMBERIAN NOMOR', path: '/admin/pemberian-nomor', icon: <PlusSquare size={20} /> },
    { name: 'RIWAYAT PENOMORAN', path: '/admin/riwayat', icon: <Clock size={20} /> },
    { name: 'MANAJEMEN USER', path: '/admin/manajemen-user', icon: <Users size={20} /> },
  ];

  const petugasLinks = [
    { name: 'INPUT DATA PENGAJUAN NOMOR', path: '/petugas/input-data', icon: <PlusSquare size={20} /> },
    { name: 'RIWAYAT PENGAJUAN', path: '/petugas/riwayat', icon: <Clock size={20} /> },
  ];

  const links = role === 'admin' ? adminLinks : petugasLinks;

  return (
    <aside className="sidebar">
      <div className="sidebar-header">
        <div className="logo-icon">
          <img src="/logo-dishub.png" alt="Logo Dishub" className="sidebar-logo-img" />
        </div>
        <div className="logo-text">
          DINAS PERHUBUNGAN<br />KABUPATEN GIANYAR
        </div>
      </div>
      
      <nav className="sidebar-nav">
        {links.map((link) => (
          <NavLink
            key={link.path}
            to={link.path}
            className={({ isActive }) => `sidebar-link ${isActive ? 'active' : ''}`}
          >
            {link.icon}
            <span>{link.name}</span>
          </NavLink>
        ))}
      </nav>

      <div className="sidebar-footer">
        {user && (
          <div className="user-info">
            <span className="user-name">{user.nama_lengkap}</span>
            <span className="user-role">{user.role.toUpperCase()}</span>
          </div>
        )}
        <button onClick={handleLogout} className="logout-btn">
          KELUAR <LogOut size={20} className="ml-2" />
        </button>
      </div>
    </aside>
  );
};

export default Sidebar;
