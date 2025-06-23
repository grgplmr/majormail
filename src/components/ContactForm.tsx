import React, { useState } from 'react';
import { Search, Send, FileText, Edit3, CheckCircle } from 'lucide-react';
import { mockCommunes, mockMessageTemplates } from '../data/mockData';
import { Commune, MessageTemplate } from '../types';

export const ContactForm: React.FC = () => {
  const [selectedCommune, setSelectedCommune] = useState<Commune | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [showCommunes, setShowCommunes] = useState(false);
  const [messageType, setMessageType] = useState<'template' | 'custom'>('template');
  const [selectedTemplate, setSelectedTemplate] = useState<MessageTemplate | null>(null);
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    customMessage: ''
  });
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  const filteredCommunes = mockCommunes.filter(commune =>
    commune.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    
    // Simulate API call
    setTimeout(() => {
      setIsLoading(false);
      setIsSubmitted(true);
    }, 2000);
  };

  const renderMessage = (template: MessageTemplate) => {
    if (!selectedCommune || !formData.firstName || !formData.lastName) return template.content;
    
    return template.content
      .replace('{commune}', selectedCommune.name)
      .replace('{prenom}', formData.firstName)
      .replace('{nom}', formData.lastName)
      .replace('{email}', formData.email);
  };

  if (isSubmitted) {
    return (
      <div className="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8 text-center">
        <CheckCircle className="w-16 h-16 text-green-500 mx-auto mb-4" />
        <h2 className="text-2xl font-bold text-gray-900 mb-4">Message envoyé avec succès !</h2>
        <p className="text-gray-600 mb-6">
          Votre message a été transmis au maire de {selectedCommune?.name}. 
          Vous recevrez un accusé de réception par email.
        </p>
        <button
          onClick={() => setIsSubmitted(false)}
          className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
        >
          Envoyer un autre message
        </button>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto">
      <div className="bg-white rounded-xl shadow-lg overflow-hidden">
        <div className="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
          <h1 className="text-2xl font-bold text-white mb-2">
            Contactez votre maire en quelques clics
          </h1>
          <p className="text-blue-100">
            Exprimez vos préoccupations concernant la suppression du ramassage porte-à-porte
          </p>
        </div>

        <form onSubmit={handleSubmit} className="p-8 space-y-6">
          {/* Commune Selection */}
          <div className="relative">
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Votre commune *
            </label>
            <div className="relative">
              <input
                type="text"
                value={selectedCommune ? selectedCommune.name : searchTerm}
                onChange={(e) => {
                  setSearchTerm(e.target.value);
                  setSelectedCommune(null);
                  setShowCommunes(true);
                }}
                onFocus={() => setShowCommunes(true)}
                placeholder="Rechercher votre commune..."
                className="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              />
              <Search className="absolute left-3 top-3.5 h-5 w-5 text-gray-400" />
            </div>
            
            {showCommunes && filteredCommunes.length > 0 && (
              <div className="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                {filteredCommunes.map((commune) => (
                  <button
                    key={commune.id}
                    type="button"
                    onClick={() => {
                      setSelectedCommune(commune);
                      setShowCommunes(false);
                      setSearchTerm('');
                    }}
                    className="w-full px-4 py-3 text-left hover:bg-blue-50 border-b border-gray-100 last:border-b-0"
                  >
                    <div className="font-medium text-gray-900">{commune.name}</div>
                    <div className="text-sm text-gray-500">
                      {commune.region} • {commune.population.toLocaleString()} habitants
                    </div>
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Personal Information */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Prénom *
              </label>
              <input
                type="text"
                value={formData.firstName}
                onChange={(e) => setFormData({ ...formData, firstName: e.target.value })}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Nom *
              </label>
              <input
                type="text"
                value={formData.lastName}
                onChange={(e) => setFormData({ ...formData, lastName: e.target.value })}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Email *
            </label>
            <input
              type="email"
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              required
            />
          </div>

          {/* Message Type Selection */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-4">
              Type de message
            </label>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <button
                type="button"
                onClick={() => setMessageType('template')}
                className={`p-4 border-2 rounded-lg transition-colors ${
                  messageType === 'template'
                    ? 'border-blue-500 bg-blue-50'
                    : 'border-gray-300 hover:border-gray-400'
                }`}
              >
                <FileText className="w-6 h-6 mb-2 text-blue-600" />
                <div className="font-medium">Message type</div>
                <div className="text-sm text-gray-500">Choisir parmi nos modèles</div>
              </button>
              <button
                type="button"
                onClick={() => setMessageType('custom')}
                className={`p-4 border-2 rounded-lg transition-colors ${
                  messageType === 'custom'
                    ? 'border-blue-500 bg-blue-50'
                    : 'border-gray-300 hover:border-gray-400'
                }`}
              >
                <Edit3 className="w-6 h-6 mb-2 text-blue-600" />
                <div className="font-medium">Message personnel</div>
                <div className="text-sm text-gray-500">Rédiger votre propre message</div>
              </button>
            </div>
          </div>

          {/* Template Selection */}
          {messageType === 'template' && (
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Choisir un modèle
              </label>
              <select
                value={selectedTemplate?.id || ''}
                onChange={(e) => {
                  const template = mockMessageTemplates.find(t => t.id === e.target.value);
                  setSelectedTemplate(template || null);
                }}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required={messageType === 'template'}
              >
                <option value="">Sélectionner un modèle...</option>
                {mockMessageTemplates.map((template) => (
                  <option key={template.id} value={template.id}>
                    {template.title} (utilisé {template.usage} fois)
                  </option>
                ))}
              </select>
            </div>
          )}

          {/* Message Preview/Edit */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              {messageType === 'template' ? 'Aperçu du message' : 'Votre message *'}
            </label>
            <textarea
              value={
                messageType === 'template' && selectedTemplate
                  ? renderMessage(selectedTemplate)
                  : formData.customMessage
              }
              onChange={(e) => {
                if (messageType === 'custom') {
                  setFormData({ ...formData, customMessage: e.target.value });
                }
              }}
              rows={12}
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder={messageType === 'custom' ? 'Rédigez votre message...' : ''}
              required={messageType === 'custom'}
              readOnly={messageType === 'template'}
            />
          </div>

          {/* Submit Button */}
          <div className="flex justify-center">
            <button
              type="submit"
              disabled={isLoading || !selectedCommune || (messageType === 'template' && !selectedTemplate)}
              className="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors inline-flex items-center"
            >
              {isLoading ? (
                <>
                  <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                  Envoi en cours...
                </>
              ) : (
                <>
                  <Send className="w-5 h-5 mr-2" />
                  Envoyer le message
                </>
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};