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

/** assignCustomersToProject web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CustomersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><customers projectId="1"><customer><id>15</id></customer><customer><id>14</id></customer></customers>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'customers')
        {

            $sid = $parser->getAttribute("sid");

            $pid = $parser->getAttribute("projectId");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<return service='assignCustomersToProject'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='assignCustomersToProject'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            //print ($parser->name . "\n");

            if ($parser->name == "customer")
            {

                $parser->read();

                while ($parser->name != "customer") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "id":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $customerVO = CustomersFacade::GetCustomer($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $assignCustomers[] = $customerVO;

            }

        } while ($parser->read());

        //var_dump($assignCustomers);


        if (count($assignCustomers) >= 1)
            foreach((array)$assignCustomers as $assignCustomer)
            {
                if (ProjectsFacade::AssignCustomerToProject($assignCustomer->getId(), $pid) == -1)
                {
                    $string = "<return service='assignCustomersToProject'><error id='1'>There was some error while assigning the customers</error></return>";
                    break;
                }
            }



        if (!$string)
        {

            $string = "<return service='assignCustomersToProject'><ok>Operation Success!</ok><customers>";

            foreach((array) $assignCustomers as $assignCustomer)
            {
                $sector = CustomersFacade::GetSector($assignCustomer->getSectorId());
                $string = $string . "<customer><id>{$assignCustomer->getId()}</id><name>{$assignCustomer->getName()}</name><type>{$assignCustomer->getType()}</type><url>{$assignCustomer->getUrl()}</url><sector>{$sector->getName()}</sector></customer>";
            }

            $string = $string . "</customers></return>";

        }

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
