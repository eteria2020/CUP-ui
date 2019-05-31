<?php
 /* ERROR LOG */
error_reporting(E_ALL);
ini_set('display_errors', 1);


require "suremdm.php";

$mdm = new SureMDM("massimo1","massimo1","6878DA8A-86F1-4397-8938-8D914D3CF13F");



// Get Groups

//print_r($mdm->getGroups());



// Get Omniaevo Group Info

//print_r($mdm->getGroupByID("063e29e7-9d1c-4af7-afcf-4ab9e5da1707"));



// Get Omniaevo group devices

//print_r($mdm->getDevicesByGroup("063e29e7-9d1c-4af7-afcf-4ab9e5da1707"));



// Get Home devices

//print_r($mdm->getDevicesByGroup());



echo "Device Test: <br>";


//print_r($mdm->getVersion());

// Get Device DEMO2

print_r($mdm->getDeviceByID("93703fb2-6246-4fcd-b6c9-0d38a193418e"));



// Get Device DEMO2

//print_r($mdm->getDeviceByName("DEMO2 - 352850063241857","063e29e7-9d1c-4af7-afcf-4ab9e5da1707"));



// Get Device DEMO2 Last Location

//print_r($mdm->getLocationByID("93703fb2-6246-4fcd-b6c9-0d38a193418e"));



// Get Device DEMO2 Locations in a time frame

//print_r($mdm->getLocationByIDTimeFrame("93703fb2-6246-4fcd-b6c9-0d38a193418e","11/10/2015 2:52:11 PM","11/17/2015 2:52:11 PM"));



// Get Device DEMO2 and DEMO  Locations in a time frame

//print_r($mdm->getLocationByIDTimeFrame("93703fb2-6246-4fcd-b6c9-0d38a193418e,380a8b3c-af7e-4d58-8224-4644e75480a6","11/10/2015 2:52:11 PM","11/17/2015 2:52:11 PM"));



// Get Device DEMO2 Name

//print_r($mdm->getDeviceNameByID("93703fb2-6246-4fcd-b6c9-0d38a193418e"));



//print_r($mdm->getJobsFolders());



echo "<br><br>JOB<br><br>";



// Reboot the device

//print_r($mdm->assignDynamicJob("93703fb2-6246-4fcd-b6c9-0d38a193418e","Reboot"));


// Assign job "Location Tracking (5 minutes)" to DEMO 2
//print_r($mdm->assignJob("9460e2f7-af84-4539-b9b7-cff81433c325","93703fb2-6246-4fcd-b6c9-0d38a193418e"));

// Assign job "Location Tracking (30 minutes)" to DEMO 2
//print_r($mdm->assignJob("76c3c9fd-6f07-43b9-8944-ae776623c294","93703fb2-6246-4fcd-b6c9-0d38a193418e"));


// Assign job "Uninstall OBC [Platform]" to DEMO 2
//print_r($mdm->assignJob("440fded1-abd8-4bf9-8a23-4945de3752ab","93703fb2-6246-4fcd-b6c9-0d38a193418e"));

// Assign job "OBC 1.100 + Reboot [Platform] " to DEMO 2
//print_r($mdm->assignJob("150615AD-4FC4-4991-8CF6-9AB34F8C5036","93703fb2-6246-4fcd-b6c9-0d38a193418e"));


// Assign job "OBC 1.100 [Platform]" to DEMO 2
print_r($mdm->assignJob("C4C96238-5DF3-43CA-97C4-216C0867C362","93703fb2-6246-4fcd-b6c9-0d38a193418e"));



echo "<br><br>";

print_r($mdm->getJobsByFolderID("d6ac05f2-b883-4740-afe8-260a30730d1b"));



echo "<br><br>Done.";



?>

