<?php
require_once 'session.php';
require('assets/fpdf/fpdf.php');

$range1 = $_POST['year'].'-01-01 00:00:00';
$range2 = $_POST['year'].'-12-31 00:00:00';
$selected_year = $_POST['year'];

// Column headings
$generate_report_qry = $conn->query("SELECT  *,COUNT(*),`date_conducted`,Year(date_conducted) as year,
                                    SUM(case when tbt_type='1' then time else 0 end) as total_hours_civils,
                                    SUM(case when tbt_type='2' then time else 0 end) as total_hours_electricals,
                                    SUM(case when tbt_type='3' then time else 0 end) as total_hours_mechanicals,
                                    SUM(case when tbt_type='4' then time else 0 end) as total_hours_camps,
                                    SUM(case when tbt_type='5' then time else 0 end) as total_time_office,       
                                    COUNT(case when tbt_type='1' then time else null end) as total_days_civils,
                                    COUNT(case when tbt_type='2' then time else null end) as total_days_electricals,
                                    COUNT(case when tbt_type='3' then time else null end) as total_days_mechanicals,  
                                    COUNT(case when tbt_type='4' then time else null end) as total_days_camps,
                                    COUNT(case when tbt_type='5' then time else null end) as total_days_office,       
                                    COUNT('tbtp_id') as totals_days,
                                    SUM(time) as totals_hours
                                FROM tbl_toolbox_talks 
                                INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                WHERE (`date_conducted`  BETWEEN '$range1' AND '$range2') AND tbl_toolbox_talks.is_deleted = 0
                                GROUP BY date_format(date_conducted, '%M') ORDER BY `date_conducted`");
$get_result= $generate_report_qry->fetchAll();



// ----------------------------------------------- ITDS ----------------------------------------------------------//
//   GET PREVIOUS YEAR OF THE SELECTED YEAR

$previous_year = explode("-",$range2);
$prev_year = ((strval($previous_year[0])-1).'-12-31 00:00:00');

$get_report_by_year = $conn->query("SELECT  *,COUNT(*),Year(date_conducted) as itd_year,  
                                            SUM(case when tbt_type='1' then time else 0 end) as total_hours_civils,
                                            SUM(case when tbt_type='2' then time else 0 end) as total_hours_electricals,
                                            SUM(case when tbt_type='3' then time else 0 end) as total_hours_mechanicals,
                                            SUM(case when tbt_type='4' then time else 0 end) as total_hours_camps,
                                            SUM(case when tbt_type='5' then time else 0 end) as total_hours_office,       
                                            COUNT(case when tbt_type='1' then time else null end) as total_days_civils,
                                            COUNT(case when tbt_type='2' then time else null end) as total_days_electricals,
                                            COUNT(case when tbt_type='3' then time else null end) as total_days_mechanicals,  
                                            COUNT(case when tbt_type='4' then time else null end) as total_days_camps,
                                            COUNT(case when tbt_type='5' then time else null end) as total_days_office,            
                                            COUNT('tbtp_id') as totals_days,    
                                        SUM(time) as totals_hours 
                                        FROM tbl_toolbox_talks                                         
                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                        WHERE (`date_conducted`  BETWEEN '2015-01-01 00:00:00' AND '$range2') AND tbl_toolbox_talks.is_deleted = 0
                                        GROUP BY date_format(date_conducted, '%Y') ORDER BY `date_conducted` DESC");

$get_result2= $get_report_by_year->fetchAll();

$get_last_row = end($get_result2);
$get_last_year2="";



if (!empty($get_last_row)){
    $get_last_year = $get_last_row['itd_year'];
    $get_last_year2 = intVal($get_last_year-1);
} else {

    $get_last_year2 = intVal($range2);
}

$get_manual_records_qry =$conn->query("SELECT  *         
                                                    FROM tbl_tds                                          
                                                    WHERE `itd_year` < $get_last_year2 AND is_deleted = 0
                                                    ORDER BY `itd_year` DESC
                                      ");


$get_total_per_year_qry = $conn->query("SELECT  *,COUNT(*),Year(date_conducted) as itd_year,  
                                            SUM(case when tbt_type='1' then time else 0 end) as total_hours_civils,
                                            SUM(case when tbt_type='2' then time else 0 end) as total_hours_electricals,
                                            SUM(case when tbt_type='3' then time else 0 end) as total_hours_mechanicals,
                                            SUM(case when tbt_type='4' then time else 0 end) as total_hours_camps,
                                            SUM(case when tbt_type='5' then time else 0 end) as total_hours_office,       
                                            COUNT(case when tbt_type='1' then time else null end) as total_days_civils,
                                            COUNT(case when tbt_type='2' then time else null end) as total_days_electricals,
                                            COUNT(case when tbt_type='3' then time else null end) as total_days_mechanicals,  
                                            COUNT(case when tbt_type='4' then time else null end) as total_days_camps,
                                            COUNT(case when tbt_type='5' then time else null end) as total_days_office,            
                                            COUNT('tbtp_id') as totals_days,    
                                            SUM(time) as totals_hours 
                                            FROM tbl_toolbox_talks                                         
                                            INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                            WHERE YEAR (`date_conducted`)  = $selected_year AND tbl_toolbox_talks.is_deleted = 0
                                            GROUP BY date_format(date_conducted, '%Y') ORDER BY `date_conducted` DESC");
$get_result3 = $get_total_per_year_qry->fetchAll();

//print_r($get_result3);

$get_manual_result = $get_manual_records_qry->fetchAll();
$array_itds  = array_merge($get_manual_result , $get_result2);

$index = 0;

// Column headings
//print_r($array_itds);/



$w2 = array(15, 20,20 ,20, 20,20, 20,20,20,20,20,20,40,40,40,40);
Class PDF extends FPDF{

    function Header(){
        // Logo
        $this->Image('assets/media/favicons/Fiafi logo.png',30,10,40);
        // Arial bold 15
        $this->SetFont('Arial','B',20,10,20);
        // Move to the right
        $this->Cell("110");
        // Title
        $this->Cell(30,18,$_POST['year']." TOOLBOX TALKS REPORTS",0,0,'A');
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
    function FancyTable($header,$header2,$header3, $data,$data2,$data3)
    {
        try {
            // Colors, line width and bold font
            $this->SetFillColor(0,112, 192);
            $this->SetTextColor(255);
            $this->SetDrawColor(0);
            $this->SetLineWidth(.2);
            $this->SetFont('','B');
            $this->SetLeftMargin(30);
            // Header
            $w = array(10, 20, 40, 40, 40,40,40,40,40,40);
            $border ='';
            for($i=0;$i<count($header);$i++){
                if ($i != 0 ) {
                    $border = 1;
                } else {
                    $border = 'LTR';
                }
                $this->Cell($w[$i+1],7,$header[$i],$border,0,'C',true);
            }
            $this->Ln();
            $this->SetFont('','');
            $this->SetTextColor(0,112, 192);
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $w2 = array(15, 20,20 ,20, 20,20, 20,20,20,20,20,20,40,40,40,40);
            $border ='';
            for($i=0;$i<count($header2);$i++) {
                if ($i != 0){
                    $this->SetFillColor(255,235,255);
                    $border = 1;
                } else {
                    $this->SetFillColor(0,112, 192);
                    $border = 'LBR';
                }
                $this->Cell($w2[$i + 1], 7, $header2[$i], $border, 0, 'C', true);
            }
            $this->Ln();
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('');
            // Data
            $fill = false;

          $month = array('January','February','March', 'April','May','June', 'July','August','September','October', 'November','December');
            $temporary_size = 40;
            $temporary_size2 = 180;

            // GET THE MONTHS WITH AVAILABLE DATA
            $available_months = array();
            foreach($data as $datum){

                $date =  $datum['date_conducted'];
                $selected_month = date("m",strtotime($date));
                array_push($available_months, $selected_month);
            }
         
            $index = 1;
            foreach($month as $row)
            {
                $select_index_of_current_month = "";
                $counter = 1;
                foreach($available_months as  $available_month){
                    if ($available_month == sprintf("%02d",$index)){

                        $select_index_of_current_month =   $counter;
                        break;
                    } else {
                        $select_index_of_current_month = "";
                    }
                    $counter++;
                }
//                echo $row." - ";
//                echo $select_index_of_current_month."<br>";

                // $temporary_size = $temporary_size+ 20;
                // $temporary_size2 = $temporary_size2+ 5;
              
               
                if ($select_index_of_current_month != ""){
                   
                    $this->Cell($w2[1] ,6,$row,'L',0,'L',$fill);
                    $this->Cell($w2[5] ,6,$data[$select_index_of_current_month-1]['total_days_civils'],'LR',0,'C',$fill);
                    $this->Cell($w2[6] ,6,$data[$select_index_of_current_month-1]['total_hours_civils'],'LR',0,'C',$fill);
                    $this->Cell($w2[7] ,6,$data[$select_index_of_current_month-1]['total_days_mechanicals'],'LR',0,'C',$fill);
                    $this->Cell($w2[8] ,6,$data[$select_index_of_current_month-1]['total_hours_mechanicals'],'LR',0,'C',$fill);
                    $this->Cell($w2[9] ,6,$data[$select_index_of_current_month-1]['total_days_electricals'],'LR',0,'C',$fill);
                    $this->Cell($w2[10],6,$data[$select_index_of_current_month-1]['total_hours_electricals'],'LR',0,'C',$fill);
                    $this->Cell($w2[9] ,6,$data[$select_index_of_current_month-1]['total_days_camps'],'LR',0,'C',$fill);
                    $this->Cell($w2[10] ,6,$data[$select_index_of_current_month-1]['total_hours_camps'],'LR',0,'C',$fill);
                    $this->Cell($w2[9] ,6,$data[$select_index_of_current_month-1]['total_days_office'],'LR',0,'C',$fill);
                    $this->Cell($w2[10] ,6,$data[$select_index_of_current_month-1]['total_hours_camps'],'LR',0,'C',$fill);
                    $this->Cell($w2[14] ,6,$data[$select_index_of_current_month-1]['totals_days'],'LR',0,'C',$fill);
                    $this->Cell($w2[14] ,6,$data[$select_index_of_current_month-1]['totals_hours'],'LR',0,'C',$fill);
                } else  {

                    $this->Cell($w2[1],6,$row,'LR',0,'L',$fill);
                    $this->Cell($w2[4],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[5],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[6],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[7],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[8],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[9],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[10],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[10],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[10],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[10],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[14],6,'0','LR',0,'C',$fill);
                    $this->Cell($w2[14],6,'0','LR',0,'C',$fill);

                }
                $index++;
                $this->Ln();
                $fill = !$fill;
            }

            $this->Cell($w2[1] ,6,"TOTAL",'LR',0,'L',$fill);
            $this->Cell($w2[5] ,6,$data3[0]['total_days_civils'],'LR',0,'C',$fill);
            $this->Cell($w2[6] ,6,$data3[0]['total_hours_civils'],'LR',0,'C',$fill);
            $this->Cell($w2[7] ,6,$data3[0]['total_days_mechanicals'],'LR',0,'C',$fill);
            $this->Cell($w2[8] ,6,$data3[0]['total_hours_mechanicals'],'LR',0,'C',$fill);
            $this->Cell($w2[9] ,6,$data3[0]['total_days_electricals'],'LR',0,'C',$fill);
            $this->Cell($w2[10],6,$data3[0]['total_hours_electricals'],'LR',0,'C',$fill);
            $this->Cell($w2[9] ,6,$data3[0]['total_days_camps'],'LR',0,'C',$fill);
            $this->Cell($w2[10],6,$data3[0]['total_hours_camps'],'LR',0,'C',$fill);
            $this->Cell($w2[9] ,6,$data3[0]['total_days_office'],'LR',0,'C',$fill);
            $this->Cell($w2[10],6,$data3[0]['total_hours_camps'],'LR',0,'C',$fill);
            $this->Cell($w2[14],6,$data3[0]['totals_days'],'LR',0,'C',$fill);
            $this->Cell($w2[14],6,$data3[0]['totals_hours'],'LR',0,'C',$fill);
            $this->Ln();
            $this->Cell(array_sum($w2)-95,0,'','B');

            $this->Ln(20);


            $this->SetFillColor(0,112, 192);
            $this->SetTextColor(255);
            $this->SetDrawColor(0);

            $this->SetFont('','B');
            // Header

            $w = array(10, 20, 40, 40, 40,40,40,40,40,40);
            $border = '';
            for($i=0;$i<count($header3);$i++){
                if ($i != 0){
                    $border = 1;
                } else {
                    $border  = 'LTR';
                }
                $this->Cell($w[$i+1],7,$header3[$i],$border,0,'C',true);

            }
            $this->Ln();

            $this->SetFont('','');
//        $this->SetTextColor(255,0,0);
            $this->SetTextColor(0);
            $this->SetFillColor(255,235,255);

            $w2 = array(15, 20,20 ,20, 20,20, 20,20,20,20,20,20,40,40,40,40);
            $boolean = 'true';
            $border = '';
            for($i=0;$i<count($header2);$i++) {
                if ($i != 0){
                    $this->SetFillColor(255,235,255);
                    $border = 1;
                } else {
                    $this->SetFillColor(0,112, 192);
                    $border = 'LBR';
                }
                $this->Cell($w2[$i+1], 7, $header2[$i], $border, 0, 'C', $boolean);
            }
            $this->Ln();
            $last_year ='';

//      ---------------------------------------      ITD ----------------------------------------------------//
            $days_civils = 0;
            $days_mechanicals = 0;
            $days_electricals = 0;
            $days_camps = 0;
            $days_office = 0;
            $hours_civils  = 0;
            $hours_mechanicals  = 0;
            $hours_electricals  = 0;
            $hours_camps  = 0;
            $hours_office  = 0;

            for ($i = 0; $i < count($data2); $i++ ){
                $days_civils =  $days_civils + $data2[$i]['total_days_civils'];
                $days_mechanicals = $days_mechanicals  + $data2[$i]['total_days_mechanicals'];
                $days_electricals = $days_electricals + $data2[$i]['total_days_electricals'];
                $days_camps = $days_camps + $data2[$i]['total_days_camps'];
                $days_office = $days_office + $data2[$i]['total_days_office'];
                $hours_civils  = $hours_civils + $data2[$i]['total_hours_civils'];
                $hours_mechanicals  = $hours_mechanicals + $data2[$i]['total_hours_mechanicals'];
                $hours_electricals  = $hours_electricals + $data2[$i]['total_hours_electricals'];
                $hours_camps  = $hours_camps + $data2[$i]['total_hours_camps'];
                $hours_office  = $hours_office  + $data2[$i]['total_hours_office'];
                $totals_hours = $hours_office + $hours_camps + $hours_electricals+ $hours_mechanicals+$hours_civils;
                $totals_days  =$days_office + $days_camps + $days_electricals+ $days_mechanicals+$days_civils;

                $this->Cell($w2[1],6,$data2[$i]['itd_year'],'LR',0,'L',$fill);
                $this->Cell($w2[5],6,$days_civils,'LR',0,'C',$fill);
                $this->Cell($w2[10],6,$hours_civils,'LR',0,'C',$fill);
                $this->Cell($w2[6],6,$days_mechanicals,'LR',0,'C',$fill);
                $this->Cell($w2[7],6,$hours_mechanicals,'LR',0,'C',$fill);
                $this->Cell($w2[8],6,$days_electricals,'LR',0,'C',$fill);
                $this->Cell($w2[9],6,$hours_electricals,'LR',0,'C',$fill);
                $this->Cell($w2[10],6,$days_camps,'LR',0,'C',$fill);
                $this->Cell($w2[9],6,$hours_camps,'LR',0,'C',$fill);
                $this->Cell($w2[10],6,$days_office,'LR',0,'C',$fill);
                $this->Cell($w2[9],6,$hours_office,'LR',0,'C',$fill);
                $this->Cell($w2[14],6,$totals_days,'LR',0,'C',$fill);
                $this->Cell($w2[14],6,$totals_hours,'LR',0,'C',$fill);

                $this->Ln();
                $last_year = $data2[$i]['itd_year'].'-12-31 00:00:00';                $fill = !$fill;
            }
//      ------------------------------------      ITD ----------------------------------------------------//
            // Closing line
            $this->Cell(array_sum($w2)-95,0,'','T');

        } catch (Exception $e) {
            echo $e;
        }
    }
}
$pdf = new PDF();
// Column headings
$header2 = array('', 'Total Days','Total Hours','Total Days','Total Hours','Total Days','Total Hours','Total Days','Total Hours','Total Days','Total Hours','Total Days','Total Hours');
$header3 = array('ITD','CIVILS', 'MECHANICAL','ELECTRICAL','CAMP','OFFICE','TOTALS','TOTALS');
$header = array('Month','CIVILS', 'MECHANICAL','ELECTRICAL','CAMP','OFFICE','TOTALS','TOTALS');
//     Data loading
$data = $get_result;
$data2 = $array_itds;
sort($data2);
$data3=  $get_result3;


$pdf->SetFont('Arial','',10);
$pdf->AddPage('L', 'Legal');
$pdf->FancyTable($header,$header2,$header3,$data,$data2,$data3);
$pdf->Output();

?>