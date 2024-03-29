/**
 * RSS Thumbnails block
 *
 * @copyright 2022 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/config'], function(cfg) {
    window.requirejs.config({
        paths: {
            "glide":
                cfg.wwwroot
                + '/lib/javascript.php/'
                + cfg.jsrev
                + '/blocks/envf_slider/js/glide/dist/glide'
                + (cfg.developerdebug ? '.min' : ''),
        },
        shim: {
            'glide': {exports: 'glide'},
        }
    });
});
