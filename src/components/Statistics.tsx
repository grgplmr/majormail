import React from 'react';
import { TrendingUp, Users, Mail, Calendar, Download } from 'lucide-react';
import { mockStatistics } from '../data/mockData';

export const Statistics: React.FC = () => {
  const stats = mockStatistics;

  return (
    <div className="max-w-7xl mx-auto">
      <div className="bg-white rounded-xl shadow-lg overflow-hidden">
        <div className="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
          <h1 className="text-2xl font-bold text-white mb-2">
            Statistiques d'utilisation
          </h1>
          <p className="text-green-100">
            Suivi des messages envoyés et engagement citoyen
          </p>
        </div>

        <div className="p-8">
          {/* Key Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div className="bg-blue-50 rounded-lg p-6">
              <div className="flex items-center">
                <Mail className="h-8 w-8 text-blue-600" />
                <div className="ml-4">
                  <p className="text-sm font-medium text-blue-600">Messages totaux</p>
                  <p className="text-2xl font-bold text-blue-900">{stats.totalMessages.toLocaleString()}</p>
                </div>
              </div>
            </div>

            <div className="bg-green-50 rounded-lg p-6">
              <div className="flex items-center">
                <TrendingUp className="h-8 w-8 text-green-600" />
                <div className="ml-4">
                  <p className="text-sm font-medium text-green-600">Cette semaine</p>
                  <p className="text-2xl font-bold text-green-900">{stats.messagesThisWeek}</p>
                </div>
              </div>
            </div>

            <div className="bg-purple-50 rounded-lg p-6">
              <div className="flex items-center">
                <Users className="h-8 w-8 text-purple-600" />
                <div className="ml-4">
                  <p className="text-sm font-medium text-purple-600">Communes actives</p>
                  <p className="text-2xl font-bold text-purple-900">{stats.topCommunes.length}</p>
                </div>
              </div>
            </div>

            <div className="bg-orange-50 rounded-lg p-6">
              <div className="flex items-center">
                <Calendar className="h-8 w-8 text-orange-600" />
                <div className="ml-4">
                  <p className="text-sm font-medium text-orange-600">Moyenne/jour</p>
                  <p className="text-2xl font-bold text-orange-900">
                    {Math.round(stats.dailyStats.reduce((acc, day) => acc + day.count, 0) / stats.dailyStats.length)}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {/* Top Communes */}
            <div className="bg-white border border-gray-200 rounded-lg p-6">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold text-gray-900">Top communes</h3>
                <button className="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                  <Download className="w-4 h-4 mr-1" />
                  Export
                </button>
              </div>
              <div className="space-y-3">
                {stats.topCommunes.map((commune, index) => (
                  <div key={commune.name} className="flex items-center justify-between">
                    <div className="flex items-center">
                      <span className="w-6 h-6 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-sm font-medium mr-3">
                        {index + 1}
                      </span>
                      <span className="text-sm font-medium text-gray-900">{commune.name}</span>
                    </div>
                    <span className="text-sm text-gray-500">{commune.count} messages</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Template Usage */}
            <div className="bg-white border border-gray-200 rounded-lg p-6">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold text-gray-900">Modèles populaires</h3>
                <button className="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                  <Download className="w-4 h-4 mr-1" />
                  Export
                </button>
              </div>
              <div className="space-y-3">
                {stats.templateUsage.map((template, index) => (
                  <div key={template.title} className="flex items-center justify-between">
                    <div className="flex items-center">
                      <div className="w-8 h-2 bg-gradient-to-r from-blue-500 to-blue-300 rounded mr-3" 
                           style={{ width: `${(template.count / stats.templateUsage[0].count) * 100}%` }}></div>
                      <span className="text-sm font-medium text-gray-900">{template.title}</span>
                    </div>
                    <span className="text-sm text-gray-500">{template.count}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Daily Activity Chart */}
          <div className="mt-8 bg-white border border-gray-200 rounded-lg p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">Activité des 7 derniers jours</h3>
            <div className="flex items-end justify-between h-64 space-x-2">
              {stats.dailyStats.map((day, index) => (
                <div key={day.date} className="flex flex-col items-center flex-1">
                  <div 
                    className="bg-blue-500 rounded-t w-full transition-all hover:bg-blue-600 cursor-pointer"
                    style={{ height: `${(day.count / Math.max(...stats.dailyStats.map(d => d.count))) * 200}px` }}
                    title={`${day.count} messages le ${new Date(day.date).toLocaleDateString('fr-FR')}`}
                  ></div>
                  <span className="text-xs text-gray-500 mt-2">
                    {new Date(day.date).toLocaleDateString('fr-FR', { weekday: 'short' })}
                  </span>
                  <span className="text-xs font-medium text-gray-700">{day.count}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Export Section */}
          <div className="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">Exports</h3>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <button className="bg-white border border-gray-300 rounded-lg p-4 hover:bg-gray-50 transition-colors flex items-center">
                <Download className="w-5 h-5 text-gray-600 mr-3" />
                <div className="text-left">
                  <div className="font-medium text-gray-900">Rapport mensuel</div>
                  <div className="text-sm text-gray-500">CSV complet</div>
                </div>
              </button>
              <button className="bg-white border border-gray-300 rounded-lg p-4 hover:bg-gray-50 transition-colors flex items-center">
                <Download className="w-5 h-5 text-gray-600 mr-3" />
                <div className="text-left">
                  <div className="font-medium text-gray-900">Top communes</div>
                  <div className="text-sm text-gray-500">Liste Excel</div>
                </div>
              </button>
              <button className="bg-white border border-gray-300 rounded-lg p-4 hover:bg-gray-50 transition-colors flex items-center">
                <Download className="w-5 h-5 text-gray-600 mr-3" />
                <div className="text-left">
                  <div className="font-medium text-gray-900">Modèles usage</div>
                  <div className="text-sm text-gray-500">Statistiques PDF</div>
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};