<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade
 *
 * @package     block_envf_slider
 * @copyright   2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the envf_slider database.
 *
 * @param int $oldversion The version number of the plugin that was installed.
 * @return boolean
 */
function xmldb_block_envf_slider_upgrade($oldversion): bool {
    // Rss_thumbnails savepoint reached.
    if ($oldversion < 2022121201) {
        upgrade_block_savepoint(true, 2022121201, 'envf_slider');
    }
    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.
    return true;
}
