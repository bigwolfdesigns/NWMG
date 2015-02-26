<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');
//the timeout is how long you want to wait for a process to be considered dead
$exec['timeout']		 = 20; //BETA: in seconds
$exec['timeout_result']	 = 5; //BETA: in seconds for which the result of a command will be kept in cache
$exec['concurrent']		 = 1; //BETA: how many concurrent processes of the same CMD execute at the same time (timeout needs to be >0 for this to work)
$exec['bin']			 = array(
	'awk'		=>'/usr/bin/awk',
	'convert'	=>'nice -n 10 /usr/local/bin/convert -limit time 10 -limit memory 512Mb -quiet', //limit will limit the time in seconds that convert can spend processing
	'composite'	=>'nice -n 10 /usr/local/bin/composite -limit time 10 -limit memory 512Mb -quiet',
	'grep'		=>'/usr/bin/grep',
	'gzip'		=>'/usr/bin/gzip',
	'identify'	=>'nice -n 10 /usr/local/bin/identify -limit time 10 -limit memory 512Mb -quiet',
	'montage'	=>'nice -n 10 /usr/local/bin/montage -limit time 10 -limit memory 512Mb -quiet',
	'php'		=>'/usr/local/bin/php',
	'rsync'		=>'/usr/local/bin/rsync',
	'rm'		=>'/bin/rm',
	'scp'		=>'/usr/bin/scp',
	'sed'		=>'/usr/bin/sed',
	'ssh'		=>'/usr/bin/ssh',
	'tar'		=>'/usr/bin/tar',
	'zip'		=>'/usr/local/bin/zip',
);

if(substr(PHP_OS, 0, 3) == 'WIN'){
	$exec['prepend'] = ''; //prepend to the exec string - no space will be added
	$exec['append']	 = ''; //append to the exec string - no space will be added
}else{
	$exec['prepend'] = ''; //prepend to the exec string - no space will be added
	$exec['append']	 = ' 2>/dev/null'; //append to the exec string - no space will be added
}

/*
  -S   Change and report the soft limit associated with a resource.
  -H   Change and report the hard limit associated with a resource.

  -a   All current limits are reported.
  -b	sbsize (bytes)
  -c   The maximum size of core files created (512-blocks).
  -d   The maximum size of a process's data segment.
  -f   The maximum size of files created by the shell(default option)  (512-blocks)
  -l   The maximum size that may be locked into memory.
  -m   The maximum resident set size.
  -n   The maximum number of open file descriptors.
  -p   The pipe buffer size (pseudo-terminals).
  -s   The maximum stack size.
  -t   The maximum amount of cpu time in seconds.
  -u   The maximum number of processes available to a single user.
  -v   The maximum amount of virtual memory available to the process.
  -w	swap limit

  When setting new limits, if neither `-H' nor `-S' is supplied, both the hard and soft limits are set.
  Restricting per user processes ( -u) can be useful for limiting the potential effects of a fork bomb.
  Values are in 1024-byte increments, except for `-t', which is in seconds, `-p', which is in units of 512-byte blocks, and `-n' and `-u', which are unscaled values.
 */
//$exec['ulimit']['bin']			= '/usr/bin/ulimit';
$exec['ulimit']['bin']			 = 'ulimit'; //built in - no need for path
$exec['ulimit']['limits']['t']	 = 30;
$exec['ulimit']['limits']['f']	 = 307200;  //150MB
$exec['ulimit']['limits']['v']	 = 307200 * 2;  //300MB - removed on 2013-02-01 - added 2014-06-04 - changed to 600MB on 2014-09-04
//$exec['ulimit']['limits']['n']	= 1000;			//removed on 2013-02-01
$exec['ulimit']['limits']['m']	 = 307200;  //300MB - removed on 2013-02-01 - added 2014-06-04
$exec['ulimit']['limits']['u']	 = 500;
?>