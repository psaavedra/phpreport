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
   include_once('phpreport/model/facade/TasksFacade.php');
   include_once('phpreport/model/vo/UserVO.php');


    $date = $_GET['date'];

    $dateFormat = $_GET['dateFormat'];

    $sid = $_GET['sid'];

    if ($dateFormat=="")
        $dateFormat = "Y-m-d";

    if ($date == "")
        $date = new DateTime();
    else
    {
        $dateParse = date_parse_from_format($dateFormat, $date);

        $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";

        $date = date_create($date);
    }

    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<personalSummary uid='" . $userLogin . "' date='" . $date->format($dateFormat) . "'><error id='2'>You must be logged in</error></personalSummary>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<personalSummary uid='" . $userLogin . "' date='" . $date->format($dateFormat) . "'><error id='3'>Forbidden service for this User</error></personalSummary>";
            break;
        }

        $userVO = $_SESSION['user'];

        $summary = TasksFacade::GetPersonalSummaryByLoginDate($userVO, $date);

        $string = "<personalSummary login='" . $userVO->getLogin() . "' date='" . $date->format($dateFormat) . "'><hours><day>" . $summary['day']  . "</day><week>" . $summary['week']  . "</week><month>" . $summary['month']  . "</month></hours></personalSummary>";

    } while(false);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
