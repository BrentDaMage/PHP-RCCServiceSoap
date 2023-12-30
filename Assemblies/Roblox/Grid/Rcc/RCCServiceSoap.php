<?php

// This file defines the RCCServiceSoap class. The class inherits the properties of a standard SoapClient, but it can be used to call RCCService functions via SOAP requests.

namespace Roblox\Grid\Rcc;

class RCCServiceSoap {

	private $SoapClient;
	private $classmap = [
		"Status" => "Roblox\Grid\Rcc\Status",
		"Job" => "Roblox\Grid\Rcc\Job",
		"ScriptExecution" => "Roblox\Grid\Rcc\ScriptExecution",
		"LuaValue" => "Roblox\Grid\Rcc\LuaValue",
		"LuaType" => "Roblox\Grid\Rcc\LuaType"
	];
	public $ip;
	public $port;
	public $url;

	// The specification of a URL and port should always be done during production, though the defaults can be used when testing.
	function __construct($url = "127.0.0.1", $port = 64989) {
		$this->ip = $url;
		$this->port = $port;
		$this->url = $url.":".$port;
		$this->SoapClient = new \SoapClient(__DIR__."\RCCService.wsdl", ["location" => "http://".$url.":".$port, "uri" => "http://roblox.com/", "classmap" => $this->classmap, "exceptions" => false]);
	}

	// Begin function handlers
	// Use the HelloWorld function as a template for all future functions.
	// NOTE: Please use is_soap_fault() when checking if functions failed.

	function callToService($name, $arguments = []) {
		$result = $this->SoapClient->{$name}($arguments);
		return (!is_soap_fault($result) ? (/*is_soap_fault($result) ||*/ !isset($result->{$name."Result"}) ? null : $result->{$name."Result"}) : $result);
	}
	
	private static function parseJobResult($value) {
		if ($value !== new \stdClass() && isset($value->LuaValue)) {
			// Our job result isn't empty, so let's deserialize it
			$result = LuaValue::deserializeValue($value->LuaValue);
		}else {
			// Something went wrong :(
			$result = null;
		}
		return $result;
	}

	/**
	 * Name: Hello World
	 * Description: This function calls a simple HelloWorld function from RCCService. The expected HelloWorldResponse is "Hello World".
	 * Parameters: []
	 */
	function HelloWorld() {
		return $this->callToService(__FUNCTION__);
	}
	
	/**
	 * Name: Get Version
	 * Description: This function fetches the version of RCCService.
	 * Parameters: []
	 */
	function GetVersion() {
		return $this->callToService(__FUNCTION__);
	}
	
	/**
	 * Name: Open Job
	 * Description: This function opens a job in accordance with the given arguments. Though this function is deprecated on ROBLOX's end, we'll still use it here and OpenJobEx will be called instead.
	 * Parameters: [
	 *	"job" 	 => "This function opens a job in accordance with the given arguments. It returns the value that's returned by the Lua script.",
	 *	"script" =>	"The ScriptExecution class that's going to be executed in the job. This contains values such as name, script, and arguments."
	 * ]
	 */
	function OpenJob($job, $script = null) {
		return $this->OpenJobEx($job, $script);
	}
	
	/**
	 * Name: Open Job Ex
	 * Description: This function opens a job in accordance with the given arguments. It returns the value that's returned by the Lua script. Feel free to use the other version of this function.
	 * Parameters: [
	 *	"job" 	 => "The Job class that's going to be serialized and pushed to the service.",
	 *	"script" =>	"The ScriptExecution class that's going to be executed in the job. This contains values such as name, script, and arguments."
	 * ]
	 */
	function OpenJobEx($job, $script = null) {
		$result = $this->callToService(__FUNCTION__, ["job" => $job, "script" => $script]);
		return RCCServiceSoap::parseJobResult($result);
	}
	
	/**
	 * Name: Batch Job
	 * Description: This function runs a batch job in accordance with the given arguments. Though this function is deprecated on ROBLOX's end, we'll still use it here and BatchJobEx will be called instead.
	 * Parameters: [
	 *	"job" 	 => "The Job class that's going to be serialized and pushed to the service.",
	 *	"script" =>	"The ScriptExecution class that's going to be executed in the job. This contains values such as name, script, and arguments."
	 * ]
	 */
	function BatchJob($job, $script) {
		return $this->BatchJobEx($job, $script);
	}
	
	/**
	 * Name: Batch Job Ex
	 * Description: This function runs a batch job in accordance with the given arguments. Feel free to use the other version of this function.
	 * Parameters: [
	 *	"job" 	 => "The Job class that's going to be serialized and pushed to the service.",
	 *	"script" =>	"The ScriptExecution class that's going to be executed in the job. This contains values such as name, script, and arguments."
	 * ]
	 */
	function BatchJobEx($job, $script) {
		$result = $this->callToService(__FUNCTION__, ["job" => $job, "script" => $script]);
		return RCCServiceSoap::parseJobResult($result);
	}
	
	/**
	 * Name: Renew Lease
	 * Description: This function changes the expirationInSeconds of a job based on the jobID. It essentially allows you to set the expiration time of a currently opened job.
	 * Parameters: [
	 *	"jobID" 			  => "The ID of the job who's expiration is going to be renewed.",
	 *	"expirationInSeconds" => "The new expiration time for the job."
	 * ]
	 */
	function RenewLease($jobID, $expirationInSeconds) {
		return $this->callToService(__FUNCTION__, ["jobID" => $jobID, "expirationInSeconds" => $expirationInSeconds]);
	}
	
	/**
	 * Name: Execute
	 * Description: This function uses the given arguments to execute a script inside an existing job. Though this function is deprecated on ROBLOX's end, we'll still use it here and ExecuteEx will be called instead.
	 * Parameters: [
	 *	"jobID"		=> "The ID of the job in which the script is executed.",
	 *	"script" 	=> "The script that's going to be executed."
	 * ]
	 */
	function Execute($jobID, $script) {
		return $this->ExecuteEx($jobID, $script);
	}
	
	/**
	 * Name: Execute Ex
	 * Description: This function uses the given arguments to execute a script inside an existing job.
	 * Parameters: [
	 *	"jobID"		=> "The ID of the job in which the script is executed.",
	 *	"script" 	=> "The script that's going to be executed."
	 * ]
	 */
	function ExecuteEx($jobID, $script) {
		return $this->callToService(__FUNCTION__, ["jobID" => $jobID, "script" => $script]);
	}
	
	/**
	 * Name: Close Job
	 * Description: This function closes an existing job using the given job ID.
	 * Parameters: [
	 *	"jobID"		=> "The ID of the job that's going to be closed."
	 * ]
	 */
	function CloseJob($jobID) {
		return $this->callToService(__FUNCTION__, ["jobID" => $jobID]);
	}

	/**
	 * Name: Get Expiration
	 * Description: This function fetches and returns the expirationInSeconds of a job using the given job ID.
	 * Parameters: [
	 *	"jobID"		=> "The ID of the job."
	 * ]
	 */
	function GetExpiration($jobID) {
		return $this->callToService(__FUNCTION__, ["jobID" => $jobID]);
	}
	
	/**
	 * Name: Diag
	 * Description: This function returns various types of diagnostic information from RCCService. Though this function is deprecated on ROBLOX's end, we'll still use it here and DiagEx will be called instead.
	 * Parameters: [
	 *	"type" 	=> "The diagnostic type to retrieve.",
	 *	"jobID"	=> "The id of the job to retrieve the diagnostic from."
	 * ]
	 */
	function Diag($type, $jobID) {
		return $this->DiagEx($type, $jobID);
	}
	
	/**
	 * Name: Diag Ex
	 * Description: This function returns various types of diagnostic information from RCCService.
	 * Parameters: [
	 *	"type"	=> "The diagnostic type to retrieve.",
	 *	"jobID" => "The id of the job to retrieve the diagnostic from."
	 * ]
	 */
	/* This is the format of the Diag data:

		type == 0
			DataModel Count in this process
			PerfCounter data
				Task Scheduler
				(obsolete entry)
				double threadAffinity
				double numQueuedJobs
				double numScheduledJobs
				double numRunningJobs
				long threadPoolSize
				double messageRate
				double messagePumpDutyCycle					
			DataModel Jobs Info
			Machine configuration
			Memory Leak Detection
		type & 1
			leak dump
		type & 2
			attempt to allocate 500k. if success, then true else false
		type & 4
			DataModel dutyCycles
	*/
	function DiagEx($type, $jobID) {
		return $this->callToService(__FUNCTION__, ["type" => $type, "jobID" => $jobID]);
	}
	
	/**
	 * Name: Get Status
	 * Description: This function fetches the status information from RCCService. The returned Status class contains a version string and an environmentCount int.
	 * Parameters: []
	 */
	function GetStatus() {
		return $this->callToService(__FUNCTION__);
	}
	
	/**
	 * Name: Get All Jobs
	 * Description: This function fetches an array of every job that's currently open on RCCService. Though this function is deprecated on ROBLOX's end, we'll still use it here and GetAllJobsEx will be called instead.
	 * Parameters: []
	 */
	function GetAllJobs() {
		// GetAllJobs is deprecated.
		return $this->GetAllJobsEx();
	}
	
	/**
	 * Name: Get All Jobs Ex
	 * Description: This function fetches an array of every job that's currently open on RCCService.
	 * Parameters: []
	 */
	function GetAllJobsEx() {
		return $this->callToService(__FUNCTION__);
	}
	
	/**
	 * Name: Close Expired Jobs
	 * Description: This function closes all currently open and expired jobs on RCCService. This returns the amount of jobs that were closed.
	 * Parameters: []
	 */
	function CloseExpiredJobs() {
		return $this->callToService(__FUNCTION__);
	}
	
	/**
	 * Name: Close All Jobs
	 * Description: This function closes all currently open jobs on RCCService. This returns the amount of jobs that were closed.
	 * Parameters: []
	 */
	function CloseAllJobs() {
		return $this->callToService(__FUNCTION__);
	}
}

// EOF
