# PHP-RCC
A PHP framework for RCCService SOAP interaction.

A PHP autoloader is recommended in order to utilize this framework.

Futher documentation can be found inside the source files.

###### Example Code
```php
$RCCServiceSoap = new Roblox\Grid\Rcc\RCCServiceSoap("127.0.0.1", 64989);

echo("HelloWorld(): ".($RCCServiceSoap->HelloWorld() ?? "Failed!")."\n");
echo("GetVersion(): ".($RCCServiceSoap->GetVersion() ?? "Failed!")."\n");
echo("GetStatus(): ".($RCCServiceSoap->GetStatus()->environmentCount ?? "Failed!")."\n");

print_r($RCCServiceSoap->GetAllJobs());
//echo('GetAllJobs(): '.($RCCServiceSoap->GetAllJobs() ?? "Failed!")."\n");

$job = new Roblox\Grid\Rcc\Job("StringTest1");
$scriptText = 'print("Recieved job with ID " .. game.JobId)
return "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."';
$script = new Roblox\Grid\Rcc\ScriptExecution("StringTest1-Script", $scriptText);
$value = $RCCServiceSoap->BatchJob($job, $script);
echo("StringTest1: ".(!is_soap_fault($value) ? ($value !== null ? $value : "null") : "Failed!")."\n");
```
