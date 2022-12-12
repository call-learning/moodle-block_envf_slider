# moodle-block_envf_slider

## Specification

### Contexte et cas d’utilisation
Dans le cadre du site Moodle du [concours vétérinaire](https://concours-veto-postbac.fr/), des affichages plus dynamiques sont souhaités.
> L’idée est de pouvoir créer un bloc qui peut s’insérer dans toute page (les pages de garde ou dashboard ou autre), qui est configurable et qui permet 
> de créer des “slides”:
> - Avec des images
> - Du texte

On doit pouvoir paramétrer le slider pour:
- Affichage du texte (gauche/droite de l’image)
- Filtre pour atténuer le fond (couleur ou gradient), possiblement CSS
- Délai pour chaque slide
- Suggestions et exemples

### Intégration de Glider dans la page:
- [Configuration basique](https://ressourcesnumeriques.hesam.eu) présent dans le thème de l'HESAM, code source [ici](https://github.com/call-learning/moodle-theme_ressourcesnum/blob/master/classes/local/settings.php)
- [Slider du Moodle Plugin Directory](https://moodle.org/plugins/block_slider)
> Ne marche pas très bien en fait, mais peut servir de base pour les paramétrages

### Contraintes
- Pour être cohérent avec les développements précédents, on devra utiliser la bibliothèque [Glider JS](https://nickpiscitelli.github.io/Glider.js)
- Responsif
- Test unitaires mais aussi behat (notamment gérer le mode responsif avec Galen)

