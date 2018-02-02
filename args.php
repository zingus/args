<?php
//TODO this shit needs documentation
/*
  Usage example:

  class stikazziArgs extends args
  {
    function dash_dash_someoption()
    {
      // handle it
    }

    function dash_someoptionwitharguments_($arg)
    {
      // handle it and its $arg
    }

    function handle($arg) 
    {
      // handle a single args
    }

    function handleAll($argList) 
    {
      // handle a whole $argList
    }

  }

  $args=new stikazziArgs();

  PROPOSAL: change name to argHandler.php?
            change name to argFucker.php?
            change name to argumenter.php? <=
            change name to argp.php?
*/

class args
{

  function args($obj=null)
  {
    $this->obj=$obj;
    $this->resetCallback();
    $this->optionsMap=array(
      '-'=>'dash',
      '/'=>'slash',
      '--'=>'dash_dash',
    );
    $this->debug=false;
    $this->init();
    $this->argList=$this->process();
  }

  function process()
  {
    $argv=$_SERVER['argv'];
    $args=array();
    foreach($argv as $k=>$v){
      // skip argv[0]
      if($k==0) continue;
      
      $ok=$this->attemptCallback($v);
      if($ok)
        continue;
      
      $ok=$this->determineCallback($v);
      if($ok) {
        $this->attemptCallback();
        continue;
      }
      
      if(!$this->callback) {
        $this->handle($v);
        $args[]=$v;
      }
    }
    
    $this->handleAll($args);
    return $args;
  }
  
  function determineCallback($rawArgument)
  {
    $ok=$this->parseOption($rawArgument);
    if(!$ok)
      return false;

    $callbackName="option_{$this->optionMapping}_{$this->optionName}";
    //echo $callbackName,"\n";

    if(is_object($this->obj))
      $callback=array($this->obj,$callbackName);
    else
      $callback=$callbackName;

    $this->debugReport($callback);

    if(is_callable($callback)) {
      $this->callback=$callback;
      $this->waitingForArgument=false;
      return true;
    }
    
    $callbackName.='_';

    if(is_object($this->obj))
      $callback=array($this->obj,$callbackName);
    else
      $callback=$callbackName;
    
    $this->debugReport($callback);

    if(is_callable($callback)) {
      $this->callback=$callback;
      $this->waitingForArgument=true;
      return true;
    }

    $this->callback=null;
    return false;
  }

  function attemptCallback($argument=null)
  {
    if(!$this->callback)
      return false;

    if(is_null($argument)) {
      if($this->waitingForArgument)
        return false;
      $array=array();
    } else {
      if(!$this->waitingForArgument)
        return false;
      $array=array($argument);
    }
    
    call_user_func_array($this->callback,$array);
    $this->resetCallback();
    return true;
  }

  function resetCallback()
  {
    $this->callback=null;
    $this->waitingForArgument=false;
  }

  function parseOption($rawArgument)
  {
    $lead=$rawArgument[0].$rawArgument[1];
    @$match=$this->optionsMap[$lead];

    if(!$match){
      $lead=$rawArgument[0];
      @$match=$this->optionsMap[$lead];
    }
      
    if($match) {
      $this->optionLead=$lead;
      $this->optionName=substr($rawArgument,strlen($lead));
      $this->optionName=strtr(rawurlencode($this->optionName),array('%'=>'','-'=>'2D'));
      $this->optionMapping=$match;
      return true;
    }
    
    return false;
  }

  function debugReport($callback)
  {
    if (!$this->debug) return;
    

    if (is_array($callback)) {
      $leader="array(\$object,'"; $trailer="')";
      $callback=$callback[1];
      $what='method';
    } else {
      $leader="'"; $trailer="'";
      $what='function';
    }
    
    if (substr($callback,-1)=='_')
      $arg='$argument';
    else
      $arg='';


    echo "Looking for $what $leader$callback($arg)$trailer\n";
  }

  function init()
  {
  
  }

  function handle($arg)
  {
  
  }

  function handleAll($list)
  {
  
  }
}

function args($obj=null)
{
  $args=new args();
  return $args->process();
}
