import React from 'react';
import { Mail, Shield, BarChart3 } from 'lucide-react';

interface HeaderProps {
  currentView: 'contact' | 'admin' | 'stats';
  onViewChange: (view: 'contact' | 'admin' | 'stats') => void;
}

export const Header: React.FC<HeaderProps> = ({ currentView, onViewChange }) => {
  return (
    <header className="bg-white shadow-sm border-b border-gray-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <div className="flex items-center">
            <div className="flex items-center">
              <Mail className="h-8 w-8 text-blue-600" />
              <div className="ml-3">
                <h1 className="text-xl font-bold text-gray-900">Interpeller son Maire</h1>
                <p className="text-sm text-gray-500">touchepasamespoubelles.fr</p>
              </div>
            </div>
          </div>
          
          <nav className="hidden md:flex space-x-8">
            <button
              onClick={() => onViewChange('contact')}
              className={`inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors ${
                currentView === 'contact'
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              <Mail className="w-4 h-4 mr-2" />
              Contacter
            </button>
            <button
              onClick={() => onViewChange('admin')}
              className={`inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors ${
                currentView === 'admin'
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              <Shield className="w-4 h-4 mr-2" />
              Administration
            </button>
            <button
              onClick={() => onViewChange('stats')}
              className={`inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors ${
                currentView === 'stats'
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              <BarChart3 className="w-4 h-4 mr-2" />
              Statistiques
            </button>
          </nav>

          <div className="md:hidden">
            <button className="text-gray-500 hover:text-gray-700">
              <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </header>
  );
};