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

/**
 * HTTP PARAMETERS RECEIVED BY THIS PAGE:
 *
 * login = Login of the user
 * password = Password of the user
 */


    $userLogin = $_GET['login'];

    $userPassword = $_GET['password'];

    //$userLogin = 'jjjameson';

    //$userPassword = 'jaragunde';


    $string = "";

    try{

        $user = UsersFacade::Login($userLogin, $userPassword);

        session_start();

        $_SESSION['user'] = $user;

        $sessionId = session_id();

        $string = $string . "<login><sessionId>$sessionId</sessionId></login>";

    }
    catch(IncorrectLoginException $exc){

    $string = $string . "<login><error id='1'>" . $exc->getMessage() . "</error></login>";

    }

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();