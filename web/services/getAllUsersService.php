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


   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/UsersFacade.php');
   include_once('phpreport/model/vo/UserVO.php');

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<report";
            if ($init!="")
                $string = $string . " init='" . $init . "'";
            if ($end!="")
                $string = $string . " end='" . $end . "'";
            $string = $string . "><error id='2'>You must be logged in</error></report>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<report";
            if ($init!="")
                $string = $string . " init='" . $init . "'";
            if ($end!="")
                $string = $string . " end='" . $end . "'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></report>";
            break;
        }

        $users = UsersFacade::GetAllUsers();

        $string = "<users>";

        foreach((array) $users as $user)
        {
            $string = $string . "<user><id>{$user->getId()}</id><login>{$user->getLogin()}</login><userGroups>";

            foreach ((array) $user->getGroups() as $group)
                $string = $string . "<{$group->getName()}>true</{$group->getName()}>";

            $string = $string . "</userGroups></user>";
        }

        $string = $string . "</users>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
