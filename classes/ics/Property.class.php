<?php namespace ics;

abstract class Property {
  public static $sSeperator          = ':';
  public static $sParameterSeperator = ';';
  private $sName                     = NULL;
  private $xValue                    = NULL;
  private $aoParameter               = [];

  abstract protected function valueToICS();
  abstract protected function valueFromICS($sICSValue);
  abstract protected function getParameterConfig();

  public function addParameter(Parameter $oParameter) {
    $oParameter->validate();
    $this->aoParameter[] = $oParameter;
    return $this;
  }

  public function getParameter() {
    return $this->aoParameter;
  }

  public function getName() {
    return $this->sName;
  }

  public function setName($sName) {
    $this->sName = strtoupper($sName);
    return $this;
  }

  public function getValue() {
    return $this->xValue;
  }

  public function setValue($xValue) {
    $this->xValue = $xValue;
    return $this;
  }

  public function validate() {
    //NAME
    if ( is_null($this->sName) ) {
      throw new \Exception('Property has no name');
    }

    //VALUE
    if ( is_null($this->xValue) ) {
      throw new \Exception('Property has no value');
    }

    //PARAMETER CONFIG
    if ( !is_array($hParamConfig = $this->getParameterConfig()) ) {
      throw new \Exception('Invalid parameter config');
    }

    //CHECK PROPERTY ALLOWED/NAME
    foreach ( $this->getParameter() as $oParameter ) {
      //NAME
      if ( empty($hParamConfig[strtolower($oParameter->getName())]) ) {
        throw new \Exception(sprintf('Parameter %s is not allowed in property %s', $oParameter->getName(), $this->getName()));
      }

      //ALLOWED
      if ( !empty($hParamConfig[strtolower($oParameter->getName())]['allowed']) && !in_array(strtoupper($oParameter->getValue()), array_map('strtoupper', $hParamConfig[strtolower($oParameter->getName())]['allowed']))) {
        throw new \Exception(sprintf('Parameter %s has not allowed value %s', $oParameter->getName(), $oParameter->getValue()));
      }

      //COUNT
      if ( empty($hParamConfig[strtolower($oParameter->getName())]['count']) ) {
        $hParamConfig[strtolower($oParameter->getName())]['count'] = 0;
      }
      $hParamConfig[strtolower($oParameter->getName())]['count']++;
    }

    //CHECK PROPERTY MAX/REQUIRED
    foreach ( $hParamConfig as $sParam => $hConfig ) {
      //REQUIRED
      if ( !empty($hConfig['required']) && empty($hConfig['count']) ) {
        throw new \Exception(sprintf('Parameter %s in property %s is required', $sParam, $this->getType()));
      }

      //MAX
      if ( array_key_exists('max', $hConfig) && is_int($hConfig['max']) && array_key_exists('count', $hConfig) && $hConfig['max'] < $hConfig['count'] ) {
        throw new \Exception(sprintf('Parameter %s in property %s has maximum of %d but %d given', $sParam, $this->getType(), $hConfig['max'], $hConfig['count']));
      }
    }
    return $this;
  }

  public static function getNameFromICS($sICS) {
    //STRING
    if ( !is_string($sICS) ) {
      throw new \Exception('ICS is not a string');
    }

    //GET NAME
    $asPart = explode(self::$sSeperator, $sICS);
    if ( !count($asPart) ) {
      throw new \Exception('ICS has no name: '.$sICS);
    }
    $asParameter = explode(self::$sParameterSeperator, array_shift($asPart));
    return array_shift($asParameter);
  }

  public function fromICS($sICS) {
    $hParamConfig = $this->getParameterConfig();

    //STRING
    if ( !is_string($sICS) ) {
      throw new \Exception('ICS is not a string');
    }

    //SET NAME
    $asPart = explode(self::$sSeperator, $sICS);
    if ( count($asPart) < 2 ) {
      throw new \Exception('ICS has no name: '.$sICS);
    }
    $asParameter = explode(self::$sParameterSeperator, array_shift($asPart));
    $this->setName(array_shift($asParameter));

    //PARAMETER
    foreach ( $asParameter as $sParameter ) {
      //CHECK IS KNOWN PARAMETER
      $asParamParts = explode(Parameter::$sSeperator, $sParameter);
      $sParam = strtolower(array_shift($asParamParts));
      if ( empty($hParamConfig[$sParam]) ) {
        continue; //UNKNOWN PARAMTER, MAYBE FAILURE
      }
      $oParameter = new Parameter();
      $this->addParameter($oParameter->fromICS($sParameter));
    }

    //VALUE
    $this->valueFromICS(implode(self::$sSeperator, $asPart));
    return $this;
  }

  private function parameterToICS() {
    if ( empty($this->getParameter()) ) {
      return '';
    }
    return self::$sParameterSeperator.implode(self::$sParameterSeperator, array_map(function(Parameter $oParameter) { return $oParameter->toICS(); }, $this->getParameter()));
  }

  public function toICS() {
    $this->validate();
    return sprintf('%s%s%s%s', $this->getName(), $this->parameterToICS(), self::$sSeperator, $this->valueToICS());
  }

  public function __toString() {
    return sprintf("Name:%s Value:%s\n", print_r($this->sName, TRUE), print_r($this->xValue, TRUE));
  }
}


