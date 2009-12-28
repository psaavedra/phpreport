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


/** File for PostgreSQLProjectDAO
 *
 *  This file just contains {@link PostgreSQLProjectDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge L�pez Fern�ndez <jlopez@igalia.com>
 */

include_once('phpreport/util/SQLIncorrectTypeException.php');
include_once('phpreport/util/DBPostgres.php');
include_once('phpreport/model/vo/ProjectVO.php');
include_once('phpreport/model/dao/ProjectDAO/ProjectDAO.php');
include_once('phpreport/model/dao/ProjectUserDAO/PostgreSQLProjectUserDAO.php');
include_once('phpreport/model/dao/WorksDAO/PostgreSQLWorksDAO.php');
include_once('phpreport/model/dao/TaskDAO/PostgreSQLTaskDAO.php');
include_once('phpreport/model/dao/RequestsDAO/PostgreSQLRequestsDAO.php');

/** DAO for Projects in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link ProjectDAO}.
 *
 * @see ProjectDAO, ProjectVO
 */
class PostgreSQLProjectDAO extends ProjectDAO {

    /** Project DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link ProjectDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see ProjectDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Project value object constructor for PostgreSQL.
     *
     * This function creates a new {@link ProjectVO} with data retrieved from database.
     *
     * @param array $row an array with the Project values from a row.
     * @return ProjectVO an {@link ProjectVO} with its properties set to the values from <var>$row</var>.
     * @see ProjectVO
     */
    protected function setValues($row)
    {

    $projectVO = new ProjectVO();

        $projectVO->setId($row[id]);
    if (strtolower($row[activation]) == "t")
            $projectVO->setActivation(True);
    else
        $projectVO->setActivation(False);
    if (is_null($row[init]))
            $projectVO->setInit(NULL);
    else
        $projectVO->setInit(date_create($row[init]));
    if (is_null($row[_end]))
            $projectVO->setEnd(NULL);
    else
        $projectVO->setEnd(date_create($row[_end]));
        $projectVO->setInvoice($row[invoice]);
        $projectVO->setEstHours($row[est_hours]);
    $projectVO->setAreaId($row[areaid]);
        $projectVO->setType($row[type]);
        $projectVO->setDescription($row[description]);
        $projectVO->setMovedHours($row[moved_hours]);
    $projectVO->setSchedType($row[sched_type]);

    return $projectVO;
    }

    /** Project retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Project table with the id <var>$projectId</var> and creates a {@link ProjectVO} with its data.
     *
     * @param int $projectId the id of the row we want to retrieve.
     * @return ProjectVO a value object {@link ProjectVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($projectId) {
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT * FROM project WHERE id=".$projectId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Projects retriever by Area id for PostgreSQL.
     *
     * This function retrieves the rows from Project table that are associated with the Area with
     * the id <var>$areaId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $areaId the id of the Area whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByAreaId($areaId) {
        $sql = "SELECT * FROM project WHERE areaid=" . $areaId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Users retriever by Project id (relationship ProjectUser) for PostgreSQL.
     *
     * This function retrieves the rows from User table that are assigned through relationship ProjectUser to the Project with
     * the id <var>$projectId</var> and creates an {@link UserVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectUserDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getUsersProject($projectId) {

    $dao = DAOFactory::getProjectUserDAO();
    return $dao->getByProjectId($projectId);

    }

     /** ProjectUser relationship entry creator by Project id and User id for PostgreSQL.
     *
     * This function creates a new entry in the table ProjectUser (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project we want to relate to the User.
     * @param int $userId the id of the User we want to relate to the Project.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function addUserProject($projectId, $userId) {

    $dao = DAOFactory::getProjectUserDAO();
    return $dao->create($userId, $projectId);

    }

    /** ProjectUser relationship entry deleter by Project id and User id for PostgreSQL.
     *
     * This function deletes an entry in the table ProjectUser (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function removeUserProject($projectId, $userId) {

    $dao = DAOFactory::getProjectUserDAO();
    return $dao->delete($userId, $projectId);

    }

    /** Users retriever by Project id (relationship Works) for PostgreSQL.
     *
     * This function retrieves the rows from User table that are assigned through relationship Works to the Project with
     * the id <var>$projectId</var> and creates an {@link UserVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see WorksDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getUsersWorks($projectId) {

    $dao = DAOFactory::getWorksDAO();
    return $dao->getByProjectId($projectId);

    }

    /** Works relationship entry creator by Project id and User id for PostgreSQL.
     *
     * This function creates a new entry in the table Works (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project we want to relate to the User.
     * @param int $userId the id of the User we want to relate to the Project.
     * @return int the number of rows that have been affected (it should be 1).
     * @see WorksDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function addUserWorks($projectId, $userId) {

    $dao = DAOFactory::getWorksDAO();
    return $dao->create($userId, $projectId);

    }

    /** Works relationship entry deleter by Project id and User id for PostgreSQL.
     *
     * This function deletes an entry in the table Works (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see WorksDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function removeUserWorks($projectId, $userId) {

    $dao = DAOFactory::getWorksDAO();
    return $dao->delete($userId, $projectId);

    }

    /** Customers retriever by Project id for PostgreSQL.
     *
     * This function retrieves the rows from Customer table that are assigned through relationship Requests to the Project with
     * the id <var>$projectId</var> and creates a {@link CustomerVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Customers we want to retrieve.
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see RequestsDAO, CustomerDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getCustomers($projectId) {

    $dao = DAOFactory::getRequestsDAO();
    return $dao->getByProjectId($projectId);

    }

    /** Requests relationship entry creator by Project id and Customer id for PostgreSQL.
     *
     * This function creates a new entry in the table Requests (that represents that relationship between Projects and Customers)
     * with the Project id <var>$projectId</var> and the Customer id <var>$customerId</var>.
     *
     * @param int $projectId the id of the Project we want to relate to the Customer.
     * @param int $customerId the id of the Customer we want to relate to the Project.
     * @return int the number of rows that have been affected (it should be 1).
     * @see RequestsDAO, CustomerDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function addCustomer($projectId, $customerId) {

    $dao = DAOFactory::getRequestsDAO();
    return $dao->create($customerId, $projectId);

    }

    /** Requests relationship entry deleter by Project id and Customer id for PostgreSQL.
     *
     * This function deletes an entry in the table Requests (that represents that relationship between Projects and Customers)
     * with the Project id <var>$projectId</var> and the Customer id <var>$customerId</var>.
     *
     * @param int $projectId the id of the Project whose relation to the Customer we want to delete.
     * @param int $customerId the id of the Customer whose relation to the Project we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see RequestsDAO, CustomerDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function removeCustomer($projectId, $customerId) {

    $dao = DAOFactory::getRequestsDAO();
    return $dao->delete($customerId, $projectId);

    }

    /** Tasks retriever by Project id for PostgreSQL.
     *
     * This function retrieves the rows from Task table that are assigned to the Project with
     * the id <var>$projectId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see TaskDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getTasks($projectId) {

    $dao = DAOFactory::getTaskDAO();
    return $dao->getByProjectId($projectId);

    }

    /** Projects retriever for PostgreSQL.
     *
     * This function retrieves the rows from Project table, applying three optional conditions:
     * projects assigned to a specific Customer through Requests,
     * projects related with a User through ProjectUser
     * or projects with the Activation flag as True.
     *
     * @param int $customerId the id of the Customer whose Projects we want to retrieve.
     * @param string $userLogin login of the user we want to use as a filter.
     * @param bool $active parameter for obtaining only the active Projects (by default it returns all them).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByCustomerUserLogin($customerId = NULL, $userLogin = NULL, $active = False) {
    $customerCondition = "true";
    $userCondition = "true";
    $activeCondition = "true";

    if ($customerId)
        $customerCondition = "id IN (SELECT projectid FROM requests where customerid = ".
                         DBPostgres::checkNull($customerId) . ")";

    if ($userLogin)
        $userCondition = "id IN (SELECT projectid FROM project_usr LEFT JOIN usr ON usrid=id ".
                    "WHERE login=" . DBPostgres::checkStringNull($userLogin) . ") ";

    if ($active)
        $activeCondition = "activation='True'";

        $sql = "SELECT * FROM project".
            " WHERE ".$customerCondition." AND ".$userCondition." AND ".$activeCondition.
            " ORDER BY id ASC";

        return $this->execute($sql);
    }

    /** Projects retriever for PostgreSQL.
     *
     * This function retrieves all rows from Project table and creates a {@link ProjectVO} with data from each row.
     *
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll($active = False) {
        $sql = "SELECT * FROM project";
    if ($active)
        $sql = $sql . " WHERE activation='True'";
    $sql = $sql . " ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Project partial updater for PostgreSQL.
     *
     * This function updates only some fields of the data of a Project by its {@link ProjectVO}, reading
     * the flags on the associative array <var>$update</var>.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to update on database.
     * @param array $update an array with flags for updating or not the different fields.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function partialUpdate(ProjectVO $projectVO, $update) {
        $affectedRows = 0;

        if($projectVO->getId() != "") {
            $currProjectVO = $this->getById($projectVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currProjectVO) > 0) {

        $sql = "UPDATE project SET ";

        if ($update[activation])
        $sql = $sql . "activation=" . DBPostgres::boolToString($projectVO->getActivation()) . ", ";

        if ($update[init])
        $sql = $sql . "init=" . DBPostgres::formatDate($projectVO->getInit()) . ", ";

        if ($update[end])
        $sql = $sql . "_end=" . DBPostgres::formatDate($projectVO->getEnd()) . ", ";

        if ($update[invoice])
        $sql = $sql . "invoice=" . DBPostgres::checkNull($projectVO->getInvoice()) . ", ";

        if ($update[estHours])
        $sql = $sql . "est_hours=" . DBPostgres::checkNull($projectVO->getEstHours()) . ", ";

        if ($update[areaId])
        $sql = $sql . "areaid=" . DBPostgres::checkNull($projectVO->getAreaId()) . ", ";

        if ($update[description])
        $sql = $sql . "description=" . DBPostgres::checkStringNull($projectVO->getDescription()) . ", ";

        if ($update[type])
        $sql = $sql . "type=" . DBPostgres::checkStringNull($projectVO->getType()) . ", ";

        if ($update[movHours])
        $sql = $sql . "moved_hours=" . DBPostgres::checkNull($projectVO->getMovedHours()) . ", ";

        if ($update[schedType])
        $sql = $sql . "sched_type=" . DBPostgres::checkStringNull($projectVO->getSchedType());

        if (strlen($sql) == strlen("UPDATE project SET "))
        return NULL;

        $last = strrpos($sql, ",");

        if ($last == (strlen($sql) - 2))
        $sql = substr($sql, 0, -2);

        $sql = $sql . " WHERE id=".$projectVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Project updater for PostgreSQL.
     *
     * This function updates the data of a Project by its {@link ProjectVO}.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function update(ProjectVO $projectVO) {
        $affectedRows = 0;

        if($projectVO->getId() != "") {
            $currProjectVO = $this->getById($projectVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currProjectVO) > 0) {

            $sql = "UPDATE project SET activation=" . DBPostgres::boolToString($projectVO->getActivation()) . ", init=" . DBPostgres::formatDate($projectVO->getInit()) . ", _end=" . DBPostgres::formatDate($projectVO->getEnd()) . ", invoice=" . DBPostgres::checkNull($projectVO->getInvoice()) . ", est_hours=" . DBPostgres::checkNull($projectVO->getEstHours()) . ", areaid=" . DBPostgres::checkNull($projectVO->getAreaId()) . ", type=" . DBPostgres::checkStringNull($projectVO->getType()) . ", description=" . DBPostgres::checkStringNull($projectVO->getDescription()) . ", moved_hours=" . DBPostgres::checkNull($projectVO->getMovedHours()) . ", sched_type=" . DBPostgres::checkStringNull($projectVO->getSchedType()) . " WHERE id=".$projectVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Project creator for PostgreSQL.
     *
     * This function creates a new row for a Project by its {@link ProjectVO}.
     * The internal id of <var>$projectVO</var> will be set after its creation.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function create(ProjectVO $projectVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO project (activation, init, _end, invoice, est_hours, areaid, type, description, moved_hours, sched_type) VALUES(" . DBPostgres::boolToString($projectVO->getActivation()) . ", " . DBPostgres::formatDate($projectVO->getInit()) . ", " . DBPostgres::formatDate($projectVO->getEnd()) . ", " . DBPostgres::checkNull($projectVO->getInvoice()) . ", " . DBPostgres::checkNull($projectVO->getEstHours()) . ", " . DBPostgres::checkNull($projectVO->getAreaId()) . ", " . DBPostgres::checkStringNull($projectVO->getType()) . ", " . DBPostgres::checkStringNull($projectVO->getDescription()) . ", " . DBPostgres::checkNull($projectVO->getMovedHours()) . ", " . DBPostgres::checkStringNull($projectVO->getSchedType()) .")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $projectVO->setId(DBPostgres::getId($this->connect, "project_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Project deleter for PostgreSQL.
     *
     * This function deletes the data of a Project by its {@link ProjectVO}.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(ProjectVO $projectVO) {
        $affectedRows = 0;

        // Check for a user ID.
        if($projectVO->getId() >= 0) {
            $currProjectVO = $this->getById($projectVO->getId());
        }

        // If it exists, then delete.
        if(sizeof($currProjectVO) > 0) {
            $sql = "DELETE FROM project WHERE id=".$projectVO->getId();

            $res = pg_query($this->connect, $sql);
            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
                $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLProjectDAO();

$projs = $dao->getByCustomerUserLogin(10, 'jaragunde');

var_dump($projs);

// We retrieve all projects

$projs = $dao->getById(183);

var_dump($projs);

// We create a new project

$project = new ProjectVO();

$project->setInit(date_create('2000-03-12'));
$project->setInvoice(12.32);
$project->setEstHours(200.5);
$project->setAreaId(1);
$project->setDescription("First project");
$project->setType("Web design");
$project->setMovedHours(5.25);
$project->setSchedType("End date");


$dao->create($project);

print ("New project Id is ". $project->getId() ."\n");

// We search for the new Id

$project = $dao->getById($project->getId());

print ("New project Id found is ". $project->getId() ."\n");

// We update the project with a differente description

$project->setDescription("Our very first project");

$dao->update($project);

// We search for the new description

$project = $dao->getById($project->getId());

print ("New project description found is " . $project->getDescription() . "\n");

// We delete the new project

$dao->delete($project);*/