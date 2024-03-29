<?php
  /**
  * GoogleKML is PHP class used for create Google KML file.
  * @package php_geoclasses
  * @name phpGoogleKML
  *
  * @author Peter Misovic - Thailon
  * @copyright GPL licence
  * @link http://internetgis.net/projects/geoclasses/phpGoogleKML
  * @version 0.2
  *
  * HISTORY
  * ver 0.2 - first oficial version
  * + generate line placemarks added
  * + set of encoding added
  * + set of xmlns version added
  * ver 0.1 - initial version
  * + generate only point palcemarks
  *
  **/


class phpGPX {

  // PROPERTIES

  // xmnls
  var $creator = "phpGPX";
  var $xmlns = "http://www.topografix.com/GPX/1/1";
  var $xmlns_xsi = "http://www.w3.org/2001/XMLSchema-instance";
  var $xmnls_xsi_schemaLocation = "http://www.topografix.com/GPX/1/1";
  var $xmnls_xsd = "http://www.topografix.com/GPX/1/1/gpx.xsd";
  
  // xml
  var $xml_encoding = "UTF-8";
  var $xml_version = "1.0";
  
  var $metadata="";
  
  // system
  var $outputDirectory = "./";
  var $filename = "phpGPX.gpx";
  var $resource;  

  
  //var $KML_name = "KML name was not defined. Using default.";
  //var $KML_description = "KML description was not defined. Using default.";
  
  
  
  var $errorMessage = ""; var $file_content = ""; var $pointWpt = ""; var $lineWpt = ""; var $footer = ""; var $header = "";
  var $xml_tag = ""; var $gpx_tag = ""; var $metadata_tag = "";

  
  function phpGPX() {}
  
  
  // INTERNAL METHODS
  function ValidateOutputDirecotry() {
	if (!file_exists($this->outputDirectory)) die('Output directory does not exist! Please create valid directory.');
	if (!is_dir($this->outputDirectory)) die('Not an directory! Please enter valid directory.');
	if (!is_writable($this->outputDirectory)) die('Direcotry is not writable! Please set appertaining permissions.');
  }

  function GetXmlTag() {
  	$this->xml_tag = "<?xml version=\"".strip_tags(trim($this->xml_version))."\" encoding=\"".strip_tags(trim($this->xml_encoding))."\"?>\n";
	return $this->xml_tag;
  }

  function GetGpxTag() {
	$this->gpx_tag = "<gpx creator=\"".strip_tags(trim($this->creator))."\" xmlns=\"".strip_tags(trim($this->xmlns))."\" xmlns:xsi=\"".strip_tags(trim($this->xmlns_xsi))."\" xsi:schemaLocation=\"".strip_tags(trim($this->xmnls_xsi_schemaLocation))." ".strip_tags(trim($this->xmnls_xsd))."\">\n";
	return $this->gpx_tag;
  }

  function GetMetadataTag() {
  	if (!empty($this->metadata)) {
  		$this->metadata_tag = "<metadata>".strip_tags(trim($this->metadata))."</metadata>\n";
  	} else {
  		$this->metadata_tag = "<metadata/>\n";
  	}
	return $this->metadata_tag;
  }


  /**
   * This internal method returns KML file header based on user defined (or pre-defined) kml or kml parameters
   *
   * @return string
   */
  function CreateHeader() {
  	$this->header .= $this->GetXmlTag(); 
	$this->header .= $this->GetGpxTag();
	$this->header .= $this->GetMetadataTag();
  	return $this->header;
  }


  function StartTrack($name ) {
    $this->pointWpt .= "<trk><name>$name</name><trkseg>";
  }

  function EndTrack() {
    $this->pointWpt .= "</trkseg></trk>";
  }

  function CreateFooter() {
  	$this->footer .= "</gpx>\n";
  	return $this->footer;
  }



 //<time>2011-07-13T07:53:42+0000</time


function addTrackPoint($time,$lat,$lon) {
    $this->pointWpt .= "<trkpt lat=\"".$lat."\" lon=\"".$lon."\">\n";
    $this->pointWpt .= "<time>".$time."</time>\n";
  	$this->pointWpt .= "</trkpt>\n";
}

  // EXTERNAL METHODS
  function addPoint($name,$cmt,$sym,$type,$description,$latitude,$longitude) {
	$this->pointWpt .= "<wpt lat=\"".$latitude."\" lon=\"".$longitude."\">\n";
	$this->pointWpt .= "<name>".$name."</name>\n";
	//$this->pointWpt .= "<cmt>".$cmt."</cmt>\n";
	//$this->pointWpt .= "<desc><![CDATA[".$description."]]></desc>\n";
	//$this->pointWpt .= "<sym>".$sym."</sym>\n";
	//$this->pointWpt .= "<type>".$type."</type>\n";
	$this->pointWpt .= "</wpt>\n";
	return $this->pointWpt;
  }
  
  function addLine() {}
  
  
  
  /**
   * This external method creates the KML file
   * @todo add ValidateFile method.   
   *    
   */
  function CreateGPXfile() {
  	$this->ValidateOutputDirecotry();
	$this->resource = fopen($this->outputDirectory.$this->filename,"w+");
	if ($this->resource) {
		$this->file_content .= $this->CreateHeader();
		$this->file_content .= $this->pointWpt;
		$this->file_content .= $this->lineWpt;
		$this->file_content .= $this->CreateFooter();
  		if (!fputs($this->resource, $this->file_content, strlen($this->file_content))) {die('Error during KML file content writing.'); unlink($this->outputDirectory.$this->filename);}
  		fclose($this->resource);
  	} else {
  		die('File resource does not exists.');
  	}
  }
  
	function GetContent() {
		echo $this->CreateHeader();
	  	echo $this->pointWpt;
	  	echo $this->lineWpt;
	  	echo $this->CreateFooter();
	}
  
  
  function DownloadGPXfile($download_type) {
  	switch ($download_type) {
  		case "KML":
			header("Content-type: application/gpx");
		  	header("Content-Disposition: attachment; filename=\"".$this->filename."\"");
		  	$this->GetContent();
  		break;
  		
  		case "TXT":
  			header("Content-type: txt/txt");
		  	header("Content-Disposition: attachment; filename=\"".$this->filename.".txt\"");
		  	$this->GetContent();
		  	/*echo $this->CreateHeader();
		  	echo $this->pointWpt;
		  	echo $this->lineWpt;
		  	echo $this->CreateFooter();*/
  		break;
  	}
  }
  
  /**
   * This external method displays created KML file in browser.
   *
   */
  function DisplayGPXfile() {
	print highlight_string($this->CreateHeader().$this->pointWpt.$this->lineWpt.$this->CreateFooter(),1);
  }
}
?>
