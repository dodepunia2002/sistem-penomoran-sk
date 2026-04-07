import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import Landing from './pages/Landing';
import Login from './pages/Login';
import DashboardLayout from './components/Layout';
// Admin Pages
import PemberianNomor from './pages/admin/PemberianNomor';
import RiwayatAdmin from './pages/admin/Riwayat';
import ManajemenUser from './pages/admin/ManajemenUser';
// Petugas Pages
import InputData from './pages/petugas/InputData';
import RiwayatPetugas from './pages/petugas/Riwayat';

// Protected Route wrapper
function ProtectedRoute({ children, allowedRoles }) {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', minHeight: '100vh' }}>
        <div className="spinner" />
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  if (allowedRoles && !allowedRoles.includes(user.role)) {
    return <Navigate to={user.role === 'admin' ? '/admin' : '/petugas'} replace />;
  }

  return children;
}

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Landing />} />
        <Route path="/login" element={<Login />} />
        
        {/* Admin Routes */}
        <Route path="/admin" element={
          <ProtectedRoute allowedRoles={['admin']}>
            <DashboardLayout role="admin" />
          </ProtectedRoute>
        }>
          <Route index element={<Navigate to="/admin/pemberian-nomor" replace />} />
          <Route path="pemberian-nomor" element={<PemberianNomor />} />
          <Route path="riwayat" element={<RiwayatAdmin />} />
          <Route path="manajemen-user" element={<ManajemenUser />} />
        </Route>

        {/* Petugas Routes */}
        <Route path="/petugas" element={
          <ProtectedRoute allowedRoles={['petugas']}>
            <DashboardLayout role="petugas" />
          </ProtectedRoute>
        }>
          <Route index element={<Navigate to="/petugas/input-data" replace />} />
          <Route path="input-data" element={<InputData />} />
          <Route path="riwayat" element={<RiwayatPetugas />} />
        </Route>

        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
