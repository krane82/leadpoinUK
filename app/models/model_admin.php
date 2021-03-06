<?php
class Model_Admin extends Model {

  public function get_data() {
    $loginUser = array(
      "login_status" => 1
    );
    return $loginUser;
  }

  public function getMonthlyStats(){
    $spent = array();
    $sold  = array();
    $con   = $this->db();
    $year = date("Y");
    $jan = strtotime(date('Y-01-01'));
    $dec = strtotime('last day of ' . date( 'F Y')) + 86399;

//    echo "$jan : $dec";
//Old sql1
//    $sql1  = 'SELECT MONTH(FROM_UNIXTIME(ld.timedate)) as month, SUM(c.lead_cost) as cost, GROUP_CONCAT(ld.id) FROM `leads_delivery` as ld';
//    $sql1 .= ' LEFT JOIN `clients` as c ON ld.client_id = c.id LEFT JOIN `leads_rejection` as lr ON lr.lead_id = ld.lead_id AND lr.client_id = ld.client_id';
//    $sql1 .= ' WHERE (lr.approval > 0 OR lr.approval IS NULL)';
//    $sql1 .= " AND (ld.timedate BETWEEN $jan AND $dec)";
//    $sql1 .= " GROUP BY month";
    //End of old sql1
    $sql1="SELECT MONTH(FROM_UNIXTIME(ld.date)) as month, sum(c.lead_cost) as 'cost' from leads_rejection ld left join clients c on ld.client_id=c.id where ld.approval!=0 and ld.date between $jan and $dec group by (month)";

    //    $sql1 .= ' AND YEAR(FROM_UNIXTIME(ld.timedate)) =' .$year ;
//old sql2
//    $sql2  = 'SELECT MONTH(FROM_UNIXTIME(l.datetime)) as month, SUM(c.cost) as cost, GROUP_CONCAT(l.id) FROM `leads` as l ';
//    $sql2 .= ' INNER JOIN `campaigns` as c ON c.id = l.campaign_id ';
//    $sql2 .= " WHERE l.datetime BETWEEN $jan AND $dec";
//    $sql2 .= ' GROUP BY month';
    //End of old sql2
//    $sql2 .= ' WHERE YEAR(FROM_UNIXTIME(l.datetime)) = ' . $year;
    $sql2="SELECT MONTH(FROM_UNIXTIME(le.datetime)) as month, SUM(cam.cost) as cost from leads le left join campaigns cam on le.campaign_id=cam.id where le.datetime between $jan and $dec group by (month)";
    $res1 = $con->query($sql1);
    if($res1){
      while($row = $res1->fetch_assoc()){
        $sold[] = $row;
      }
    }


    $res2 = $con->query($sql2);
    if($res2){
      while($row = $res2->fetch_assoc()){
//        print_r($resul1);
        $spent[] = $row;
      }
    }
//    var_dump($spent); exit;

    $months = array("", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

    for($i=1; $i<13; $i++){
      $key = false;
      foreach ($spent as $k=>$v) {
        if((int)$v["month"] === $i){
          $key = $k;
          $month = (int)$v["month"];
        }
      }
      if($month == $i){
        $array[] = array(
          'm' => $months[$i],
          'a'=> $spent[$key]["cost"],
          'b'=> $sold[$key]["cost"],
          'c'=> $sold[$key]["cost"] - $spent[$key]["cost"]
        );
      } else {
        $array[] = array(
          'm' => $months[$i],
          'a'=> 0,
          'b'=> 0,
          'c'=> 0
        );
      }
    }
    return $array;
  }
  public function DistributionOrder(){
    $now = time();
    $st = new DateTime(date('Y-m-01', $now));
    $start = $st->getTimestamp();
    $st->modify("+14 days");
    $end = $st->getTimestamp();
    if( $now < $end ) {
      // do nothing
    } else {
      $start = $end;
      $end = strtotime(date("Y-m-t", $now));
    }

    $sql =  'SELECT c.campaign_name as client, IFNULL(c.lead_cost,0) as lead_cost, IF(COUNT(*)=0,0,SUM(IF(lr.approval=0,1,0))/COUNT(*)) as percentage, ((COUNT(*) - SUM(IF(lr.approval=0,1,0)))*c.lead_cost) as revenue FROM `leads_delivery` as ld';
    $sql .= ' INNER JOIN `leads_rejection` as lr ON lr.lead_id = ld.lead_id AND lr.client_id = ld.client_id INNER JOIN clients as c ON ld.client_id=c.id';
    $sql .= ' WHERE `ld`.`timedate` BETWEEN '.$start.' AND '.$end.'';
    $sql .= ' GROUP BY ld.client_id';
    $sql .= ' ORDER BY revenue DESC, percentage ASC, lead_cost DESC';

    // print($sql); die;
// var_dump($sql);
// exit();
    $con  = $this->db();
    $data = array();



    if($res = $con->query($sql)){
      while($result = $res->fetch_assoc()){
        $data[] = $result;
      }
    
      return $data;
    }
    return FALSE;
  }

  public function pendingPercent()
  {
    $con  = $this->db();
    $Monday = strtotime("Monday this week");
    $sql="select count(t1.id)/(select count(t2.id) from `leads_rejection` t2 where t2.approval!=1 AND t2.date>1491166800) from `leads_rejection` t1 where t1.date>'".$Monday."'";
    $res=$con->query($sql);
    $result=$res->fetch_array();
    return (float)$result[0].'%';
  }
}
