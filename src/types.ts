export interface Commune {
  id: string;
  name: string;
  codeInsee: string;
  mayorEmail: string;
  population: number;
  region: string;
}

export interface MessageTemplate {
  id: string;
  title: string;
  subject: string;
  content: string;
  category: 'waste' | 'environment' | 'services' | 'transport';
  usage: number;
}

export interface ContactMessage {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  commune: string;
  message: string;
  templateId?: string;
  timestamp: Date;
  status: 'sent' | 'delivered' | 'error';
}

export interface Statistics {
  totalMessages: number;
  messagesThisWeek: number;
  topCommunes: Array<{ name: string; count: number }>;
  templateUsage: Array<{ title: string; count: number }>;
  dailyStats: Array<{ date: string; count: number }>;
}

export interface User {
  id: string;
  email: string;
  role: 'admin' | 'user';
  name: string;
}