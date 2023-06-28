/**
 * ENVF slider block
 *
 * @package
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'block_envf_slider/config'], function($) {
    return function(locator, config) {
        require(['glide'], function(Glide) {
            // Show the slider now we are initialised.
            $(locator).removeClass('d-none');
            let glide = new Glide(locator, config);
            glide.mount();

            let forward = document.querySelector('#arrow-right');
            let backward = document.querySelector('#arrow-left');

            forward.addEventListener('click', function() {
                glide.go('>');
            });
            backward.addEventListener('click', function() {
                glide.go('<');
            });

            let footeritems = document.getElementsByClassName('slidefooter-item');

            for (let i = 0; i < footeritems.length; i++) {
                var footeritem = footeritems[i];
                footeritem.addEventListener('click', function() {
                    glide.go('=' + i.toString());
                });
            }
        });
    };
});

