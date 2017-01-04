<?php namespace ics;

class TextProperty extends Property {
  protected function getParameterConfig() {
    return [];
  }

  protected function valueFromICS($sICSValue) {
    $this->setValue(trim($sICSValue));
    return $this;
  }

  protected function valueToICS() {
    return $this->getValue();
  }

  public function validate() {
    parent::validate();

    //VALUE IS STRING
    if ( !is_string($this->getValue()) ) {
      throw new \Exception('Value is not of type String');
    }
    return $this;
  }
}

