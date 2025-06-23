import React, { useState } from 'react';
import { Header } from './components/Header';
import { ContactForm } from './components/ContactForm';
import { AdminPanel } from './components/AdminPanel';
import { Statistics } from './components/Statistics';

type View = 'contact' | 'admin' | 'stats';

function App() {
  const [currentView, setCurrentView] = useState<View>('contact');

  const renderCurrentView = () => {
    switch (currentView) {
      case 'contact':
        return <ContactForm />;
      case 'admin':
        return <AdminPanel />;
      case 'stats':
        return <Statistics />;
      default:
        return <ContactForm />;
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header currentView={currentView} onViewChange={setCurrentView} />
      <main className="py-8 px-4 sm:px-6 lg:px-8">
        {renderCurrentView()}
      </main>
      
      {/* Footer */}
      <footer className="bg-white border-t border-gray-200 mt-12">
        <div className="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row justify-between items-center">
            <div className="flex items-center mb-4 md:mb-0">
              <div className="text-sm text-gray-600">
                © 2025 touchepasamespoubelles.fr - Plugin Interpeller son Maire v1.0
              </div>
            </div>
            <div className="flex space-x-6 text-sm text-gray-600">
              <a href="#" className="hover:text-blue-600 transition-colors">Politique de confidentialité</a>
              <a href="#" className="hover:text-blue-600 transition-colors">RGPD</a>
              <a href="#" className="hover:text-blue-600 transition-colors">Contact</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}

export default App;