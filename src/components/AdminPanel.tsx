import React, { useState } from 'react';
import { Users, FileText, AlertTriangle, Settings, Plus, Edit, Trash2, Download } from 'lucide-react';
import { mockCommunes, mockMessageTemplates } from '../data/mockData';

type AdminTab = 'communes' | 'templates' | 'moderation' | 'settings';

export const AdminPanel: React.FC = () => {
  const [activeTab, setActiveTab] = useState<AdminTab>('communes');
  const [showAddModal, setShowAddModal] = useState(false);

  const tabs = [
    { id: 'communes' as AdminTab, label: 'Communes & Maires', icon: Users },
    { id: 'templates' as AdminTab, label: 'Modèles de messages', icon: FileText },
    { id: 'moderation' as AdminTab, label: 'Modération', icon: AlertTriangle },
    { id: 'settings' as AdminTab, label: 'Paramètres', icon: Settings }
  ];

  const renderCommunesTab = () => (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold text-gray-900">Gestion des communes</h2>
        <div className="flex space-x-3">
          <button className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
            <Download className="w-4 h-4 mr-2" />
            Importer CSV
          </button>
          <button 
            onClick={() => setShowAddModal(true)}
            className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center"
          >
            <Plus className="w-4 h-4 mr-2" />
            Ajouter commune
          </button>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Commune
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Code INSEE
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Population
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email Maire
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {mockCommunes.map((commune) => (
              <tr key={commune.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">{commune.name}</div>
                  <div className="text-sm text-gray-500">{commune.region}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {commune.codeInsee}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {commune.population.toLocaleString()}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {commune.mayorEmail}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button className="text-blue-600 hover:text-blue-900 mr-3">
                    <Edit className="w-4 h-4" />
                  </button>
                  <button className="text-red-600 hover:text-red-900">
                    <Trash2 className="w-4 h-4" />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );

  const renderTemplatesTab = () => (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold text-gray-900">Modèles de messages</h2>
        <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
          <Plus className="w-4 h-4 mr-2" />
          Nouveau modèle
        </button>
      </div>

      <div className="grid gap-6">
        {mockMessageTemplates.map((template) => (
          <div key={template.id} className="bg-white rounded-lg shadow p-6">
            <div className="flex justify-between items-start mb-4">
              <div>
                <h3 className="text-lg font-semibold text-gray-900">{template.title}</h3>
                <p className="text-sm text-gray-500">
                  Catégorie: {template.category} • Utilisé {template.usage} fois
                </p>
              </div>
              <div className="flex space-x-2">
                <button className="text-blue-600 hover:text-blue-900">
                  <Edit className="w-4 h-4" />
                </button>
                <button className="text-red-600 hover:text-red-900">
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>
            </div>
            <div className="bg-gray-50 rounded p-4">
              <p className="text-sm font-medium text-gray-700 mb-2">Objet: {template.subject}</p>
              <p className="text-sm text-gray-600 whitespace-pre-line">{template.content.substring(0, 200)}...</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );

  const renderModerationTab = () => (
    <div>
      <h2 className="text-xl font-semibold text-gray-900 mb-6">Modération des contenus</h2>
      
      <div className="space-y-6">
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Mots interdits</h3>
          <textarea
            className="w-full h-32 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Entrez les mots interdits, un par ligne..."
            defaultValue="spam\ninsulte\nviolence\nharcèlement"
          />
          <button className="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Sauvegarder
          </button>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-lg font-semibold text-gray-900">Messages en attente</h3>
            <span className="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-sm">3 en attente</span>
          </div>
          <p className="text-gray-600">Aucun message en attente de modération</p>
        </div>
      </div>
    </div>
  );

  const renderSettingsTab = () => (
    <div>
      <h2 className="text-xl font-semibold text-gray-900 mb-6">Paramètres généraux</h2>
      
      <div className="space-y-6">
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Configuration email</h3>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Objet par défaut
              </label>
              <input
                type="text"
                defaultValue="Message de {prenom} {nom} - {commune}"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Message d'accusé de réception
              </label>
              <textarea
                rows={4}
                defaultValue="Votre message a bien été transmis au maire de {commune}. Vous devriez recevoir une réponse dans les prochains jours."
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Sécurité</h3>
          <div className="space-y-4">
            <div className="flex items-center">
              <input type="checkbox" defaultChecked className="mr-3" />
              <label className="text-sm text-gray-700">Activer Google reCAPTCHA v3</label>
            </div>
            <div className="flex items-center">
              <input type="checkbox" defaultChecked className="mr-3" />
              <label className="text-sm text-gray-700">Purge automatique des logs (12 mois)</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  );

  return (
    <div className="max-w-7xl mx-auto">
      <div className="bg-white rounded-xl shadow-lg overflow-hidden">
        <div className="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
          <h1 className="text-2xl font-bold text-white mb-2">
            Administration
          </h1>
          <p className="text-blue-100">
            Gestion des communes, modèles et paramètres du plugin
          </p>
        </div>

        <div className="flex">
          {/* Sidebar */}
          <div className="w-64 bg-gray-50 border-r border-gray-200">
            <nav className="mt-6">
              {tabs.map((tab) => {
                const Icon = tab.icon;
                return (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`w-full flex items-center px-6 py-3 text-sm font-medium transition-colors ${
                      activeTab === tab.id
                        ? 'bg-blue-50 border-r-2 border-blue-500 text-blue-700'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                    }`}
                  >
                    <Icon className="w-5 h-5 mr-3" />
                    {tab.label}
                  </button>
                );
              })}
            </nav>
          </div>

          {/* Main Content */}
          <div className="flex-1 p-8">
            {activeTab === 'communes' && renderCommunesTab()}
            {activeTab === 'templates' && renderTemplatesTab()}
            {activeTab === 'moderation' && renderModerationTab()}
            {activeTab === 'settings' && renderSettingsTab()}
          </div>
        </div>
      </div>
    </div>
  );
};