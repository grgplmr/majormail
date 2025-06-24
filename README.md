# majormail
Plugin WordPress pour l'envoi d'un message aux communes.

## Compilation du code React

L'interface du plugin est développée avec React et TypeScript. Elle est
compilée grâce à [Vite](https://vitejs.dev/). Pour générer les fichiers
JavaScript finaux placés dans `assets/js`, suivez les étapes ci-dessous :

1. Installez les dépendances npm :

   ```bash
   npm install
   ```

2. Lancez la compilation :

   ```bash
   npm run build
   ```

Le fichier `assets/js/frontend.js` sera alors créé et pourra être embarqué
dans l'archive du plugin.
