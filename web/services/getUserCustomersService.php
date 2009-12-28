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
   include_once('phpreport/model/facade/CustomersFacade.php');
   include_once('phpreport/model/vo/UserVO.php');

    $userLogin = $_GET['uid'];

    $active = $_GET['active'];

    if (strtolower($active) == "true")
    $active = True;
    else
    $active = False;

    $login = $_GET['login'];

    $sid = $_GET['sid'];


    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<tasks";
            if ($userLogin!="")
            $string = $string . " login='" . $userLogin . "'";
            if ($active)
            $string = $string . " active = 'True'";
            $string = $string . "><error id='2'>You must be logged in</error></tasks>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<tasks";
            if ($userLogin!="")
            $string = $string . " login='" . $userLogin . "'";
            if ($active)
            $string = $string . " active = 'True'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></tasks>";
            break;
        }

        if ($userLogin == "")
        {
        $customers = CustomersFacade::GetCustomersByProjectUser(NULL, $active);
            $string = "<customers";
        if ($active)
            $string = $string . " active = 'True'";
        $string = $string . ">";
        }
        else
        {
            $userVO = new UserVO();

            $userVO->setLogin($userLogin);

            $customers = CustomersFacade::GetCustomersByProjectUser($userVO, $active);

                $string = "<customers login='" . $userLogin . "'";
            if ($active)
            $string = $string . " active = 'True'";
            $string = $string . ">";
        }

        foreach((array) $customers as $customer)
        {

        $string = $string . "<customer><id>{$customer->getId()}</id><sectorId>{$customer->getSectorId()}</sectorId><name>{$customer->getName()}</name><type>{$customer->getType()}</type><url>{$customer->getUrl()}</url></customer>";

        }

        $string = $string . "</customers>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();