import { useContext } from 'react';
import { AuthContext } from '../contexts/authContextObject';

export const useAuth = () => useContext(AuthContext);
