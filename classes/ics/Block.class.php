<?php namespace ics;

abstract class Block {
  public static $sStartTag = 'BEGIN:';
  public static $sEndTag   = 'END:';
  private $aoProperty      = [];
  private $aoBlock         = [];
  
  abstract protected function getType();
  abstract protected function getPropertyConfig();
  abstract protected function getBlockConfig();

  public function addProperty(Property $oProperty) {
    $oProperty->validate();
    $this->aoProperty[] = $oProperty;
    return $this;
  }

  public function getProperty() {
    return $this->aoProperty;
  }

  public function addBlock(Block $oBlock) {
    $oBlock->validate();
    $this->aoBlock[] = $oBlock;
    return $this;
  }

  public function getBlock() {
    return $this->aoBlock;
  }

  public function validate() {
    //TYPE
    if ( empty($this->getType()) ) {
      throw new \Exception('Block has no type');
    }

    //PROPERTY CONFIG
    if ( !is_array($hPropConfig = $this->getPropertyConfig()) ) {
      throw new \Exception('Invalid property config');
    }

    //CHECK PROPERTY TYPE/NAME
    foreach ( $this->getProperty() as $oProperty ) {
      //NAME
      if ( empty($hPropConfig[strtolower($oProperty->getName())]) ) {
        throw new \Exception(sprintf('Property %s is not allowed in block %s', $oProperty->getName(), $this->getType()));
      }

      //TYPE
      $asParts   = explode('\\', get_class($oProperty));
      $sPropType = str_replace('Property', '', array_pop($asParts));
      if ( empty($hPropConfig[strtolower($oProperty->getName())]['type']) ) {
        throw new \Exception(sprintf('No type for property %s in block %s defined', $oProperty->getName(), $this->getType()));
      } elseif ( $sPropType !== $hPropConfig[strtolower($oProperty->getName())]['type'] ) {
        throw new \Exception(sprintf('Property %s needs in block %s type %s but %s given', $oProperty->getName(), $this->getType(), $sPropType, $hPropConfig[strtolower($oProperty->getName())]['type']));
      }

      //COUNT
      if ( empty($hPropConfig[strtolower($oProperty->getName())]['count']) ) {
        $hPropConfig[strtolower($oProperty->getName())]['count'] = 0;
      }
      $hPropConfig[strtolower($oProperty->getName())]['count']++;
    }

    //CHECK PROPERTY MAX/REQUIRED
    foreach ( $hPropConfig as $sProp => $hConfig ) {
      //REQUIRED
      if ( !empty($hConfig['required']) && empty($hConfig['count']) ) {
        throw new \Exception(sprintf('Property %s in block %s is required', $sProp, $this->getType()));
      }

      //MAX
      if ( array_key_exists('max', $hConfig) && is_int($hConfig['max']) && array_key_exists('count', $hConfig) && $hConfig['max'] < $hConfig['count'] ) {
        throw new \Exception(sprintf('Property %s in block %s has maximum of %d but %d given', $sProp, $this->getType(), $hConfig['max'], $hConfig['count']));
      }
    }
  }

  private function getBlockStartFlag() {
    return self::$sStartTag.strtoupper($this->getType());
  }

  private function getBlockEndFlag() {
    return self::$sEndTag.strtoupper($this->getType());
  }

  private function propertyToICS() {
    return implode("\n", array_map(function(Property $oProperty) { return $oProperty->toICS(); }, $this->aoProperty));
  }

  private function blockToICS() {
    return implode("\n", array_map(function(Block $oBlock) { return $oBlock->toICS(); }, $this->aoBlock));
  }

  public function toICS() {
    $this->validate();
    $sContent = '';
    //PROPERTY
    if ( !empty($sProperty = $this->propertyToICS()) ) {
      $sContent .= $sProperty;
    }

    //BLOCK
    if ( !empty($sBlock = $this->blockToICS()) ) {
      $sContent .= $sBlock;
    }

    //EMPTY
    if ( empty($sContent) ) {
      return '';
    }

    return sprintf("%s\n%s\n%s", $this->getBlockStartFlag(), $sContent, $this->getBlockEndFlag());
  }

  public function __toString() {
    $sProperty = '  '.implode("\n  ", array_map(function(Property $oProperty) { return sprintf('%s', $oProperty); }, $this->aoProperty));
    return sprintf("[%s]\n%s\n[/%s]\n", print_r($this->sType, TRUE), $sProperty, print_r($this->sType, TRUE));
  }

  private function stripOuterTag(&$asLine) {
    if ( strtoupper(trim($asLine[0])) === self::$sStartTag.$this->getType() ) {
      $asLine = array_slice($asLine, 1, -1);
    }
  }

  public function fromICS($sICSBlock) {
    $asLine       = explode("\n", $sICSBlock);
    $hPropConfig  = $this->getPropertyConfig();
    $hBlockConfig = $this->getBlockConfig();
    $hTmpBlock    = [];

    //STRIP OUTER TAG
    $this->stripOuterTag($asLine);

    foreach ( $asLine as $sLine ) {
      //EMPTY
      if ( empty($sLine) ) {
        continue;
      }

      //IN BLOCK
      if ( !empty($hTmpBlock['end']) ) {
        $hTmpBlock['content'][] = $sLine;
        //BLOCK END FOUND
        if ( trim(strtoupper($sLine)) == $hTmpBlock['end'] ) {
          //CREATE & ADD BLOCK
          if ( !empty($hBlockConfig[$hTmpBlock['type']]) ) {
            if ( !class_exists($sClass = __NAMESPACE__.'\\'.$hBlockConfig[$hTmpBlock['type']]['type']) ) {
              throw new \Exception('Found no class %s for block %s', $sClass, $hTmpBlock['type']);
            }
            $oBlock = new $sClass();
            $this->addBlock($oBlock->fromICS(implode("\n", $hTmpBlock['content'])));
          }
          $hTmpBlock = [];
        }
        continue;
      }

      //CHECK IF BLOCK START
      if ( strpos(strtoupper($sLine), self::$sStartTag) === 0 ) {
        $hTmpBlock = [
          'end'     => str_replace(self::$sStartTag, self::$sEndTag, strtoupper(trim($sLine))),
          'content' => [$sLine],
          'type'    => strtolower(str_replace(self::$sStartTag, '', strtoupper(trim($sLine)))),
        ];
        continue;
      }

      //CHECK IS KNOWN PROPERTY
      $sProp = strtolower(Property::getNameFromICS($sLine));
      if ( empty($hPropConfig[$sProp]) ) {
        continue; //UNKNOWN PROPERTY, MAYBE FAILURE
      }

      //CREATE & ADD PROPERTY
      if ( !class_exists($sClass = __NAMESPACE__.'\\'.$hPropConfig[$sProp]['type'].'Property') ) {
        throw new \Exception('Found no class %s for property %s', $sClass, $sProp);
      }
      $oProp = new $sClass();
      $this->addProperty($oProp->fromICS($sLine));
    }
    return $this;
  }
}


