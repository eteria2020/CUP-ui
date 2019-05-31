<?php
 /**
 * suremdm.php, phpDocumentor Quickstart
 *
 * This file implements the use of the @SureMDM RESTful API
 * @author Massimo De Luisa <massimodeluisa@me.com>
 * @version 1.0
 * @package SureMDM
 *
 * SureMDM RESTful API Reference:
 * @link: http://mars.42gears.com/support/inout/mdm/api.html
 *
 * REQUIRE Travis Dent php-restclient:
 * @link: https://github.com/tcdent/php-restclient
 */

/* ERROR LOG */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Rest Client Library
require 'restclient.php';

/**
 * Documents the class following
 * @package SureMDM
 */
class SureMDM {
    public  $userName;      //= "massimo1";
    public  $userPassword;  //= "massimo1";
    public  $apiKey;        //= "6878DA8A-86F1-4397-8938-8D914D3CF13F";
    public  $baseURL          = "https://suremdm.42gears.com/api/";
    private $restClient;

    public function __construct($userName,$userPassword,$apiKey) {
        $this->userName      = $userName;
        $this->userPassword  = $userPassword;
        $this->apiKey        = $apiKey;
        $this->restClient    = new RestClient;

        $this->restClient->set_option("username",$this->userName);
        $this->restClient->set_option("password",$this->userPassword);
        $this->restClient->set_option("base_url",$this->baseURL);
        $this->restClient->set_option("headers", ["ApiKey" => $this->apiKey]);
    }

    /**
    *   Ref. 1  [Customer]
    *
    *   Gets the customer details of currently logged in user.
    *   If the user is SureAdmin, then details of all the customers are returned.
    *
    *   @return  JSON Object
    */
    public function getCustomer(){

        $result = $this->restClient->get("customer");
        return $result->decode_response();                                                   // JSON Object
    }

    /**
    *   Ref. 2  [Message]
    *
    *   Gets the list of messages accessible to the user.
    *   By default, it fetches only unread messages.
    *
    *   @param   $fetchRead    [Bool] true or false
    *   @return  JSON Object
    */
    public function getUserMessage($fetchRead = false){

        $result = $this->restClient->get("message",["FetchRead" => $fetchRead]);
        return $result->decode_response();                                                   // JSON Object
    }

    /**
    *   Ref. 2  [Message]
    *
    *   Gets a particular message and marks it as read.
    *
    *   @param   $messageId    The ID of the message
    *   @return  JSON Object
    */
    public function getMessageByID($messageId = null){

        $result = $this->restClient->get("message/$messageId");

        return $result->decode_response();                                                   // JSON Object
    }

    /**
    *   Ref. 3  [Group]
    *
    *   Gets the list of groups accessible to the user.
    *
    *   @return  JSON Object
    */
    public function getGroups(){

        $result = $this->restClient->get("group");

        return $result->decode_response();                                                   // JSON Object
    }

    /**
    *   Ref. 3  [Group]
    *
    *   Gets the list of groups accessible to the user.
    *
    *   @return  JSON Object
    */
    public function getGroupByID($groupId = null){

        $result = $this->restClient->get("group",["GroupId" => $groupId]);

        return $result->decode_response();                                                   // JSON Object
    }


    /**
    *   Ref. 4  [Device]
    *
    *   Gets the list of devices
    *
    *   @param   $groupID    The Group of devices
    *                        (NULL) by default, it fetches devices only in the home group
    *   @return  JSON Object
    */
    public function getDevicesByGroup($groupID = ""){

        $result = $this->restClient->get("device",["GroupId"=>$groupID]);
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 4  [Device]
    *
    *   Gets the list of devices
    *   By default, it fetches devices only in the home group.
    *   For a subgroup devices, specify the $groupID
    *
    *   @param      $deviceName     The device name
    *   @param      $groupID        The Group of the device
    *   @return     JSON Object
    */
    public function getDeviceByName($deviceName = null,$groupID = null){

        $result = $this->restClient->get("device", ["DeviceName"=>$deviceName,"GroupId"=>$groupID]);
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 4  [Device]
    *
    *   Gets the list of devices
    *   By default, it fetches devices only in the home group.
    *   For a subgroup devices, specify the $groupID
    *
    *   @param      $macAddress    The device MAC Address
    *   @param      $groupID        The Group of the device
    *   @return     JSON Object
    */
    public function getDevicesByMAC($macAddress = null,$groupID = null){

        $result = $this->restClient->get("device", ["MacAddress"=>$macAddress,"GroupId"=>$groupID]);
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 4  [Device]
    *
    *   Gets the list of devices
    *   By default, it fetches devices only in the home group.
    *   For a subgroup devices, specify the $groupID
    *
    *   @param      $deviceName     The device name
    *   @param      $macAddress     The device MAC Address
    *   @param      $groupID        The Group of the device
    *   @return     JSON Object
    */
    public function getDeviceByNameAndMAC($deviceName = null,$macAddress = null,$groupID = null){

        $result = $this->restClient->get("device", ["DeviceName" => $deviceName,"MacAddress"=>$macAddress,"GroupId"=>$groupID]);
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 4  [Device]
    *
    *   Gets the details of a particular device.
    *
    *   @param      $deviceID       The ID of the device
    *   @return     JSON Object
    */
    public function getDeviceByID($deviceID = null){

        $result = $this->restClient->get("device/$deviceID");
        return $result->decode_response();                                                  // JSON Object
    }


    /**
    *   Ref. 5  [Version]
    *
    *   Gets current SureMDM API version
    *
    *   @return  JSON Object
    */
    public function getVersion(){

        $result = $this->restClient->get("version");
        return $result->decode_response();                                                  // JSON Object

    }

    /**
    *   Ref. 6  [API Key]
    *
    *   Fetches the API Key for On Premise customers only
    *   If API Key is not present, new API Key is generated. For suremdm.42gears.com,
    *   it returns 404 error.
    *
    *   @return  JSON Object
    */
    public function getAPIKey(){

        $result = $this->restClient->get("apikey");
        return $result->decode_response();                                                  // JSON Object

    }

    /**
    *   Ref. 7  [Location]
    *
    *   Gets latest location of device.
    *
    *   @param      $deviceID   The device ID (CSV for more devices: "DevID1,DevID2...")
    *   @return     JSON Object
    */
    public function getLocationByID($deviceID = null){

        $result = $this->restClient->get("location",["DeviceID"=>$deviceID]);
        return $result->decode_response();                                                  // JSON Object

    }

    /**
    *   Ref. 7  [Location]
    *
    *   Gets location of device in time frame
    *
    *   @param      $deviceID   The device ID (CSV for more devices: "DevID1,DevID2...")
    *   @param      $fromTime   The inizial frame time
    *                           (format "m/d/yyyy/ h:mm:ss AM" == PHP Format 'n/j/Y g:i:s A')
    *   @param      $toTime     The end frame time (like $fromTime).
    *                           The default value is the execution datetime.
    *   @return     JSON Object (Maximum - 1000 results)
    */
    public function getLocationByIDTimeFrame($deviceID = null,$fromTime = null, $toTime = null){
        $toTime = $toTime == null ? date('n/j/Y g:i:s A') : $toTime;

        $result = $this->restClient->get("location",["DeviceID"=>$deviceID,"FromTime" => $fromTime, "ToTime" => $toTime]);
        return $result->decode_response();                                                  // JSON Object

    }

    /**
    *   Ref. 7  [Location]
    *
    *   Gets location of all devices in time frame
    *
    *   @param      $fromTime   The inizial frame time
    *                           (format "m/d/yyyy/ h:mm:ss AM" == PHP Format 'n/j/Y g:i:s A')
    *   @param      $toTime     The end frame time (like $fromTime).
    *                           The default value is the execution datetime.
    *   @return     JSON Object (Maximum - 1000 results)
    */
    public function getLocationByTimeFrame($fromTime = null, $toTime = null){
        $toTime = $toTime == null ? date('n/j/Y g:i:s A') : $toTime;

        $result = $this->restClient->get("location",["FromTime" => $fromTime, "ToTime" => $toTime]);
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 8  [Location Count]
    *
    *   Gets total location count reported by each device of this customer
    *
    *   @return     JSON Object
    */
    public function getLocationCount(){

        $result = $this->restClient->get("locationcount");
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 8  [Location Count]
    *
    *   Gets total location count reported by device
    *
    *   @param      $deviceId       The device ID (CSV for more devices: "DevID1,DevID2...")
    *   @return     JSON Object
    */
    public function getLocationCountByID($deviceID = null){

        $result = $this->restClient->get("locationcount",["DeviceID"=>$deviceID]);
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 8  [Location Count]
    *
    *   Gets location reported by device in time frame
    *
    *   @param      $deviceId       The device ID (CSV for more devices: "DevID1,DevID2...")
    *   @param      $fromTime       The inizial frame time
    *                               (format "m/d/yyyy/ h:mm:ss AM" == PHP Format 'n/j/Y g:i:s A')
    *   @param      $toTime         The end frame time (like $fromTime).
    *                               The default value is the execution datetime.
    *   @return     JSON Object
    */
    public function getLocationCountByIDTimeFrame($deviceID = null,$fromTime = null, $toTime = null){
        $toTime = $toTime == null ? date('n/j/Y g:i:s A') : $toTime;

        $result = $this->restClient->get("locationcount",["DeviceID"=>$deviceID,"FromTime" => $fromTime, "ToTime" => $toTime]);
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 8  [Location Count]
    *
    *   Gets total location count reported by each device under this customer
    *   for given time framee
    *
    *   @param      $fromTime       The inizial frame time
    *                               (format "m/d/yyyy/ h:mm:ss AM" == PHP Format 'n/j/Y g:i:s A')
    *   @param      $toTime         The end frame time (like $fromTime).
    *                               The default value is the execution datetime.
    *   @return     JSON Object
    */
    public function getLocationCountTimeFrame($fromTime = null, $toTime = null){

        $result = $this->restClient->get("locationcount",["FromTime" => $fromTime, "ToTime" => $toTime]);
        return $result->decode_response();                                                  // JSON Object
    }


    /**
    *   Ref. 9  [Device Approval]
    *
    *   Approves all unapproved devices
    *
    *   @return     JSON Object
    */
    public function approvesDevices(){

        $result = $this->restClient->get("deviceapproval");
        return $result->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 11  [Device Name]
    *
    *   Gets the device name
    *
    *   @param      $deviceId       The device ID
    *   @return     JSON Object
    */
    public function getDeviceNameByID($deviceID = null){

        $result = $this->restClient->get("devicename",["DeviceID"=>$deviceID]);
        return $result->decode_response();                                                  // JSON Object
    }


    /**
    *   Ref. 12  [Job]
    *
    *   Gets all jobs for the Customer
    *
    *   @param      $folderid   The group of devices
    *   @return     JSON Object
    */
    public function getJobsByFolderID($folderID = "null"){

        $result = $this->restClient->get("job",["folderid"=>$folderID]);
        return $result->decode_response();                                                  // JSON Object
    }



    /**
    *   Ref. 14  [Job Folder]
    *
    *   Gets details of all jobs for the customer (in the specified folder)
    *
    *   @param      $folderID       The folder containing the job/s
    *   @return     JSON Object
    */
    public function getJobsFolders($folderID = "null"){

        $result = $this->restClient->get("jobfolder",["folderid"=>$folderID]);
        return $result->decode_response();                                                  // JSON Object
    }

    #MARK : POST FUNCTIONS

    /**
    *   Ref. 13  [Job Assignment]
    *
    *   Applies a job to list of devices
    *
    *   @param      $folderid   The group of devices
    *   @return     JSON Object
    */
    public function assignJob($jobId = null, $deviceIds = null){
        //$post = "JobId=".urlencode($jobId)."&DeviceIds=$deviceIds";
        $post =  ["JobId" => $jobId, "DeviceIds" => $deviceIds];
        $postEncoded = json_encode($post);

        /*$result = $this->restClient->post(
            "jobassignment",$postEncoded,
            ["Content-Type" => "application/json"]
        );*/

        $jstr = json_encode(array("JobID" => $jobId,"DeviceIDs" => array($deviceIds)));

        $result = $this->restClient->post("jobassignment",
            $jstr,
            array('Content-Type' => "application/json","Content-Length" => strlen($jstr)));


        //$result = $this->restClient->post("jobassignment",["JobId"=>$jobId,"DeviceIds" => "[\"$deviceIds\"]"]);
        return $result->decode_response();                                                  // JSON Object
    }


    /**
    *   Ref. 15  [Group]
    *
    *   Creates a group for the customer
    *
    *   @param      $groupName   The new Group name
    *   @param      $groupId     The new Group ID
    *   @return     JSON Object
    */
    public function createGroup($groupName = null, $groupId = null){

        $result = $this->restClient->post("group",["GroupName"=>$groupName,"GroupId" => $groupId]);
        return $result;//->decode_response();                                                  // JSON Object
    }

    /**
    *   Ref. 16  [Dynamic Job]
    *
    *   Creates a Dynamic job and applies it to the device
    *
    *   @param      $jobId          The ID job
    *   @param      $jobType        Possible value (Reboot)
    *   @return     JSON Object
    */
    public function assignDynamicJob($deviceID = null,$jobType = "null"){

        $result = $this->restClient->post("dynamicjob",["DeviceID"=>$deviceID,"JobType" => $jobType]);
        return $result->decode_response();                                                  // JSON Object
    }


}

?>
