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


/** File for PostgreSQLTaskDAO
 *
 *  This file just contains {@link PostgreSQLTaskDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge L�pez Fern�ndez <jlopez@igalia.com>
 */

include_once('phpreport/util/TaskReportInvalidParameterException.php');
include_once('phpreport/util/DBPostgres.php');
include_once('phpreport/model/vo/TaskVO.php');
include_once('phpreport/model/vo/UserVO.php');
include_once('phpreport/model/vo/CustomerVO.php');
include_once('phpreport/model/vo/ProjectVO.php');
include_once('phpreport/model/dao/TaskDAO/TaskDAO.php');
include_once('phpreport/util/ConfigurationParametersManager.php');

/** DAO for Tasks in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link TaskDAO}.
 *
 * @see TaskDAO, TaskVO
 */
class PostgreSQLTaskDAO extends TaskDAO{

    /** Task DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link TaskDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see TaskDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Task value object constructor for PostgreSQL.
     *
     * This function creates a new {@link TaskVO} with data retrieved from database.
     *
     * @param array $row an array with the Task values from a row.
     * @return TaskVO a {@link TaskVO} with its properties set to the values from <var>$row</var>.
     * @see TaskVO
     */
    protected function setValues($row)
    {

    $taskVO = new TaskVO();

    $taskVO->setId($row[id]);
    $taskVO->setDate(date_create($row[_date]));
    $taskVO->setInit($row[init]);
    $taskVO->setEnd($row[_end]);
    $taskVO->setStory($row[story]);
    if (strtolower($row[telework]) == "t")
        $taskVO->setTelework(True);
    elseif (strtolower($row[telework]) == "f")
        $taskVO->setTelework(False);
    $taskVO->setText($row[text]);
    $taskVO->setTtype($row[ttype]);
    $taskVO->setPhase($row[phase]);
    $taskVO->setUserId($row[usrid]);
    $taskVO->setProjectId($row[projectid]);
    $taskVO->setCustomerId($row[customerid]);
    $taskVO->setTaskStoryId($row[task_storyid]);

    return $taskVO;
    }

    /** Task retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Task table with the id <var>$taskId</var> and creates a {@link TaskVO} with its data.
     *
     * @param int $taskId the id of the row we want to retrieve.
     * @return TaskVO a value object {@link TaskVO} with its properties set to the values from the row.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($taskId) {
        if (!is_numeric($taskId))
        throw new SQLIncorrectTypeException($taskId);
        $sql = "SELECT * FROM task WHERE id=".$taskId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Tasks retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from Task table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $userId the id of the User whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM task WHERE usrid=".$userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Tasks User Id checker for PostgreSQL.
     *
     * This function retrieves the row from Task table with the id <var>$taskId</var> and checks it's User id
     * vs. the passed one, <var>$userId</var>.
     *
     * @param int $taskId the id of the Task whose User Id we want to check.
     * @param int $userId the User Id we want to check.
     * @return bool a bool that indicates if the Id's are equal.
     * @throws {@link SQLQueryErrorException}
     */
    public function checkUserId($taskId, $userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
    if (!is_numeric($taskId))
        throw new SQLIncorrectTypeException($taskId);
        $sql = "SELECT (" . $userId . " = ( SELECT usrid FROM task WHERE id=" . $taskId . " )) AS result";

    $result = @pg_query($this->connect, $sql);
    if ($result == NULL) throw new SQLQueryErrorException(pg_last_error());

    $row = @pg_fetch_array($result);

    if (strtolower($row[result]) == "t")
        return true;
    else
        return false;
    }

    /** Tasks retriever by User id and date for PostgreSQL.
     *
     * This function retrieves the rows from Task table that are associated with the User with
     * the id <var>$userId</var> and for date <var>$date</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $userId the id of the User whose Tasks we want to retrieve.
     * @param DateTime $date the date whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserIdDate($userId, DateTime $date) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM task WHERE usrid=" . $userId . " AND _date=" . DBPostgres::formatDate($date) . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Task User id checker for PostgreSQL.
     *
     * This function retrieves the row from Task table with id <var>$taskId</var> and checks if it's User id
     * is the same as <var>$userId</var>.
     *
     * @param int $taskId the id of the Task we want to check.
     * @param int $userId the User id we want to compare.
     * @return bool a bool indicating if the User id is the same.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function checkTaskUserId($taskId, $userId) {
    if (!is_numeric($taskId))
        throw new SQLIncorrectTypeException($taskId);
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT " . $userId . " = (SELECT usrid FROM task WHERE id=" . $taskId . ") as same";

    $res = @pg_query($this->connect, $sql);
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

    $row = @pg_fetch_array($res);
    if (strtolower($row[same]) == "t")
        return true;

    return false;
    }

    /** Tasks retriever by Customer id for PostgreSQL.
     *
     * This function retrieves the rows from Task table that are associated with the Customer with
     * the id <var>$customerId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $customerId the id of the Customer whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getByCustomerId($customerId) {
    if (!is_numeric($customerId))
        throw new SQLIncorrectTypeException($customerId);
        $sql = "SELECT * FROM task WHERE customerid=".$customerId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Tasks retriever by Task Story id.
     *
     * This function retrieves the rows from Task table that are associated with the Task Story with
     * the id <var>$taskStoryId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $taskStoryId the id of the Task Story whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getByTaskStoryId($taskStoryId) {
    if (!is_numeric($taskStoryId))
        throw new SQLIncorrectTypeException($taskStoryId);
    $sql = "SELECT * FROM task WHERE task_storyid=".$taskStoryId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Tasks retriever by Project id for PostgreSQL.
     *
     * This function retrieves the rows from Task table that are associated with the Project with
     * the id <var>$projectId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getByProjectId($projectId) {
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
    $sql = "SELECT * FROM task WHERE projectid=".$projectId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Tasks retriever by Story id for PostgreSQL.
     *
     * This function retrieves the rows from Task table that are associated with the Story with
     * the id <var>$storyId</var> through its Task Stories and creates a {@link TaskVO} with data from each row.
     *
     * @param int $storyId the id of the Story whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getByStoryId($storyId) {
    if (!is_numeric($storyId))
        throw new SQLIncorrectTypeException($storyId);
    $sql = "SELECT task.* FROM (task JOIN task_story ON (task.task_storyid=task_story.id) ) JOIN story ON (task_story.storyid=story.id) WHERE story.id = " . $storyId;
    $result = $this->execute($sql);
    return $result;
    }

    /** Tasks retriever for PostgreSQL.
     *
     * This function retrieves all rows from Task table and creates a {@link TaskVO} with data from each row.
     *
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM task ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Tasks report generator for PostgreSQL.
     *
     * This function generates a report of the hours users have worked in Tasks related to an element <var>$reportObject</var>,
     * which may be a {@link UserVO}, {@link TaskVO} or {@link CustomerVO}. Two optional dates can also be passed, <var>$initDate</var>
     * and <var>$endDate</var>, to limit the dates of the tasks retrieved (if they are not passed, it returns the result for tasks of any date),
     * and two group fields, <var>$groupField1</var> and <var>$groupField2</var>, that only are used for making groups with the
     * results if they are passed.
     *
     * @param mixed $reportObject the object whose related Tasks we want to use for computing the worked hours.
     * @param DateTime $initDate the optional DateTime object that represents the beginning of the date interval.
     * @param DateTime $endDate the optional DateTime object that represents the end of the date interval (included).
     * @param string $groupField1 the optional first field for grouping the data (valid values are stored in {@link $groupFields}).
     * @param string $groupField2 the optional second field for grouping the data (valid values are stored in {@link $groupFields}).
     * @return array an array with the resulting rows of computing the extra hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields if they were passed).
     * @todo write examples of usage and result.
     * @throws {@link TaskReportInvalidParameterException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getTaskReport($reportObject, DateTime $initDate = NULL, DateTime $endDate = NULL, $groupField1 = NULL, $groupField2 = NULL) {

    $sql = "SELECT ";

    if (!is_null($this->groupFields[$groupField1]))
    {
        $sql = $sql . $this->groupFields[$groupField1] . ", ";
        if (!is_null($this->groupFields[$groupField2]))
            $sql = $sql . $this->groupFields[$groupField2] . ", ";
        elseif (!is_null($groupField2))
            throw new TaskReportInvalidParameterException($groupField2);
    }
    elseif (!is_null($groupField1))
        throw new TaskReportInvalidParameterException($groupField1);

    $sql = $sql . "SUM( _end - init ) / 60.0 AS add_hours FROM task ";

    if ($reportObject instanceof UserVO)
    {
        $sql = $sql . "WHERE usrid=" . $reportObject->getId() . " ";
    }
    elseif ($reportObject instanceof CustomerVO)
    {
        $sql = $sql . "WHERE customerid=" . $reportObject->getId() . " ";
    }
    elseif ($reportObject instanceof ProjectVO)
    {
        $sql = $sql . "WHERE projectid=" . $reportObject->getId() . " ";
    }
    else throw new TaskReportInvalidParameterException($reportObject);

    if (!is_null($initDate) && !is_null($endDate))
    {
        $sql = $sql . "AND _date >= " . DBPostgres::formatDate($initDate) . " AND _date <= " . DBPostgres::formatDate($endDate) . " ";
    }

    if (!is_null($this->groupFields[$groupField1]))
    {
        $sql = $sql . "GROUP BY " . $this->groupFields[$groupField1];
        if (!is_null($this->groupFields[$groupField2]))
            $sql = $sql . ", " . $this->groupFields[$groupField2];

        $sql = $sql . " ORDER BY " . $this->groupFields[$groupField1];
        if (!is_null($this->groupFields[$groupField2]))
            $sql = $sql . ", " . $this->groupFields[$groupField2];

    }

    $res = @pg_query($this->connect, $sql);

    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        if(pg_num_rows($res) > 0) {
            for($i = 0; $i < pg_num_rows($res); $i++)
        {
                $rows[$i] = @pg_fetch_array($res);
        }
        }

    return $rows;

    }

    /** Tasks global report generator for PostgreSQL.
     *
     * This function generates a report of the hours users have worked in all Tasks. Two optional dates can also be passed, <var>$initDate</var>
     * and <var>$endDate</var>, to limit the dates of the tasks retrieved (if they are not passed, it returns the result for tasks of any date),
     * and two group fields, <var>$groupField1</var> and <var>$groupField2</var>, that only are used for making groups with the
     * results if they are passed. This function works very likely {@link getTaskReport()}, but for all tasks and grouping the results by users.
     *
     * @param DateTime $initDate the optional DateTime object that represents the beginning of the date interval.
     * @param DateTime $endDate the optional DateTime object that represents the end of the date interval (included).
     * @param string $groupField1 the optional first field for grouping the data (valid values are stored in {@link $groupFields}).
     * @param string $groupField2 the optional second field for grouping the data (valid values are stored in {@link $groupFields}).
     * @return array an array with the resulting rows of computing the extra hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields, <i>usrid</i> and others if they were passed).
     * @todo write examples of usage and result.
     * @throws {@link TaskReportInvalidParameterException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getGlobalTaskReport(DateTime $initDate = NULL, DateTime $endDate = NULL, $groupField1 = NULL, $groupField2 = NULL) {

    $sql = "SELECT ";

    if (!is_null($this->groupFields[$groupField1]))
    {
        $sql = $sql . $this->groupFields[$groupField1] . ", ";
        if (!is_null($this->groupFields[$groupField2]))
            $sql = $sql . $this->groupFields[$groupField2] . ", ";
        elseif (!is_null($groupField2))
            throw new TaskReportInvalidParameterException($groupField2);
    }
    elseif (!is_null($groupField1))
        throw new TaskReportInvalidParameterException($groupField1);

    $sql = $sql . $this->groupFields['USER']. ", SUM( _end - init ) / 60.0 AS add_hours FROM task ";

    if (!is_null($initDate) && !is_null($endDate))
    {
        $sql = $sql . "WHERE _date >= " . DBPostgres::formatDate($initDate) . " AND _date <= " . DBPostgres::formatDate($endDate) . " ";
    }

    $sql = $sql . "GROUP BY " . $this->groupFields['USER'];

    if (!is_null($this->groupFields[$groupField1]))
    {
        $sql = $sql . ", " . $this->groupFields[$groupField1];
        if (!is_null($this->groupFields[$groupField2]))
            $sql = $sql . ", " . $this->groupFields[$groupField2];
    }

    $sql = $sql . " ORDER BY " . $this->groupFields['USER'];

    if (!is_null($this->groupFields[$groupField1]))
    {
        $sql = $sql . ", " . $this->groupFields[$groupField1];
        if (!is_null($this->groupFields[$groupField2]))
            $sql = $sql . ", " . $this->groupFields[$groupField2];

    }

    $res = @pg_query($this->connect, $sql);

    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        if(pg_num_rows($res) > 0) {
            for($i = 0; $i < pg_num_rows($res); $i++)
        {
                $rows[$i] = @pg_fetch_array($res);
        }
        }

    return $rows;

    }

    /** Vacations report generator for PostgreSQL.
     *
     * This function generates a report of the vacations hours a user {@link UserVO} has spent as for today. Two optional DateTime parameters can be passed,
     * <var>$initDate</var> and <var>$endDate</var>, to limit the dates of the vacation hours retrieved.
     *
     * @param UserVO $userVO the user whose vacation hours we want to retrieve.
     * @param DateTime $initDate the optional DateTime object that represents the beginning of the date interval.
     * @param DateTime $endDate the optional DateTime object that represents the end of the date interval (included).
     * @return array an associative array with the user id (<i>usrid</i>) and the vacations hours he/she has spent (<i>add_hours</i>).
     * @throws {@link SQLQueryErrorException}
     */
    public function getVacations(UserVO $userVO, DateTime $initDate = NULL, DateTime $endDate = NULL) {

    $sql = "SELECT * FROM project WHERE description='" . ConfigurationParametersManager::getParameter('VACATIONS_PROJECT') . "'";

    $res = pg_query($this->connect, $sql);
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

    $resultAux = @pg_fetch_array($res);

    $vacId = $resultAux['id'];

    $sql = "SELECT usrid, SUM(_end-init)/60.0 AS add_hours FROM task WHERE projectid=" . $vacId ." AND usrid=" . $userVO->getId();

    if (!is_null($initDate))
    {
        $sql = $sql . " AND _date >=" . DBPostgres::formatDate($initDate);
        if (!is_null($endDate))
            $sql = $sql . " AND _date <=" . DBPostgres::formatDate($endDate);
    }

    $sql = $sql . " GROUP BY usrid";

    $res = pg_query($this->connect, $sql);
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

    for($i = 0; $i < pg_num_rows($res); $i++)
        {
        $result[$i] = @pg_fetch_array($res);
        }

    return $result[0];

    }

    /** Task partial updater for PostgreSQL.
     *
     * This function updates only some fields of the data of a Task by its {@link TaskVO}, reading
     * the flags on the associative array <var>$update</var>.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to update on database.
     * @param array $update an array with flags for updating or not the different fields.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function partialUpdate(TaskVO $taskVO, $update) {
        $affectedRows = 0;

        if($taskVO->getId() != "") {
            $currTaskVO = $this->getById($taskVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currTaskVO) > 0) {

        $sql = "UPDATE task SET ";

        if ($update[date])
        $sql = $sql . "_date=" . DBPostgres::formatDate($taskVO->getDate()) . ", ";

        if ($update[init])
        $sql = $sql . "init=" . DBPostgres::checkNull($taskVO->getInit()) . ", ";

        if ($update[end])
        $sql = $sql . "_end=" . DBPostgres::checkNull($taskVO->getEnd()) . ", ";

        if ($update[story])
        $sql = $sql . "story=" . DBPostgres::checkStringNull($taskVO->getStory()) . ", ";

        if ($update[telework])
        $sql = $sql . "telework=" . DBPostgres::boolToString($taskVO->getTelework()) . ", ";

        if ($update[text])
        $sql = $sql . "text=" . DBPostgres::checkStringNull($taskVO->getText()) . ", ";

        if ($update[ttype])
        $sql = $sql . "ttype=" . DBPostgres::checkStringNull($taskVO->getTtype()) . ", ";

        if ($update[phase])
        $sql = $sql . "phase=" . DBPostgres::checkStringNull($taskVO->getPhase()) . ", ";

        if ($update[userId])
        $sql = $sql . "usrid=" . DBPostgres::checkNull($taskVO->getUserId()) . ", ";

        if ($update[projectId])
        $sql = $sql . "projectid=" . DBPostgres::checkNull($taskVO->getProjectId()) . ", ";

        if ($update[customerId])
        $sql = $sql . "customerid=" . DBPostgres::checkNull($taskVO->getCustomerId()) . ", ";

        if ($update[taskStoryId])
        $sql = $sql . "task_storyid=" . DBPostgres::checkNull($taskVO->getTaskStoryId());

        if (strlen($sql) == strlen("UPDATE task SET "))
        return NULL;

        $last = strrpos($sql, ",");

        if ($last == (strlen($sql) - 2))
        $sql = substr($sql, 0, -2);

        $sql = $sql . " WHERE id=".$taskVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Task updater for PostgreSQL.
     *
     * This function updates the data of a Task by its {@link TaskVO}.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(TaskVO $taskVO) {
        $affectedRows = 0;

        if($taskVO->getId() != "") {
            $currTaskVO = $this->getById($taskVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currTaskVO) > 0) {

            $sql = "UPDATE task SET _date=" . DBPostgres::formatDate($taskVO->getDate()) . ", init=" . DBPostgres::checkNull($taskVO->getInit()) . ", _end=" . DBPostgres::checkNull($taskVO->getEnd()) . ", story=" . DBPostgres::checkStringNull($taskVO->getStory()) . ", telework=" . DBPostgres::boolToString($taskVO->getTelework()) . ", text=" . DBPostgres::checkStringNull($taskVO->getText()) . ", ttype=" . DBPostgres::checkStringNull($taskVO->getTtype()) . ", phase=" . DBPostgres::checkStringNull($taskVO->getPhase()) . ", usrid=" . DBPostgres::checkNull($taskVO->getUserId()) . ", projectid=" . DBPostgres::checkNull($taskVO->getProjectId()) . ", customerid=" . DBPostgres::checkNull($taskVO->getCustomerId()) . ", task_storyid=" . DBPostgres::checkNull($taskVO->getTaskStoryId()) . " WHERE id=".$taskVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_task_usr_time"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);

        }

        return $affectedRows;
    }

    /** Task creator for PostgreSQL.
     *
     * This function creates a new row for a Task by its {@link TaskVO}.
     * The internal id of <var>$taskVO</var> will be set after its creation.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(TaskVO $taskVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO task (_date, init, _end, story, telework, text, ttype, phase, usrid, projectid, customerid, task_storyid) VALUES(" . DBPostgres::formatDate($taskVO->getDate()) . ", " . DBPostgres::checkNull($taskVO->getInit()) . ", " . DBPostgres::checkNull($taskVO->getEnd()) . ", " . DBPostgres::checkStringNull($taskVO->getStory()) . ", " . DBPostgres::boolToString($taskVO->getTelework()) . ", " . DBPostgres::checkStringNull($taskVO->getText()) . ", " . DBPostgres::checkStringNull($taskVO->getTtype()) . ", " . DBPostgres::checkStringNull($taskVO->getPhase()) . ", " . DBPostgres::checkNull($taskVO->getUserId()) . ", " . DBPostgres::checkNull($taskVO->getProjectId()) . ", " . DBPostgres::checkNull($taskVO->getCustomerId()). ", " . DBPostgres::checkNull($taskVO->getTaskStoryId()) .")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_task_usr_time"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $taskVO->setId(DBPostgres::getId($this->connect, "task_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Task deleter for PostgreSQL.
     *
     * This function deletes the data of a Task by its {@link TaskVO}.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(TaskVO $taskVO) {
        $affectedRows = 0;

        // Check for a task ID.
        if($taskVO->getId() >= 0) {
            $currTaskVO = $this->getById($taskVO->getId());
        }

        // Otherwise delete a task.
        if(sizeof($currTaskVO) > 0) {
            $sql = "DELETE FROM task WHERE id=".$taskVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




//Uncomment these lines in order to do a simple test of the Dao


$dao = new PostgreSQLTaskDAO();

/*// We create a new task

$task = new TaskVO();

$task->setDate(date_create('2009-01-05'));
$task->setInit(1);
$task->setEnd(2);
$task->setStory("Very well");
$task->setTelework("FALSE");
$task->setText("Old text");
$task->setTtype("Ttype 1");
$task->setPhase("Initial");
$task->setUserId(1);
$task->setProjectId(1);
$task->setCustomerId(1);

$dao->create($task);

$user = new UserVO();

$user->setId(1);

$res = $dao->getTaskReport($user);

foreach($res as $row)
    print $row[add_hours] . "\n";

$res = $dao->getTaskReport($user, date_create('2009-03-05'), date_create('2009-03-27'));

foreach($res as $row)
    print $row[add_hours] . "\n";

$res = $dao->getTaskReport($user, date_create('2009-03-05'), date_create('2009-03-27'), "TYPE");

foreach($res as $row)
    print $row[add_hours] . "\n";

/*$dao->getTaskReport($user, date_create('2009-03-05'), date_create('2009-03-27'), "ttype", "story");

$project = new ProjectVO();

$project->setId(12);

$dao->getTaskReport($project);

$dao->getTaskReport($project, date_create('2009-03-05'));

$dao->getTaskReport($project, date_create('2009-03-05'), date_create('2009-03-27'));

$dao->getTaskReport($project, date_create('2009-03-05'), date_create('2009-03-27'), "projectid");

$dao->getTaskReport($project, date_create('2009-03-05'), date_create('2009-03-27'), "usrid", "story");

$customer = new CustomerVO();

$customer->setId(12);

$dao->getTaskReport($customer);

$dao->getTaskReport($customer, date_create('2009-03-05'));

$dao->getTaskReport($customer, date_create('2009-03-05'), date_create('2009-03-27'));

$dao->getTaskReport($customer, date_create('2009-03-05'), date_create('2009-03-27'), "customerid");

$dao->getTaskReport($customer, date_create('2009-03-05'), date_create('2009-03-27'), "ttype", "usrid");

/*$dao->create($task);

print ("New task Id is ". $task->getId() ."\n");

// We search for the old text

$task = $dao->getById($task->getId());

print ("Old text found is ". $task->getText() ."\n");

// We update the task with a different text

$task->setText("New text");

$dao->update($task);

// We search for the new text

$task = $dao->getById($task->getId());

print ("New text found is ". $task->getText() ."\n");

// We delete the new task

$dao->delete($task);

$task = $dao->getById($task->getId());

$task->setStory("lolololol");

$flags[story] = true;

$dao->partialUpdate($task, $flags);*/