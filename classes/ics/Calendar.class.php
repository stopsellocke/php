<?php namespace ics;

class Calendar extends Block {
  protected function getType() {
    return 'VCALENDAR';
  }

  protected function getPropertyConfig() {
    return [
    ];
  }

  protected function getBlockConfig() {
    return [
      'vevent' => ['required' => false, 'type' => 'Event'],
    ];
  }

}


