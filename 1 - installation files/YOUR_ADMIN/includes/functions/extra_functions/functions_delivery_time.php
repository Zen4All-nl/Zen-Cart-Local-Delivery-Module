<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Returns Delivery time slots
function zen4All_getTimeSlots() {
  global $db;
  $slot = $db->Execute("SELECT slot_id, slot
                              FROM " . TABLE_TIME_SLOTS . "
                              WHERE 1
                              ORDER BY slot, slot_id ASC");
  while (!$slot->EOF) {
    $slotArray[] = array(
      'id' => $slot->fields['slot_id'],
      'text' => $slot->fields['slot']
    );

    $slot->MoveNext();
  }
  return $slotArray;
}

function zen4All_getWeekDays() {
  $weekDays[] = array();
  $weekDays[0]['id'] = 1;
  $weekDays[0]['text'] = _MONDAY;
  $weekDays[1]['id'] = 2;
  $weekDays[1]['text'] = _TUESDAY;
  $weekDays[2]['id'] = 3;
  $weekDays[2]['text'] = _WEDNESDAY;
  $weekDays[3]['id'] = 4;
  $weekDays[3]['text'] = _THURSDAY;
  $weekDays[4]['id'] = 5;
  $weekDays[4]['text'] = _FRIDAY;
  $weekDays[5]['id'] = 6;
  $weekDays[5]['text'] = _SATURDAY;
  $weekDays[6]['id'] = 7;
  $weekDays[6]['text'] = _SUNDAY;
  return $weekDays;
}
