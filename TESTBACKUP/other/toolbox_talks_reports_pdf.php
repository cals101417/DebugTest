<?php

require('assets/fpdf/fpdf.php');
$w2 = array(15, 20,20 ,20, 20,20, 20,20,20,20,20,20,40,40,40,40);
Class PDF extends FPDF{

    function Header(){
        $dept = $_GET['dept'];
        // Logo
        $month = sprintf("%02d",$_GET['month']);
        $year = $_GET['year'];
        $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

        $dept_array = ['CIVILS','ELECTRICALS','MECHANICALS','CAMPS','OFFICES','ALL'];
        $this->Image('assets/media/favicons/Fiafi logo.png',30,10,40);
        // Arial bold 15
        $this->SetFont('Arial','B',20,10,20);
        // Move to the right
        $this->Cell("110");
        // Title
        $this->Cell(30,18,$months[number_format($month)-1]." ".$_GET['year']." TOOLBOX TALKS REPORTS - ".$dept_array[$dept-1],0,0,'A');
        // Line break
        $this->Ln(20);
    }
    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        $this->SetX(300);
        // Select Arial italic 8
        $this->SetFont('Arial','I',8);
        // Print current and total page numbers
        $this->Cell(0,15,'Page '.$this->PageNo().'',0,0,'C');
        $this->SetY(-15);
        $this->SetX(-590);
        // Select Arial italic 8
        $this->SetFont('Arial','I',8);
        // Print current and total page numbers
        $this->Cell(0,15,'UNCONTROLLED COPY IF PRINTED',0,0,'C');
    }


// Colored table
    function FancyTable(){
        // Colors, line width and bold font
        require_once 'session.php';
        $this->SetFillColor(0,112, 192);
        $this->SetTextColor(255);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.2);
        $this->SetFont('','B');
        $this->SetLeftMargin(10);
        // Header
        $month = sprintf("%02d",$_GET['month']);
        $dept = $_GET['dept'];
        $year = $_GET['year'];

        $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec");
        $w2 = array(40,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10);
        $border ='';
        $this->Cell(10,7,"#",$border,0,'C',true);
        $this->Cell($w2[0],7,$months[$month-1]." ".$year,$border,0,'C',true);
        for($i=1;$i<= 31;$i++){
            $this->Cell($w2[$i],7,$i,$border,0,'C',true);
        }
        $this->Cell(20,7,"Total Day",$border,0,'C',true);
        $this->Cell(20,7,"Total TIME",$border,0,'C',true);

        $this->Ln();
        $this->SetFont('','');
        $this->SetTextColor(0,112, 192);
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        // Data
        $fill = false;
        $index = 0;

        try {

            $get_position_qry =  $conn->query("SELECT `position`,`position_id`  FROM `tbl_position`");
            $get_all_position = $get_position_qry->fetchAll();
            $index = 0;
            $over_all_total = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0  );
            $c = count($get_all_position);
            $over_all_total_days = 0;
            $over_all_total_time = 0;
            foreach($get_all_position as $position)
            {
                $position_id = $position['position_id'];
                $position_desc = $position['position'];
                $get_records_per_position_qry= '';
                if ($dept == 6){
                    $get_records_per_position_qry = $conn->query("SELECT `date_conducted`,
                                                                    count(tbl_toolbox_talks_participants.tbt_id) AS day_per_participants,
                                                                    sum(tbl_toolbox_talks_participants.time) AS time_per_participant,       
                                                                    tbl_position.position, tbl_employees.employee_id, 
                                                                    tbl_position.position_id
                                                                    FROM `tbl_toolbox_talks_participants`
                                                                    INNER JOIN `tbl_employees`  ON  tbl_toolbox_talks_participants.employee_id  =  tbl_employees.employee_id  
                                                                    INNER JOIN `tbl_position` ON tbl_employees.position = tbl_position.position_id 
                                                                    INNER JOIN  `tbl_toolbox_talks` ON tbl_toolbox_talks_participants.tbt_id = tbl_toolbox_talks.tbt_id
                                                                    WHERE  tbl_employees.position = $position_id 
                                                                               AND tbl_toolbox_talks.is_deleted = 0 
                                                                               AND YEAR ( tbl_toolbox_talks.date_conducted ) = $year AND  MONTH ( tbl_toolbox_talks.date_conducted ) = $month
                                                                    GROUP BY  DAY(date_conducted) ");
                } else {
                    $get_records_per_position_qry = $conn->query("SELECT `date_conducted`,
                                                                        count(tbl_toolbox_talks_participants.tbt_id) AS day_per_participants,
                                                                        sum(tbl_toolbox_talks_participants.time) AS time_per_participant,       
                                                                        tbl_position.position, tbl_employees.employee_id, 
                                                                        tbl_position.position_id
                                                                        FROM `tbl_toolbox_talks_participants`
                                                                        INNER JOIN `tbl_employees`  ON  tbl_toolbox_talks_participants.employee_id  =  tbl_employees.employee_id  
                                                                        INNER JOIN `tbl_position` ON tbl_employees.position = tbl_position.position_id 
                                                                        INNER JOIN  `tbl_toolbox_talks` ON tbl_toolbox_talks_participants.tbt_id = tbl_toolbox_talks.tbt_id
                                                                        WHERE  tbl_employees.position = $position_id 
                                                                                   AND tbl_toolbox_talks.is_deleted = 0 
                                                                                   AND tbl_toolbox_talks.tbt_type = $dept  
                                                                                   AND YEAR ( tbl_toolbox_talks.date_conducted ) = $year AND  MONTH ( tbl_toolbox_talks.date_conducted ) = $month
                                                                        GROUP BY  DAY(date_conducted) ");
                }

                $get_position_results = $get_records_per_position_qry->fetchAll();

                $total_days = 0;
                $total_time = 0;
                $this->Cell(10,5,$position_id,'LR',0,'L',$fill);
                $this->Cell($w2[0],5,$position_desc,'LR',0,'L',$fill);
                $current_total = 0;  // temporarily store total per date.

                for ($i = 1; $i <=31; $i++) {
                    $cell_display = 0;
                    $time_per_day = 0;
                    foreach($get_position_results as $item){

                        $get_day = explode("-",$item['date_conducted']);
                        $get_day = explode(" ",$get_day[2]);
                        $get_calendar_day = $get_day[0];
                        if (sprintf("%02d",$i) == $get_calendar_day){
                            $cell_display = $item['day_per_participants'];
                            $time_per_day = $item['time_per_participant'];
                            $total_days = $total_days+ $item['day_per_participants'];
                            $total_time = $total_time+ $item['time_per_participant'];
                            break;
                        }

                    }
//                    echo $td_display;
                    $over_all_total[$i-1] = $over_all_total[$i-1] + $time_per_day;

                    $this->Cell($w2[$i+1],5,$cell_display,'LR',0,'C',$fill);
                }

                $this->Cell(20,5,$total_days,'LR',0,'C',$fill);
                $this->Cell(20,5,$total_time,'LR',0,'C',$fill);
                $over_all_total_time = $over_all_total_time + $total_time;
                $over_all_total_days = $over_all_total_days + $total_days;
                $this->Ln();
                $fill = !$fill;
            }

//            DISPLAY OVER ALL RESULTS
            $this->Cell(10,5,$c+1,'LR',0,'L',$fill);
            $this->Cell(40,5,"TOTAL HOURS",'LR',0,'L',$fill);

            for ($i = 0; $i < count($over_all_total);$i++) {
                $this->Cell($w2[$i+1],5,$over_all_total[$i],'LR',0,'C',$fill);
            }

            $this->Cell(20,5,$over_all_total_days,'LR',0,'C',$fill);
            $this->Cell(20,5,$over_all_total_time,'LR',0,'C',$fill);
            $this->Ln();
            // Closing line
            $this->Cell(array_sum($w2)-120,0,'','T');
        } catch (Exception $e){
            echo $e;
        }
    }
}
$pdf = new PDF();
// Column headings


$pdf->SetFont('Arial','',10);
$pdf->AddPage('L', 'A3');
$pdf->FancyTable();
$pdf->Output();

?>