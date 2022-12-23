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

### Todos, Bugs & Issues
- Problème de deletion de slide:
> #### Reproduction :
>> 1. Ajouter un block ENVF Slider
>> 2. Le configurer.
>> 3. Ajouter 1 ou 2 slides.
>> 4. Cliquer sur "Save changes".
>> 5. Re-configurer le block.
>> 6. Cliquer sur "Delete slide n°x".
> #### Résultat :
>> La configuration du block est invalide puisqu'on y trouve 2 ids et 2 valeurs pour whitetext
> #### Cause :
>> Lors de l'appel à `formslib::exportValues()` l'attribut `_elements` du formulaire contient toujours les champs supprimés.
> 
>*cf. Todos dans `block_envf_slider_edit_form::set_data`*
- Ids de slides à leur création toujours à 0.
> #### Reproduction :
>> 1. Ajouter un block ENVF Slider
>> 2. Le configurer.
>> 3. Ajouter plusieurs slides.
> #### Résultat :
>> Les champs `hidden` `config_slide_id` de toutes les slides ont une valeur égale à 0. 
> #### Cause possible :
>> Lors de l'appel à `MoodleQuickForm::createElement` dans `block_envf_slider_edit_form::add_slides_elements()` pour créer le champ
> `config_slide_id` *(cf. l~149-153)*, la valeur est directement initialisée. Si cette méthode est appelée avant que la slide précédente ait pu être créée, la valeur restera la même.  
>
>*cf. Todos dans `block_envf_slider_edit_form::add_slides_elements()`*
- Remplacer le champ white text par un color picker ?
- Utilisation de générateur dans les tests behat ?