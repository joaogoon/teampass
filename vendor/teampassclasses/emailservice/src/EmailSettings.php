<?php
namespace TeampassClasses\EmailService;

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
 * @file      EmailService.php
 * @author    Nils Laumaillé (nils@teampass.net)
 * @copyright 2009-2025 Teampass.net
 * @license   GPL-3.0
 * @see       https://www.teampass.net
 */

class EmailSettings
{
    public $smtpServer;
    public $smtpAuth;
    public $authUsername;
    public $authPassword;
    public $port;
    public $security;
    public $from;
    public $fromName;
    public $debugLevel;
    public $dir;

    // Constructeur pour initialiser les paramètres
    public function __construct(array $SETTINGS)
    {
        $this->smtpServer = $SETTINGS['email_smtp_server'] ?? '';
        $this->smtpAuth = isset($SETTINGS['email_smtp_auth']) ? ((int) $SETTINGS['email_smtp_auth']) === 1 : false;
        $this->authUsername = $SETTINGS['email_auth_username'] ?? '';
        $this->authPassword = $SETTINGS['email_auth_pwd'] ?? '';
        $this->port = isset($SETTINGS['email_port']) ? (int) $SETTINGS['email_port'] : 25;
        $this->security = $SETTINGS['email_security'] ?? 'none';
        $this->from = $SETTINGS['email_from'] ?? 'no-reply@example.com';
        $this->fromName = $SETTINGS['email_from_name'] ?? 'No Reply';
        $this->debugLevel = $SETTINGS['email_debug_level'] ?? 0;
        $this->dir = $SETTINGS['cpassman_dir'] ?? __DIR__;
    }
}