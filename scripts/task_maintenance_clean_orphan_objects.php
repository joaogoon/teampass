<?php
/**
 * Teampass - a collaborative passwords manager.
 * ---
 * This file is part of the TeamPass project.
 * 
 * TeamPass is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 * 
 * TeamPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * 
 * Certain components of this file may be under different licenses. For
 * details, see the `licenses` directory or individual file headers.
 * ---
 * @file      task_maintenance_clean_orphan_objects.php
 * @author    Nils Laumaillé (nils@teampass.net)
 * @copyright 2009-2025 Teampass.net
 * @license   GPL-3.0
 * @see       https://www.teampass.net
 */

use TeampassClasses\Language\Language;
use TeampassClasses\ConfigManager\ConfigManager;

// Load functions
require_once __DIR__.'/../sources/main.functions.php';

// init
loadClasses('DB');
$lang = new Language('english');

// Load config
$configManager = new ConfigManager();
$SETTINGS = $configManager->getAllSettings();

// Define Timezone
date_default_timezone_set($SETTINGS['timezone'] ?? 'UTC');

// Set header properties
header('Content-type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
error_reporting(E_ERROR);
// increase the maximum amount of time a script is allowed to run
set_time_limit($SETTINGS['task_maximum_run_time']);

// --------------------------------- //

require_once __DIR__.'/background_tasks___functions.php';

// log start
$logID = doLog('ongoing', 'do_maintenance - clean-orphan-objects', 1);

// Perform maintenance tasks
cleanOrphanObjects();

// log end
doLog('completed', '', 1, $logID);

/**
 * Delete all orphan objects from DB
 *
 * @return void
 */
function cleanOrphanObjects(): void
{
    // Delete all item keys for which no user exist
    DB::query(
        'DELETE k.* FROM ' . prefixTable('sharekeys_items') . ' k
        LEFT JOIN ' . prefixTable('users') . ' u ON k.user_id = u.id
        WHERE u.id IS NULL OR u.deleted_at IS NOT NULL'
    );

    // Delete all files keys for which no item exist
    DB::query(
        'DELETE k.* FROM ' . prefixTable('sharekeys_files') . ' k
        LEFT JOIN ' . prefixTable('items') . ' i ON k.object_id = i.id
        WHERE i.id IS NULL'
    );

    // Delete all fields keys for which no item exist
    DB::query(
        'DELETE k.* FROM ' . prefixTable('sharekeys_fields') . ' k
        LEFT JOIN ' . prefixTable('categories_items') . ' c ON k.object_id = c.id
        LEFT JOIN ' . prefixTable('items') . ' i ON c.item_id = i.id
        WHERE c.id IS NULL OR i.id IS NULL'
    );

    // Delete all item logs for which no user exist
    DB::query(
        'DELETE l.* FROM ' . prefixTable('log_items') . ' l
        LEFT JOIN ' . prefixTable('items') . ' i ON l.id_item = i.id
        WHERE i.id IS NULL'
    );

    // Delete all system logs for which no user exist
    DB::query(
        'DELETE l.* FROM ' . prefixTable('log_system') . ' l
        LEFT JOIN ' . prefixTable('users') . ' u ON l.qui = u.id
        WHERE u.id IS NULL OR u.deleted_at IS NOT NULL'
    );


    // Update CACHE table
    updateCacheTable('reload', null);
}