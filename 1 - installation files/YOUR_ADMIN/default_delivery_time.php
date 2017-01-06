<?php
/**
 * @package admin
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: orders_status.php 19330 2011-08-07 06:32:56Z drbyte $
 */
require('includes/application_top.php');
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (zen_not_null($action)) {
  switch ($action) {
    case 'insert':
    case 'save':
      $defaultId = zen_db_prepare_input((int)$_POST['cID']);
      $dayId = zen_db_prepare_input((int)$_POST['day_id']);
      $slotId = zen_db_prepare_input((int)$_POST['slot_id']);
      $cost = zen_db_prepare_input($_POST['cost']);
      $max = zen_db_prepare_input($_POST['max']);

      $sql_data_array = array(
        'slot_id' => $slotId,
        'cost' => $cost,
        'max_limit' => $max
      );
      if ($action == 'insert') {
        $insert_sql_data = array('day_id' => $dayId);
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
        zen_db_perform(TABLE_DEFAULT_DELIVERY_TIME, $sql_data_array);
      } elseif ($action == 'save') {
        zen_db_perform(TABLE_DEFAULT_DELIVERY_TIME, $sql_data_array, 'update', "default_id = '" . (int)$defaultId . "'");
      }

      zen_redirect(zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$dayId . '&cID=' . (int)$defaultid));
      break;
    case 'deleteconfirm':
      $defaultId = zen_db_prepare_input((int)$_POST['cID']);
      $dayId = zen_db_prepare_input((int)$_POST['day_id']);
      $db->Execute("DELETE FROM " . TABLE_DEFAULT_DELIVERY_TIME . "
                    WHERE default_id = '" . zen_db_input($defaultId) . "'");
      zen_redirect(zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$dayId));
      break;
    case 'delete':
      break;
  }
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script language="javascript" src="includes/menu.js"></script>
    <script language="javascript" src="includes/general.js"></script>
    <script type="text/javascript">
      <!--
      function init()
      {
        cssjsmenu('navbar');
        if (document.getElementById)
        {
          var kill = document.getElementById('hoverJS');
          kill.disabled = true;
        }
      }
      // -->
    </script>
  </head>
  <body onload="init()">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->
    <table width="100%" cellspacing="2" cellpadding="2">
      <tr>
        <!-- body_text //-->
        <td width="100%" valign="top">
          <table width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td>
                <table  width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                    <td class="pageHeading right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                  </tr>
                  <tr>
                    <td colspan="2" class="smallText">
                      <?php
                      echo zen_draw_form('selectDay', FILENAME_DEFAULT_DELIVERY_TIME, '', 'get');
                      echo HEADING_TITLE_SELECT_DAY . ' ' . zen_draw_pull_down_menu('day_id', zen4All_getWeekDays(), (int)$_GET['day_id'], 'onChange="this.form.submit();"');
                      echo '</form>';
                      ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <table width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td valign="top">
                      <table width="100%" cellspacing="0" cellpadding="2">
                        <tr class="dataTableHeadingRow">
                          <td class="dataTableHeadingContent right"><?php echo TABLE_HEADING_SLOT; ?></td>
                          <td class="dataTableHeadingContent right"><?php echo TABLE_HEADING_COST; ?></td>
                          <td class="dataTableHeadingContent right"><?php echo TABLE_HEADING_MAX_LIMIT; ?></td>
                          <td class="dataTableHeadingContent right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                        </tr>
                        <?php
                        if ((int)$_GET['day_id'] == "") {
                          $delivery_time_query_raw = "SELECT ddt.default_id, ddt.day_id, ddt.slot_id, ddt.cost, ddt.max_limit,
                                                             ts.slot_id, ts.slot
                                                      FROM " . TABLE_DEFAULT_DELIVERY_TIME . " ddt
                                                      INNER JOIN " . TABLE_TIME_SLOTS . " ts ON ddt.slot_id = ts.slot_id
                                                      WHERE ddt.day_id = 1
                                                      ORDER BY ts.slot, ddt.slot_id ASC";
                        } else {
                          $delivery_time_query_raw = "SELECT ddt.default_id, ddt.day_id, ddt.slot_id, ddt.cost, ddt.max_limit,
                                                             ts.slot_id, ts.slot
                                                      FROM " . TABLE_DEFAULT_DELIVERY_TIME . " ddt
                                                      INNER JOIN " . TABLE_TIME_SLOTS . " ts ON ddt.slot_id = ts.slot_id
                                                      WHERE ddt.day_id = " . (int)$_GET['day_id'] . "
                                                      ORDER BY ts.slot, ddt.slot_id ASC";
                        }
                        $delivery_time_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $delivery_time_query_raw, $delivery_time_query_numrows);
                        $delivery_time = $db->Execute($delivery_time_query_raw);
                        while (!$delivery_time->EOF) {
                          if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $delivery_time->fields['default_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                            $cInfo = new objectInfo($delivery_time->fields);
                          }

                          if (isset($cInfo) && is_object($cInfo) && ($delivery_time->fields['default_id'] == $cInfo->default_id)) {
                            echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&cID=' . (int)$cInfo->default_id . '&action=edit') . '\'">' . "\n";
                          } else {
                            echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$delivery_time->fields['default_id']) . '\'">' . "\n";
                          }
                          ?>
                          <td class="dataTableContent right"><?php echo $delivery_time->fields['slot']; ?></td>
                          <td class="dataTableContent right"><?php echo $delivery_time->fields['cost']; ?></td>
                          <td class="dataTableContent right"><?php echo $delivery_time->fields['max_limit']; ?></td>
                          <td class="dataTableContent right">
                            <?php
                            if (isset($cInfo) && is_object($cInfo) && ($delivery_time->fields['default_id'] == $cInfo->default_id)) {
                              echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                            } else {
                              echo '<a href="' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$delivery_time->fields['default_id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                            }
                            ?>
                            &nbsp;
                          </td>
                    </tr>
                    <?php
                    $delivery_time->MoveNext();
                  }
                  ?>
                  <tr>
                    <td colspan="4">
                      <table width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                          <td class="smallText" valign="top"><?php echo $delivery_time_split->display_count($delivery_time_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, (int)$_GET['page'], TEXT_DISPLAY_NUMBER_OF_DELIVERY_TIME); ?></td>
                          <td class="smallText right"><?php echo $delivery_time_split->display_links($delivery_time_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, (int)$_GET['page']); ?></td>
                        </tr>
                        <?php
                        if (empty($action)) {
                          ?>
                          <tr>
                            <td colspan="2" class="right"><?php echo '<a href="' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&action=new') . '">' . zen_image_button('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
                          </tr>
                          <?php
                        }
                        ?>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
              <?php
              $heading = array();
              $contents = array();

              switch ($action) {
                case 'new':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_DELIVERY . '</b>');

                  $contents = array('form' => zen_draw_form('delivery', FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&action=insert'));
                  $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
                  $contents[] = array('text' => '<br>' . TEXT_INFO_EDIT_SLOT . '<br>' . zen_draw_pull_down_menu('slot_id', zen4All_getTimeSlots()));
                  $contents[] = array('text' => '<br>' . TEXT_INFO_EDIT_COST . '<br>' . zen_draw_input_field('cost'));
                  $contents[] = array('text' => '<br>' . TEXT_INFO_EDIT_MAX_LIMIT . '<br>' . zen_draw_input_field('max', '', '', '', 'number'));
                  $contents[] = array('text' => zen_draw_hidden_field('day_id', (int)$_GET['day_id']));
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_insert.gif', IMAGE_INSERT) . ' <a href="' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id']) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                case 'edit':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_DELIVERY . '</b>');

                  $contents = array('form' => zen_draw_form('delivery', FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$cInfo->default_id . '&action=save'));
                  $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
                  $contents[] = array('text' => '<br>' . TEXT_INFO_EDIT_SLOT . '<br>' . zen_draw_pull_down_menu('slot_id', zen4All_getTimeSlots(), $cInfo->slot_id));
                  $contents[] = array('text' => '<br>' . TEXT_INFO_EDIT_COST . '<br>' . zen_draw_input_field('cost', $cInfo->cost));
                  $contents[] = array('text' => '<br>' . TEXT_INFO_EDIT_MAX_LIMIT . '<br>' . zen_draw_input_field('max', $cInfo->max_limit, '', '', 'number'));
                  $contents[] = array('text' => zen_draw_hidden_field('default_id', (int)$_GET['cID']));
                  $contents[] = array('text' => zen_draw_hidden_field('day_id', $_GET['day_id']));
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$cInfo->default_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                case 'delete':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_DELIVERY_TIME . '</b>');

                  $contents = array('form' => zen_draw_form('delivery', FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&action=deleteconfirm') . zen_draw_hidden_field('cID', (int)$cInfo->default_id));
                  $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                  $contents[] = array('text' => '<br><b>' . $cInfo->slot . '</b>');
                  $contents[] = array('text' => zen_draw_hidden_field('default_id', (int)$_GET['cID']));
                  $contents[] = array('text' => zen_draw_hidden_field('day_id', (int)$_GET['day_id']));
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$cInfo->default_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                default:
                  if (isset($cInfo) && is_object($cInfo)) {
                    $heading[] = array('text' => '<b>' . $cInfo->slot . '</b>');

                    $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$cInfo->default_id . '&action=edit') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . zen_href_link(FILENAME_DEFAULT_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$cInfo->default_id . '&action=delete') . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
                  }
                  break;
              }

              if ((zen_not_null($heading)) && (zen_not_null($contents))) {
                echo '            <td width="25%" valign="top">' . "\n";

                $box = new box;
                echo $box->infoBox($heading, $contents);

                echo '            </td>' . "\n";
              }
              ?>
            </tr>
          </table></td>
      </tr>
    </table></td>
  <!-- body_text_eof //-->
</tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>