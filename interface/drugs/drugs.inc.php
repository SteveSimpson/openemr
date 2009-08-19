<?php
 // Copyright (C) 2006, 2008 Rod Roark <rod@sunsetsystems.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 //

 // Modified 7-2009 by BM in order to migrate using the form,
 // unit, route, and interval lists with the
 // functions in openemr/library/options.inc.php .
 // These lists are based on the constants found in the 
 // openemr/library/classes/Prescription.class.php file.

 $substitute_array = array('', xl('Allowed'), xl('Not Allowed'));

function send_drug_email($subject, $body) {
  require_once ($GLOBALS['srcdir'] . "/classes/class.phpmailer.php");
  $recipient = $GLOBALS['practice_return_email_path'];
  if (empty($recipient)) return;
  $mail = new PHPMailer();
  $mail->SetLanguage("en", $GLOBALS['fileroot'] . "/library/" );
  $mail->From = $recipient;
  $mail->FromName = 'In-House Pharmacy';
  $mail->isMail();
  $mail->Host = "localhost";
  $mail->Mailer = "mail";
  $mail->Body = $body;
  $mail->Subject = $subject;
  $mail->AddAddress($recipient);
  if(!$mail->Send()) {
    error_log("There has been a mail error sending to " . $recipient .
      " " . $mail->ErrorInfo);
  }
}

function sellDrug($drug_id, $quantity, $fee, $patient_id=0, $encounter_id=0,
  $prescription_id=0, $sale_date='', $user='') {

  if (empty($patient_id))   $patient_id   = $GLOBALS['pid'];
  if (empty($sale_date))    $sale_date    = date('Y-m-d');
  if (empty($user))         $user         = $_SESSION['authUser'];

  // Find and update inventory, deal with errors.
  //
  $res = sqlStatement("SELECT * FROM drug_inventory WHERE " .
    // "drug_id = '$drug_id' AND on_hand > 0 AND destroy_date IS NULL " .
    "drug_id = '$drug_id' AND destroy_date IS NULL " .
    "ORDER BY expiration, inventory_id");
  $rowsleft = mysql_num_rows($res);
  $bad_lot_list = '';
  while ($row = sqlFetchArray($res)) {
    if ((empty($row['expiration']) || $row['expiration'] > $sale_date) && $row['on_hand'] >= $quantity) {
      break;
    }
    if ($row['on_hand'] > 0) {
      $tmp = $row['lot_number'];
      if (! $tmp) $tmp = '[missing lot number]';
      if ($bad_lot_list) $bad_lot_list .= ', ';
      $bad_lot_list .= $tmp;
    }
    if (! --$rowsleft) break; // to retain the last $row
  }

  if ($bad_lot_list) {
    send_drug_email("Lot destruction needed",
      "The following lot(s) are expired or too small to fill the order for " .
      "patient $patient_id and should be destroyed: $bad_lot_list\n");
  }

  if (! $row) return 0; // No undestroyed lots exist

  $inventory_id = $row['inventory_id'];

  sqlStatement("UPDATE drug_inventory SET " .
    "on_hand = on_hand - $quantity " .
    "WHERE inventory_id = $inventory_id");

  $rowsum = sqlQuery("SELECT sum(on_hand) AS sum FROM drug_inventory WHERE " .
    "drug_id = '$drug_id' AND on_hand > '$quantity' AND " .
    "( expiration IS NULL OR expiration > CURRENT_DATE )");
  $rowdrug = sqlQuery("SELECT * FROM drugs WHERE " .
    "drug_id = '$drug_id'");
  if ($rowsum['sum'] <= $rowdrug['reorder_point']) {
    send_drug_email("Product re-order required",
      "Product '" . $rowdrug['name'] . "' has reached its reorder point.\n");
  }

  // TBD: Maybe set and check a reorder notification date so we don't
  // send zillions of redundant emails.

  return sqlInsert("INSERT INTO drug_sales ( " .
    "drug_id, inventory_id, prescription_id, pid, encounter, user, " .
    "sale_date, quantity, fee ) VALUES ( " .
    "'$drug_id', '$inventory_id', '$prescription_id', '$patient_id', " .
    "'$encounter_id', '$user', '$sale_date', '$quantity', '$fee' )");
}
?>