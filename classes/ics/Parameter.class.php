<?php namespace ics;

class Parameter {
  public static $sSeperator = '=';
  private $sName            = NULL;
  private $sValue           = NULL;

  public function getName() {
    return $this->sName;
  }

  public function setName($sName) {
    $this->sName = strtoupper(trim($sName));
    return $this;
  }

  public function getValue() {
    return $this->sValue;
  }

  public function setValue($sValue) {
    $this->sValue = trim($sValue);
    return $this;
  }

  public function validate() {
    //NAME
    if ( is_null($this->sName) ) {
      throw new \Exception('Parameter has no name');
    }

    //VALUE
    if ( is_null($this->sValue) ) {
      throw new \Exception('Parameter has no value');
    }
    return $this;
  }

  public function fromICS($sICS) {
    //STRING
    if ( !is_string($sICS) ) {
      throw new \Exception('Parameter in ICS-format is not a string');
    }

    //SET TYPE
    $asPart = explode(self::$sSeperator, $sICS);
    if ( count($asPart) != 2 ) {
      throw new \Exception('Parameter has invalid ICS format: '.$sICS);
    }
    $this->setName($asPart[0]);
    $this->setValue($asPart[1]);
    return $this;
  }

  public function toICS() {
    $this->validate();
    return sprintf('%s%s%s', $this->getName(), self::$sSeperator, $this->getValue());
  }

  public function __toString() {
    return sprintf("Name:%s Value:%s\n", print_r($this->sName, TRUE), print_r($this->sValue, TRUE));
  }
}



