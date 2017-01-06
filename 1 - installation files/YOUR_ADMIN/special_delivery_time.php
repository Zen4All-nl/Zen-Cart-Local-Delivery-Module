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
      $id = zen_db_prepare_input($_POST['cID']);
      $deliveryDate = $_POST['d_date_year'] . "-" . $_POST['d_date_month'] . "-" . $_POST['d_date_day'];
      $slotId = zen_db_prepare_input($_POST['slotid']);
      $cost = zen_db_prepare_input($_POST['special_cost']);
      $max = zen_db_prepare_input($_POST['special_max_limit']);

      $sql_data_array = array(
        'slot_id' => $slotId,
        'special_cost' => $cost,
        'special_max_limit' => $max
      );
      if ($action == 'insert') {
        $insert_sql_data = array('special_delivery_date' => $deliveryDate);
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
        zen_db_perform(TABLE_DEFAULT_DELIVERY_TIME, $sql_data_array);
      } elseif ($action == 'save') {
        zen_db_perform(TABLE_SPECIAL_DELIVERY_TIME, $sql_data_array, 'update', "id = '" . (int)$id . "'");
      }

      zen_redirect(zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&cID=' . (int)$id));
      break;
    case 'deleteconfirm':
      $id = zen_db_prepare_input((int)$_POST['cID']);

      $db->Execute("DELETE FROM " . TABLE_SPECIAL_DELIVERY_TIME . "
                    WHERE id = '" . zen_db_input($id) . "'");
      zen_redirect(zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page']));
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
                <table width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                    <td class="pageHeading right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
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
                          <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DATE; ?></td>
                          <td class="dataTableHeadingContent right"><?php echo TABLE_HEADING_SLOT; ?></td>
                          <td class="dataTableHeadingContent right"><?php echo TABLE_HEADING_COST; ?></td>
                          <td class="dataTableHeadingContent right"><?php echo TABLE_HEADING_MAX_LIMIT; ?></td>
                          <td class="dataTableHeadingContent right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                        </tr>
                        <?php
                        $delivery_time_query_raw = "SELECT *
                                                    FROM " . TABLE_SPECIAL_DELIVERY_TIME . " sdt
                                                    INNER JOIN " . TABLE_TIME_SLOTS . " ts ON sdt.slot_id = ts.slot_id
                                                    ORDER BY sdt.special_delivery_date, ts.slot, sdt.slot_id ASC";

                        $delivery_time_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $delivery_time_query_raw, $delivery_time_query_numrows);
                        $delivery_time = $db->Execute($delivery_time_query_raw);
                        while (!$delivery_time->EOF) {
                          if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $delivery_time->fields['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                            $cInfo = new objectInfo($delivery_time->fields);
                          }

                          if (isset($cInfo) && is_object($cInfo) && ($delivery_time->fields['id'] == $cInfo->id)) {
                            echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&cID=' . (int)$cInfo->id . '&action=edit') . '\'">' . "\n";
                          } else {
                            echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&dayid=' . (int)$_GET['dayid'] . '&cID=' . (int)$delivery_time->fields['id']) . '\'">' . "\n";
                          }
                          ?>

                          <td class="dataTableContent"><?php echo $delivery_time->fields['special_delivery_date']; ?></td>
                          <td class="dataTableContent right"><?php echo $delivery_time->fields['slot']; ?></td>
                          <td class="dataTableContent right"><?php echo $delivery_time->fields['special_cost']; ?></td>
                          <td class="dataTableContent right"><?php echo $delivery_time->fields['special_max_limit']; ?></td>
                          <td class="dataTableContent right">
                            <?php
                            if (isset($cInfo) && is_object($cInfo) && ($delivery_time->fields['id'] == $cInfo->id)) {
                              echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                            } else {
                              echo '<a href="' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$delivery_time->fields['id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
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
                    <td colspan="5">
                      <table width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                          <td class="smallText" valign="top"><?php echo $delivery_time_split->display_count($delivery_time_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, (int)$_GET['page'], TEXT_DISPLAY_NUMBER_OF_DELIVERY_TIME); ?></td>
                          <td class="smallText right"><?php echo $delivery_time_split->display_links($delivery_time_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, (int)$_GET['page']); ?></td>
                        </tr>
                        <?php
                        if (empty($action)) {
                          ?>
                          <tr>
                            <td colspan="3" class="right"><?php echo '<a href="' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&action=new') . '">' . zen_image_button('button_insert.gif', IMAGE_NEW_DELIVERY_TIME_BUT) . '</a>'; ?></td>
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
              //    $d_date = split("[-]", date('Y-m-d'));
                  $d_date = preg_split('/[-]/', date('Y-m-d'));
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SPECIAL_TIME . '</b>');

                  $contents = array('form' => zen_draw_form('delivery', FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&action=insert'));
                  $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
                  $contents[] = array('text' => '<br>' . TABLE_HEADING_DATE . '<br>' . zen_draw_date_selector('d_date', mktime(0, 0, 0, $d_date[1], $d_date[2], $d_date[0])));
                  $contents[] = array('text' => '<br>' . TABLE_HEADING_SLOT . '<br>' . zen_draw_pull_down_menu('slot_id', zen4All_getTimeSlots()));
                  $contents[] = array('text' => '<br>' . TABLE_HEADING_COST . '<br>' . zen_draw_input_field('cost'));
                  $contents[] = array('text' => '<br>' . TABLE_HEADING_MAX_LIMIT . '<br>' . zen_draw_input_field('max_limit', '', '', '', 'number'));
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_insert.gif', IMAGE_INSERT) . ' <a href="' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page']) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                case 'edit':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_DELIVERY . '</b>');

                  $contents = array('form' => zen_draw_form('delivery', FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&day_id=' . (int)$_GET['day_id'] . '&cID=' . (int)$cInfo->id . '&action=save'));
                  $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
                  $contents[] = array('text' => '<br>' . TABLE_HEADING_DATE . '<br>' . $cInfo->special_delivery_date);
                  $contents[] = array('text' => '<br>' . TABLE_HEADING_SLOT . '<br>' . $cInfo->slot);
                  $contents[] = array('text' => '<br>' . TABLE_HEADING_COST . '<br>' . zen_draw_input_field('special_cost', $cInfo->special_cost));
                  $contents[] = array('text' => '<br>' . TEXT_INFO_EDIT_MAX_LIMIT . '<br>' . zen_draw_input_field('special_max_limit', $cInfo->special_max_limit, '', '', 'number'));
                  $contents[] = array('text' => zen_draw_hidden_field('id', (int)$_GET['cID']));
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                case 'delete':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_DELIVERY_TIME . '</b>');

                  $contents = array('form' => zen_draw_form('delivery', FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&cID=' . (int)$cInfo->id . '&action=deleteconfirm'));
                  $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                  $contents[] = array('text' => '<br><b>' . TABLE_HEADING_DATE . ':' . $cInfo->special_delivery_date . '</b>');
                  $contents[] = array('text' => '<br><b>' . TABLE_HEADING_SLOT . ':' . $cInfo->slot . '</b>');
                  $contents[] = array('text' => zen_draw_hidden_field('id', (int)$_GET['cID']));
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&cID=' . (int)$cInfo->id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                default:
                  if (isset($cInfo) && is_object($cInfo)) {
                    $heading[] = array('text' => '<b>' . $cInfo->special_delivery_date . '(' . $cInfo->slot . ')</b>');

                    $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&cID=' . (int)$cInfo->id . '&action=edit') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . zen_href_link(FILENAME_SPECIAL_DELIVERY_TIME, 'page=' . (int)$_GET['page'] . '&cID=' . (int)$cInfo->id . '&action=delete') . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
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