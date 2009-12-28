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


/** File for TasksFacade
 *
 *  This file just contains {@link TasksFacade}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge L�pez Fern�ndez <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/CreateReportAction.php');
include_once('phpreport/model/facade/action/DeleteReportAction.php');
include_once('phpreport/model/facade/action/UpdateReportAction.php');
include_once('phpreport/model/facade/action/PartialUpdateReportAction.php');
include_once('phpreport/model/facade/action/GetUserTasksAction.php');
include_once('phpreport/model/facade/action/GetGlobalUsersProjectsReportAction.php');
include_once('phpreport/model/facade/action/GetGlobalUsersProjectsCustomersReportAction.php');
include_once('phpreport/model/facade/action/GetUserProjectCustomerReportAction.php');
include_once('phpreport/model/facade/action/GetProjectTtypeReportAction.php');
include_once('phpreport/model/facade/action/GetProjectUserCustomerReportAction.php');
include_once('phpreport/model/facade/action/GetUserTasksByDateAction.php');
include_once('phpreport/model/facade/action/GetUserTasksByLoginDateAction.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskVO.php');
include_once('phpreport/model/vo/ProjectVO.php');
include_once('phpreport/model/vo/UserVO.php');

/** Tasks Facade
 *
 *  This Facade contains the functions used in tasks related to Tasks (yeah, I know it sounds stupid).
 *
 * @package PhpReport
 * @subpackage facade
 * @todo create the retrieval functions.
 * @author Jorge L�pez Fern�ndez <jlopez@igalia.com>
 */
abstract class TasksFacade {

    /** Create Task Function
     *
     *  This function is used for creating a new Task.
     *
     * @param TaskVO $task the Task value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateReport(TaskVO $task) {

    $action = new CreateReportAction($task);

    return $action->execute();

    }

    /** Create Tasks Function
     *
     *  This function is used for creating an array of new Tasks.
     *  If an error occurs, it stops creating.
     *
     * @param array $tasks the Task value objects we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateReports($tasks) {

    foreach((array)$tasks as $task)
        if ((TasksFacade::CreateReport($task)) == -1)
            return -1;

    return 0;

    }

    /** Delete Task Function
     *
     *  This function is used for deleting a Task.
     *
     * @param TaskVO $task the Task value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteReport(TaskVO $task) {

    $action = new DeleteReportAction($task);

    return $action->execute();

    }

    /** Delete Reports Function
     *
     *  This function is used for deleting an array of Tasks.
     *  If an error occurs, it stops deleting.
     *
     * @param array $tasks the Task value objects we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteReports($tasks) {

    foreach((array)$tasks as $task)
        if ((TasksFacade::DeleteReport($task)) == -1)
            return -1;

    return 0;

    }

    /** Update Task Function
     *
     *  This function is used for updating a Task.
     *
     * @param TaskVO $task the Task value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateReport(TaskVO $task) {

    $action = new UpdateReportAction($task);

    return $action->execute();

    }

    /** Partial Update Task Function
     *
     *  This function is used for partially updating a Task.
     *
     * @param TaskVO $task the Task value object we want to update.
     * @param array $update the updating flags of the Task VO.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function PartialUpdateReport(TaskVO $task, $update) {

    $action = new PartialUpdateReportAction($task, $update);

    return $action->execute();

    }

    /** Update Tasks Function
     *
     *  This function is used for updating an array of Tasks.
     *  If an error occurs, it stops updating.
     *
     * @param array $tasks the Task value objects we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateReports($tasks) {

    foreach((array)$tasks as $task)
        if ((TasksFacade::UpdateReport($task)) == -1)
            return -1;

    return 0;

    }

    /** Partial Update Tasks Function
     *
     *  This function is used for partially updating an array of Tasks.
     *  If an error occurs, it stops updating.
     *
     * @param array $tasks the Task value objects we want to update.
     * @param array $updates the updating flag arrays of the Task VOs.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function PartialUpdateReports($tasks, $updates) {

    foreach((array)$tasks as $i=>$task)
        if ((TasksFacade::PartialUpdateReport($task, $updates[$i])) == -1)
            return -1;

    return 0;

    }

    /** Get User Tasks Function
     *
     *  This function is used for retrieving all Tasks related to a User.
     *
     * @param UserVO $userVO the User whose Tasks we want to retieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUserTasks(UserVO $userVO) {

    $action = new GetUserTasksAction($userVO);

    return $action->execute();

    }

    /** Get User Tasks by date Function
     *
     *  This function is used for retrieving all Tasks related to a User on a date by his/her login.
     *
     * @param UserVO $userVO the User whose Tasks we want to retieve.
     * @param DateTime $date the date whose tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUserTasksByLoginDate(UserVO $userVO, DateTime $date) {

    $action = new GetUserTasksByLoginDateAction($userVO, $date);

    return $action->execute();
    }

    /** Get User Tasks by date Function
     *
     *  This function is used for retrieving all Tasks related to a User on a date.
     *
     * @param UserVO $userVO the User whose Tasks we want to retieve.
     * @param DateTime $date the date whose tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUserTasksByDate(UserVO $userVO, DateTime $date) {

    $action = new GetUserTasksByDateAction($userVO, $date);

    return $action->execute();

    }

    /**  Get Global Users Projects Report Action
     *
     *  This function is used for retrieving information about Tasks done by Users for each Project. We can pass dates
     *  with optional parameters <var>$init</var> and <var>$end</var> if we want to retrieve information about only an interval.
     *
     * @param DateTime $init the initial date of the interval whose Tasks report we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Tasks report we want to retrieve.
     * @return array an array with the resulting rows of computing the extra hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields <i>userid</i> and <i>projectid</i>).
     */
    static function GetGlobalUsersProjectsReport(DateTime $init = NULL, DateTime $end = NULL) {

    $action = new GetGlobalUsersProjectsReportAction($init, $end);

    return $action->execute();

    }

    /**  Get Project User Customer Report Action
     *
     *  This function is used for retrieving information about worked hours in Tasks related to a Project, grouped by User and Customer.
     *
     * @param ProjectVO $projectVO the Project whose Tasks report we want to retrieve.
     * @param DateTime $init the initial date of the interval whose Tasks report we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Tasks report we want to retrieve.
     * @return array an associative array with the worked hours data, with the User login as first level key and the Customer id
     * as second level one.
     */
    static function GetProjectUserCustomerReport(ProjectVO $projectVO, DateTime $init = NULL, DateTime $end = NULL) {

    $action = new GetProjectUserCustomerReportAction($projectVO, $init, $end);

    return $action->execute();

    }

    /** Get Global Users Projects Customers report Action
     *
     *  This function is used for retrieving information about Tasks done by Users. We can pass dates with optional
     *  parameters <var>$init</var> and <var>$end</var> if we want to retrieve information about only an interval.
     *
     * @param DateTime $init the initial date of the interval whose Tasks report we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Tasks report we want to retrieve.
     * @return array an array with the resulting rows of computing the worked hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields <i>userid</i>, <i>projectid</i> and <i>customerid</i>).
     */
    static function GetGlobalUsersProjectsCustomersReport(DateTime $init = NULL, DateTime $end = NULL) {

    $action = new GetGlobalUsersProjectsCustomersReportAction($init, $end);

    return $action->execute();

    }

    /** Get User Project Customer report Action
     *
     *  This function is used for for retrieving information about Tasks done by a User. We can pass dates with optional
     *  parameters <var>$init</var> and <var>$end</var> if we want to retrieve information about only an interval.
     *
     * @param DateTime $init the initial date of the interval whose Tasks report we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Tasks report we want to retrieve.
     * @return array an associative array with the worked hours data, with the Project description as first level key and
     * the Customer id as second level one.
     */
    static function GetUserProjectCustomerReport(UserVO $userVO, DateTime $init = NULL, DateTime $end = NULL) {

    $action = new GetUserProjectCustomerReportAction($userVO, $init, $end);

    return $action->execute();

    }

    /** Get Project Ttype report Action
     *
     *  This function is used for retrieving information about Tasks related to a Project, grouped by task type (ttype).
     *
     * @param ProjectVO $projectVO the Project whose Tasks report we want to retrieve.
     * @return array an array with the resulting rows of computing the worked hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields <i>projectid</i> and <i>ttype</i>).
     */
    static function GetProjectTtypeReport(ProjectVO $projectVO) {

    $action = new GetProjectTtypeReportAction($projectVO);

    return $action->execute();

    }

}

/*$task = new TaskVO();

$task->setId(1);

$task->setStory("lolololol");

$task->setTelework(true);

$update = array();

$update[telework] = true;

$update[story] = true;

$tasks[] = $task;

$updates[] = $update;

$task = new TaskVO();

$task->setId(2);

$task->setStory("lalalal");

$update = array();

$update[story] = false;

$tasks[] = $task;

$updates[] = $update;

TasksFacade::PartialUpdateReports($tasks, $updates);*/