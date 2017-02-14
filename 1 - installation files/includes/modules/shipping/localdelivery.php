<?php

/**
 * @package shippingMethod
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Sat Oct 17 22:52:38 2015 -0400 Modified in v1.5.5 $
 */
class localdelivery {

  var $code, $title, $description, $icon, $enabled;

// class constructor
  function __construct() {
    global $order, $db;

    $this->code = 'localdelivery';
    $this->title = MODULE_SHIPPING_LOCALDELIVERY_TEXT_TITLE;
    $this->description = MODULE_SHIPPING_LOCALDELIVERY_TEXT_DESCRIPTION;
    $this->sort_order = MODULE_SHIPPING_LOCALDELIVERY_SORT_ORDER;
    $this->icon = '';
    $this->tax_class = MODULE_SHIPPING_LOCALDELIVERY_TAX_CLASS;
    $this->tax_basis = MODULE_SHIPPING_LOCALDELIVERY_TAX_BASIS;

    // disable only when entire cart is free shipping
    if (zen_get_shipping_enabled($this->code)) {
      $this->enabled = ((MODULE_SHIPPING_LOCALDELIVERY_STATUS == 'True') ? true : false);
    }

    if (($this->enabled == true) && ((int)MODULE_SHIPPING_LOCALDELIVERY_ZONE > 0)) {
      $check_flag = false;
      $check = $db->Execute("SELECT zone_id
                             FROM " . TABLE_ZONES_TO_GEO_ZONES . "
                             WHERE geo_zone_id = '" . MODULE_SHIPPING_LOCALDELIVERY_ZONE . "'
                             AND zone_country_id = '" . $order->delivery['country']['id'] . "'
                             ORDER BY zone_id");

      while (!$check->EOF) {
        if ($check->fields['zone_id'] < 1) {
          $check_flag = true;
          break;
        } elseif ($check->fields['zone_id'] == $order->delivery['zone_id']) {
          $check_flag = true;
          break;
        }
        $check->MoveNext();
      }

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }
  }

// class methods
  function quote($method = '') {
    global $order;
    if (isset($_SESSION['DeliveryTime'])) {
      $shippingCost = $_SESSION['DeliveryTime']['cost'];
    } else {
      $shippingCost = 0;
    }
    $this->quotes = array(
      'id' => $this->code,
      'module' => MODULE_SHIPPING_LOCALDELIVERY_TEXT_TITLE,
      'methods' => array(array(
          'id' => $this->code,
          'title' => MODULE_SHIPPING_LOCALDELIVERY_TEXT_WAY,
          'cost' => $shippingCost)));
    if ($this->tax_class > 0) {
      $this->quotes['tax'] = zen_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
    }

    if (zen_not_null($this->icon)) {
      $this->quotes['icon'] = zen_image($this->icon, $this->title);
    }
    return $this->quotes;
  }

  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("SELECT configuration_value
                                   FROM " . TABLE_CONFIGURATION . "
                                   WHERE configuration_key = 'MODULE_SHIPPING_LOCALDELIVERY_STATUS'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }

  function install() {
    global $db;
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable Local Delivery Shipping', 'MODULE_SHIPPING_LOCALDELIVERY_STATUS', 'True', 'Do you want to offer local delivery?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Tax Class', 'MODULE_SHIPPING_LOCALDELIVERY_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'zen_get_tax_class_title', 'zen_cfg_pull_down_tax_classes(', now())");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Tax Basis', 'MODULE_SHIPPING_LOCALDELIVERY_TAX_BASIS', 'Shipping', 'On what basis is Shipping Tax calculated. Options are<br />Shipping - Based on customers Shipping Address<br />Billing Based on customers Billing address<br />Store - Based on Store address if Billing/Shipping Zone equals Store zone', '6', '0', 'zen_cfg_select_option(array(\'Shipping\', \'Billing\', \'Store\'), ', now())");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Shipping Zone', 'MODULE_SHIPPING_LOCALDELIVERY_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort Order', 'MODULE_SHIPPING_LOCALDELIVERY_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Number of days', 'MODULE_SHIPPING_LOCALDELIVERY_DAYS_AHEAD', '10', 'Set the number of days in to the future', 6, 0, now())");
  }

  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key like 'MODULE\_SHIPPING\_LOCALDELIVERY\_%'");
  }

  function keys() {
    return array('MODULE_SHIPPING_LOCALDELIVERY_STATUS', 'MODULE_SHIPPING_LOCALDELIVERY_TAX_CLASS', 'MODULE_SHIPPING_LOCALDELIVERY_TAX_BASIS', 'MODULE_SHIPPING_LOCALDELIVERY_ZONE', 'MODULE_SHIPPING_LOCALDELIVERY_DAYS_AHEAD', 'MODULE_SHIPPING_LOCALDELIVERY_SORT_ORDER');
  }
}