export const localServiceAreas = [
  'Arles',
  'Les Baux-de-Provence',
  'Boulbon',
  'Fontvieille',
  'Graveson',
  'Maillane',
  'Mas-Blanc-des-Alpilles',
  'Maussane-les-Alpilles',
  'Paradou',
  'Saint-Étienne-du-Grès',
  'Saint-Martin-de-Crau',
  'Saint-Rémy-de-Provence',
  'Tarascon',
  'Beaucaire',
  'Bellegarde',
  'Fourques',
  'Saint-Gilles',
] as const;

export const localAreaGroups = [
  {
    name: 'Arles et la Camargue',
    places: ['Arles', 'Fourques', 'Saint-Gilles', 'Bellegarde'],
  },
  {
    name: 'Les Alpilles',
    places: [
      'Les Baux-de-Provence',
      'Fontvieille',
      'Maillane',
      'Mas-Blanc-des-Alpilles',
      'Maussane-les-Alpilles',
      'Paradou',
      'Saint-Étienne-du-Grès',
      'Saint-Rémy-de-Provence',
    ],
  },
  {
    name: 'Plaine de la Crau et vallée du Rhône',
    places: ['Saint-Martin-de-Crau', 'Boulbon', 'Graveson', 'Tarascon', 'Beaucaire'],
  },
] as const;
