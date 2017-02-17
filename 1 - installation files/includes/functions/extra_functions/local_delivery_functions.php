<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Returns Delivery time slots
function zen4All_getTimeSlots() {
  global $db;
  $timeSlots = $db->Execute("SELECT slot_id, slot
                             FROM " . TABLE_TIME_SLOTS . "
                             WHERE 1
                             ORDER BY slot");
  while (!$timeSlots->EOF) {
    $timeSlotsArray[] = array(
      'id' => $timeSlots->fields['slot_id'],
      'slot' => $timeSlots->fields['slot']
    );
    $timeSlots->MoveNext();
  }
  return $timeSlotsArray;
}

///For Delivery Details
function zen4All_getSlot($slotId) {
  global $db;
  $tot_res = $db->Execute("SELECT slot
                           FROM " . TABLE_TIME_SLOTS . "
                           WHERE slot_id = $slotId");
  return $tot_res->fields['slot'];
}

//Returns total count by date & time slot 
function zen4All_getTotalCount($date, $slotid) {
  global $db;
  $total = $db->Execute("SELECT COUNT(*) AS num
                         FROM " . TABLE_ORDERS_DELIVERY_TIME . "
                         WHERE delivery_date = '" . $date . "'
                         AND delivery_time_slot_id = " . $slotid);
  return $total->fields['num'];
}

//Returns max_limit & cost if special time exists for a particular date & slot
function zen4All_getSpecialTime($date, $slotid) {
  global $db;
  $specialTime = $db->Execute("SELECT *
                               FROM " . TABLE_SPECIAL_DELIVERY_TIME . "
                               WHERE special_delivery_date = '" . $date . "'
                               AND slot_id = " . $slotid);
  if ($specialTime->RecordCount() > 0) {
    return $specialTime->fields;
  } else {
    return 0;
  }
}

//Returns max_limit & cost from default table

function zen4All_getDefaultTime($dayId, $slotId) {
  global $db;
  $defaultTime = $db->Execute("SELECT *
                               FROM ". TABLE_DEFAULT_DELIVERY_TIME . "
                               WHERE day_id = ". $dayId . "
                               AND slot_id = ". $slotId);
  return $defaultTime->fields;
}
