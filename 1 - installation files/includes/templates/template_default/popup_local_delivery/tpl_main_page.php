<?php
/*
 * tpl_main_page.php
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_main_page.php 2870 2006-01-21 21:36:02Z birdbrain $
 */

$slots = zen4All_getTimeSlots();
$taxRate = zen_get_tax_rate(MODULE_SHIPPING_LOCAL_DELIVERY_TAX_CLASS);
?>
<body id="popupLocalDelivery">
  <?php
  if ($_POST['act'] == "save_time") {
    $gridLocation = $_POST['selectDeliveryTime'];
    $_SESSION['DeliveryTime'] = array(
      'gridLocation' => $_POST['selectDeliveryTime'],
      'date' => $_POST['grid'][$gridLocation]['date'],
      'timeSlotId' => $_POST['grid'][$gridLocation]['timeSlotId'],
      'cost' => $_POST['grid'][$gridLocation]['cost']
    );
    ?>
  <script type="text/javascript">
      window.close();
    </script>
    <?php
  }
  if (isset($_SESSION['DeliveryTime'])) {
    $selectedTimeSlot = $_SESSION['DeliveryTime']['gridLocation'];
  }
  ?>
  <script type="text/javascript">
    function valid()
    {
      var i, selOption;
      selOption = -1;
      for (i = document.localDeliveryForm.selectDeliveryTime.length - 1; i > -1; i--) {
        if (document.localDeliveryForm.selectDeliveryTime[i].checked) {
          selOption = i;
        }
      }
      if (selOption == -1) {
        alert("<?php echo ALERT_SELECT_TIME_SLOT ; ?>");
        return false;
      }
      return true;
    }
  </script>
  <h1><?php echo HEADING_LOCAL_DELIVERY ?></h1>
  <table>
    <tr>
      <td>
        <table>
          <tr>
            <td style="vertical-align: top;">
              <?php echo zen_draw_form('localDeliveryForm', '', 'post', 'onSubmit="return valid();"'); ?>
              <?php echo zen_draw_hidden_field('act', 'save_time'); ?>
              <table id="localDelivery" border="1">
                <tr class="lightGrey">
                  <td>&nbsp;</td>
                  <?php
                  for ($i = 0, $n = sizeof($slots); $i < $n; $i++) {
                    ?>
                    <td class="main alignCenter"><?php echo $slots[$i]['slot']; ?></td>
                    <?php
                  }
                  ?>
                </tr>
                <?php
                for ($i = 0; $i < MODULE_SHIPPING_LOCAL_DELIVERY_DAYS_AHEAD; $i++) {
                  $timestamps = strtotime("+$i day");
                  $weekDayId = date('N', $timestamps);
                  ?>
                  <tr>
                    <td class="days lightGrey">
                      <?php echo '<span class="back">' . date('l', $timestamps) . '</span><span class="forward alignRight"> - ' . date('j F', $timestamps) . '</span>'; ?>
                    </td>
                    <?php
                    for ($j = 0, $m = sizeof($slots); $j < $m; $j++) {
                      $specialDeliveryTimeSlots = zen4All_getSpecialTime(date('Y-m-d', $timestamps), $slots[$j]['id']);
                      $bookedNumTimeSlots = zen4All_getTotalCount(date('Y-m-d', $timestamps), $slots[$j]['id']);
                      if ($specialDeliveryTimeSlots != 0) {
                        //Special delivery time
                        if ($specialDeliveryTimeSlots['special_max_limit'] == 0) {
                          //Fully Blocked
                          ?>
                          <td class="alignCenter">&nbsp;</td>
                          <?php
                        } else if ($bookedNumTimeSlots == $specialDeliveryTimeSlots['special_max_limit']) {
                          ?>
                          <td class="alignCenter">&nbsp;</td>
                          <?php
                        } else {
                          ?>
                          <td class="alignCenter" style="background-color:#d4ffbd;">
                            <?php
                            echo zen_draw_radio_field('selectDeliveryTime', $i . '_' . $j, ($selectedTimeSlot == $i . '_' . $j) ? true : false, 'class="selectDeliveryTime"');
                            echo zen_draw_hidden_field('grid[' . $i . '_' . $j . '][date]', date('Y-m-d', $timestamps));
                            echo zen_draw_hidden_field('grid[' . $i . '_' . $j . '][timeSlotId]', $specialDeliveryTimeSlots['slot_id']);
                            echo zen_draw_hidden_field('grid[' . $i . '_' . $j . '][cost]', $specialDeliveryTimeSlots['special_cost']);
                            ?>
                            <br>
                            <label>
                              <?php
                              echo $currencies->format(zen_add_tax($specialDeliveryTimeSlots['special_cost'], $taxRate));
                              ?>
                            </label>
                          </td>
                          <?php
                        }
                      } else {
                        //Get from default table
                        $standardDeliveryTimeSlots = zen4All_getDefaultTime($weekDayId, $slots[$j]['id']);
                        if ($standardDeliveryTimeSlots['max_limit'] == 0) { //Fully Blocked
                          ?>
                          <td class="alignCenter">&nbsp;</td>
                          <?php
                        } else if ($bookedNumTimeSlots == $standardDeliveryTimeSlots['max_limit']) {
                          ?>
                          <td class="alignCenter">&nbsp;</td>
                          <?php
                        } else {
                          ?>
                          <td class="alignCenter" style="background-color:#d4ffbd;">
                            <?php
                            echo zen_draw_radio_field('selectDeliveryTime', $i . '_' . $j, ($selectedTimeSlot == $i . '_' . $j) ? true : false, 'class="selectDeliveryTime"');
                            echo zen_draw_hidden_field('grid[' . $i . '_' . $j . '][date]', date('Y-m-d', $timestamps));
                            echo zen_draw_hidden_field('grid[' . $i . '_' . $j . '][timeSlotId]', $standardDeliveryTimeSlots['slot_id']);
                            echo zen_draw_hidden_field('grid[' . $i . '_' . $j . '][cost]', $standardDeliveryTimeSlots['cost']);
                            ?>
                            <br>
                            <label>
                              <?php echo $currencies->format(zen_add_tax($standardDeliveryTimeSlots['cost'], $taxRate)); ?>
                            </label>
                          </td>
                          <?php
                        }
                      } //end outer else
                    } //end for
                    ?>
                  </tr>
                  <?php
                } //end for
                ?>
              </table>
              <br>
              <div class="alignCenter">
                <?php
                echo zen_draw_hidden_field('gridLocation', $selectedTimeSlot);
                echo zen_image_submit('button_submit.gif', 'Select & Close'); ?>&nbsp; <?php echo zen_image_button('button_cancel.gif', BUTTON_CANCEL_ALT, 'onClick="window.close();"') ?>
              </div>
              </form>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>