<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


include_once('phpreport/model/facade/actionplugin/ActionPlugin.php');

class EmailAdminPlugin extends ActionPlugin {

    public function __construct($action) {
        $this->pluggedAction = $action;
    }

    //TODO: actually send the email
    public function run($status) {
        if ($this->pluggedAction instanceof CreateUserAction) {
            if ($status)
                echo "ok\n";
            else
                echo "bad\n";
        }
        // if the action doesn't belong to one of those classes,
        // we do nothing
    }
}
