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


/** File for UpdateExtraHourAction
 *
 *  This file just contains {@link UpdateExtraHourAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge L�pez Fern�ndez <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ExtraHourVO.php');

/** Update Extra Hour Action
 *
 *  This action is used for updating an Extra Hour.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge L�pez Fern�ndez <jlopez@igalia.com>
 */
class UpdateExtraHourAction extends Action{

    /** The Extra Hour
     *
     * This variable contains the Extra Hour we want to update.
     *
     * @var ExtraHourVO
     */
    private $extraHour;

    /** UpdateExtraHourAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ExtraHourVO $extraHour the Extra Hour value object we want to update.
     */
    public function __construct(ExtraHourVO $extraHour) {
        $this->extraHour=$extraHour;
        $this->preActionParameter="UPDATE_EXTRA_HOUR_PREACTION";
        $this->postActionParameter="UPDATE_EXTRA_HOUR_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Extra Hour on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getExtraHourDAO();
        if ($dao->update($this->extraHour)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$uservo= new UserVO();
$uservo->setLogin('jjsantos');
$uservo->setPassword('jaragunde');
$action= new CreateUserAction($uservo);
var_dump($action);
$action->execute();
var_dump($uservo);
*/
