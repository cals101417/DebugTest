<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

function send_mail($reviewer_emails,
                  $cc1,
                  $cc2_emails,
                  $manual_cc,
                  $folder_name,
                  $title,
                  $file_new_name,
                  $date_uploaded,
                   $reviewer_assigned,
                  $approval_assigned,
                  $status,
                  $folder_id,
                  $subscriber,$new_file_names){

    $mail = new PHPMailer(true);
    $subscriber2 = "Fiafi Group";
    $cc2_string = '';
    try {
        if (count($cc2_emails)!= 0 ){
            foreach ($cc2_emails  as $cc2) {
                $mail->addCC($cc2);
                $mail->addAddress($cc2);
                $cc2_string =  $cc2_string.$cc2.'<br>';
            }
        }
        $review_addresses = '';
        foreach ($reviewer_emails as $reviewer_email) {

            $review_addresses = $review_addresses.$reviewer_email.'<br>';
        }
        $files = '';
    //    foreach ($file_name as $file) {
    //        $files  = $files.'<p>'.$file['src'].'</p>';
    //    }
        $reviewer_names = '';
        foreach ($reviewer_assigned as $name) {
            $reviewer_names = $reviewer_names.$name.'<br>';
        }

        $email_content2 =
        '
        <div style="width: 100%;">
            <h3>New Notification from '."$subscriber2".' - HSE Management System</h3>
            <table style=" font-family: arial, sans-serif;  border-collapse: collapse; width: 800">
        
                <tr >
                    <td height="30"  style="  text-align: left;">Folder : </td>
                   <td height="30"  style="padding: 8px; text-align: left;">'.$folder_name.'</td>
                </tr>
                <tr style=" background-color:">
                   <td height="30"  style="  text-align: left;">CC : </td>
                   <td height="30"  style=" " >
                        '.$cc1.'   '.$manual_cc.'   '.$cc2_string.'
                    </td>
                </tr>
        
               <td height="30"  style="  text-align: left;">Document : </td>
               <td height="30"  style=" ">'.$file_new_name.' </td>
                </tr>
                <tr style=" background-color:">
                   <td height="30"  style="  text-align: left;">Date Uploaded : </td>
                   <td height="30"  style=" ">'.$date_uploaded.'</td>
                </tr>
                <tr >
                   <td height="30"  style="  text-align: left;">Reviewal Assigned : </td>
                   <td height="30"  style="  ">'.$reviewer_names.'</td>
                </tr>
                <tr style=" background-color:">
                   <td height="30"  style="  text-align: left;">Approval Assigned : </td>
                   <td height="30"  style="  ">'.$approval_assigned.'</td>
                </tr>
                <tr>
                   <td height="30" >
                        <br>
                    </td>
                </tr>
                <tr>
                   <td height="30"  style="  text-align: left;  background-color: #FFFF00">Status</td>
                   <td height="30"  style=" background-color: #FFFF00">'.$status.'</td>
                </tr>
                <tr>
                    <td>
                    <a href="https://membersafety-surfers.com/document_files_specific.php?view='.$folder_id.'">Open Link <a/>
                    </td>
                </tr>
            </table>
        </div>';

        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;   // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true;  // authentication enabled
        $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
        $mail->SMTPAutoTLS = false;
        $mail->Host = 'smtp.office365.com';
        $mail->Port = 587;
        $mail->Username = "Integrated_Management@outlook.com";
        $mail->Password = "Management10";
        $mail->Subject = "Management System Notification";
        $mail->setFrom("Integrated_Management@outlook.com");
    //$mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";}; //$mail->Debugoutput = 'echo';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
            foreach ($reviewer_emails as $reviewer_email) {
                $mail->addAddress($reviewer_email);
            }
        $mail->isHTML(true);
        $mail->Body = $email_content2;
        if($manual_cc != ''){
            $mail->addAddress($manual_cc);
            $mail->addCC($manual_cc);
        }

//        foreach($new_file_names as $file_name){
//            $mail->addAttachment("../assets/media/docs/".$file_name);
//        }
        if(!$mail->Send()) {
            $message = 'Mail error: '.$mail->ErrorInfo;
            return false;
        } else {
            $message = 'Message sent!';
            return true;
        }
        $mail->smtpClose();

    } catch (Exception $e) {
        echo $e;
        echo 'Mail error: '.$mail->ErrorInfo;
    }
}
?>
