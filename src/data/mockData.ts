import { Commune, MessageTemplate, ContactMessage, Statistics } from '../types';

export const mockCommunes: Commune[] = [
  {
    id: '1',
    name: 'Paris 1er Arrondissement',
    codeInsee: '75101',
    mayorEmail: 'maire@paris1.fr',
    population: 16888,
    region: 'Île-de-France'
  },
  {
    id: '2',
    name: 'Lyon',
    codeInsee: '69123',
    mayorEmail: 'maire@lyon.fr',
    population: 522228,
    region: 'Auvergne-Rhône-Alpes'
  },
  {
    id: '3',
    name: 'Marseille',
    codeInsee: '13055',
    mayorEmail: 'maire@marseille.fr',
    population: 870018,
    region: 'Provence-Alpes-Côte d\'Azur'
  },
  {
    id: '4',
    name: 'Toulouse',
    codeInsee: '31555',
    mayorEmail: 'maire@toulouse.fr',
    population: 486828,
    region: 'Occitanie'
  },
  {
    id: '5',
    name: 'Nice',
    codeInsee: '06088',
    mayorEmail: 'maire@nice.fr',
    population: 341032,
    region: 'Provence-Alpes-Côte d\'Azur'
  },
  {
    id: '6',
    name: 'Nantes',
    codeInsee: '44109',
    mayorEmail: 'maire@nantes.fr',
    population: 320732,
    region: 'Pays de la Loire'
  }
];

export const mockMessageTemplates: MessageTemplate[] = [
  {
    id: '1',
    title: 'Suppression du ramassage porte-à-porte',
    subject: 'Préoccupation concernant la suppression du ramassage des déchets',
    content: `Madame la Maire, Monsieur le Maire,

Je vous écris en tant que citoyen(ne) de {commune} pour exprimer ma vive préoccupation concernant la suppression annoncée du ramassage porte-à-porte des déchets.

Cette décision aura un impact significatif sur la qualité de vie des habitants, particulièrement pour les personnes âgées et à mobilité réduite qui ne pourront pas facilement se déplacer vers les points de collecte.

Je vous demande de reconsidérer cette décision et d'étudier des alternatives qui préservent l'accessibilité du service public de collecte des déchets.

Cordialement,
{prenom} {nom}`,
    category: 'waste',
    usage: 234
  },
  {
    id: '2',
    title: 'Impact sur les personnes âgées',
    subject: 'Impact de la suppression du ramassage sur les personnes âgées',
    content: `Madame la Maire, Monsieur le Maire,

En tant que résident(e) de {commune}, je souhaite attirer votre attention sur l'impact particulièrement difficile que la suppression du ramassage porte-à-porte aura sur nos concitoyens âgés.

Beaucoup de personnes âgées de notre commune ne peuvent physiquement pas porter leurs déchets jusqu'aux points de collecte, ce qui risque de créer des situations d'insalubrité domiciliaire.

Je vous propose d'envisager un maintien du service pour les personnes de plus de 70 ans ou en situation de handicap.

Respectueusement,
{prenom} {nom}`,
    category: 'waste',
    usage: 156
  },
  {
    id: '3',
    title: 'Alternative écologique',
    subject: 'Proposition d\'alternatives écologiques',
    content: `Madame la Maire, Monsieur le Maire,

Je comprends les enjeux budgétaires qui motivent la suppression du ramassage porte-à-porte, mais je souhaiterais proposer des alternatives qui concilient économies et service public.

Pourquoi ne pas envisager :
- Un ramassage hebdomadaire au lieu de bi-hebdomadaire
- Des bacs collectifs par îlots d'habitation
- Un système de compostage communal encouragé

Ces solutions pourraient réduire les coûts tout en maintenant un service de qualité.

Cordialement,
{prenom} {nom}`,
    category: 'environment',
    usage: 89
  }
];

export const mockStatistics: Statistics = {
  totalMessages: 1247,
  messagesThisWeek: 34,
  topCommunes: [
    { name: 'Paris 1er', count: 156 },
    { name: 'Lyon', count: 134 },
    { name: 'Marseille', count: 122 },
    { name: 'Toulouse', count: 98 },
    { name: 'Nice', count: 87 }
  ],
  templateUsage: [
    { title: 'Suppression ramassage', count: 234 },
    { title: 'Impact personnes âgées', count: 156 },
    { title: 'Alternative écologique', count: 89 },
    { title: 'Coût du service', count: 67 },
    { title: 'Accessibilité PMR', count: 45 }
  ],
  dailyStats: [
    { date: '2025-01-15', count: 12 },
    { date: '2025-01-16', count: 8 },
    { date: '2025-01-17', count: 15 },
    { date: '2025-01-18', count: 22 },
    { date: '2025-01-19', count: 18 },
    { date: '2025-01-20', count: 25 },
    { date: '2025-01-21', count: 31 }
  ]
};