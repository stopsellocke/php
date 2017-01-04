<?php namespace ics;

class DateTimeProperty extends Property {
  protected function getParameterConfig() {
    return [
      'value' => ['required' => false, 'allowed' => ['Date', 'Date-Time']],
      'tzid'  => ['required' => false],
    ];
  }

  private function getDateTimeFormat() {
    foreach ( $this->getParameter() as $oParameter ) {
      if ( $oParameter->getName() === 'VALUE' && strtoupper($oParameter->getValue()) === 'DATE' ) {
        return ['format' => 'Ymd', 'length' => 8];
      }
    }
    return ['format' => 'Ymd\THis', 'length' => 15];
  }

  protected function valueFromICS($sICSValue) {
    $hFormat = $this->getDateTimeFormat();
    $sValue = substr($sICSValue, 0, $hFormat['length']);
    if ( ($oDate = \DateTime::createFromFormat($hFormat['format'], $sValue)) === FALSE ) {
      throw new \Exception('Invalid DateTime-Format: '.$sValue);
    }
    $this->setValue($oDate);
    return $this;
  }

  protected function valueToICS() {
    $hFormat = $this->getDateTimeFormat();
    return $this->getValue()->format($hFormat['format']);
  }

  public function validate() {
    parent::validate();

    //VALUE IS DATETIME
    if ( !is_a($this->getValue(), 'DateTime') ) {
      throw new \Exception('Value is not of type DateTime');
    }
    return $this;
  }
}

