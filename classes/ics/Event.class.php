<?php namespace ics;

class Event extends Block {
  protected function getType() {
    return 'VEVENT';
  }

  protected function getPropertyConfig() {
    return [
      'dtstart' => ['required' => false, 'max' => 1, 'type' => 'DateTime'],
      'dtend' => ['required' => false, 'max' => 1, 'type' => 'DateTime'],
      'dtstamp' => ['required' => false, 'max' => 1, 'type' => 'DateTime'],
      'uid' => ['required' => false, 'max' => 1, 'type' => 'Text'],
      'created' => ['required' => false, 'max' => 1, 'type' => 'DateTime'],
      'description' => ['required' => false, 'max' => 1, 'type' => 'Text'],
      'last-modified' => ['required' => false, 'max' => 1, 'type' => 'DateTime'],
      'location' => ['required' => false, 'max' => 1, 'type' => 'Text'],
      'sequence' => ['required' => false, 'max' => 1, 'type' => 'Text'],
      'status' => ['required' => false, 'max' => 1, 'type' => 'Text'],
      'summery' => ['required' => false, 'max' => 1, 'type' => 'Text'],
      'transp' => ['required' => false, 'max' => 1, 'type' => 'Text'],
    ];
  }

  protected function getBlockConfig() {
    return [];
  }
}


