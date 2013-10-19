<?php
//TODO this shit needs documentation

class args
{

  function args($obj=null)
  {
    $this->handlerInstance=$obj;
    $this->resetCallback();
    $this->optionPrefixes=array(
      '-'=>'dash',
      '/'=>'slash',
      '--'=>'dash_dash',
    );
    $this->callbackVariants=array(
      'bundled'=>'%s_bundled',
      'numeric'=>'%s_NN',
      'arg'=>'%s_',
      'base'=>'%s',
    );
    $this->debug=false;
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
      
      $ok=$this->parseOption($rawArgument)
      if($ok) {
        $ok2=$this->determineCallback();
        if($ok2) {
          $this->attemptCallback();
          continue;
        }
      }
      
      if(!$this->callback)
        $args[]=$v;
    }
    return $args;
  }
  
  function determineCallback()
  {
    
    foreach( as $k=>$callback)
    {
      if(is_object($this->handlerInstance))
        $callback=array($this->handlerInstance,$this->callbackName);
      else
        $callback=$this->callbackName;

    $this->debugReport($callback);

    if(is_callable($callback)) {
      $this->callback=$callback;
      $this->waitingForArgument=false;
      return true;
    }
    
    $callbackName.='_';

    if(is_object($this->handlerInstance))
      $callback=array($this->handlerInstance,$callbackName);
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

  /**
    parses a $rawArgument like '-someoption' recognizes the prefix
   */
  function parseOption($rawArgument)
  {
    #
    # lookup prefix in $this->optionPrefixes
    # first try one with 2 chars, then one with a single char
    #
    $prefix=substr($rawArgument,0,2);
    @$mapped=$this->optionPrefixes[$prefix];

    if(!$mapped){
      $prefix=$prefix[0];
      @$mapped=$this->optionPrefixes[$prefix];
    }
     
    #
    # no match in $mapped? bail out
    #
    if(!$mapped) return false;
    
    #
    # still here? parse option
    #
    $this->optionPrefix=$prefix;
      
    // now parse $optionName
    $optionName=substr($rawArgument,strlen($prefix));
    
    // parse eventual "=value" arguments
    list($optionName,$argument)=split('=',$optionName,2);
   
    // normalize optionName
    $optionName=strtr(rawurlencode($optionName),array('%'=>'','-'=>'2D'));

    $baseCallbackName="option_{$mapped}_{$optionName}";
    
    $this->findCallback($baseCallbackName);
    $this->argument=$argument;

    return true;
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
}

function args($obj=null)
{
  $args=new args();
  return $args->process();
}
