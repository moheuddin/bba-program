<?php
date_default_timezone_set('Asia/Dhaka');
 if(!session_id())
      session_start();
	  
include "config.php";

$charset = 'utf8mb4';
$sql = '';
$sql_data = [];
$result = '';
$stmt = '';
$debug='';
$isAuthenticate=false;
$userName='';
include(dirname(__FILE__)."/../wp-authenticate.php");

if( isset($_SESSION["userName"]) ){
$userName =$_SESSION["userName"];
$isAuthenticate=true;
}


$id = '';
// $con = mysqli_connect($host, $user, $password, $dbname);
//var_dump($_POST);exit;
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
//var_dump($pdo);exit;

$method = $_SERVER['REQUEST_METHOD'];
// $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
//$input = json_decode(file_get_contents('php://input'),true);
$date = date('Y-m-d');
$debug= $date;

switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? intval($_GET['id']) : '';
        $filter = 	(isset($_GET['action']) && ($_GET['action']=="active")) ? '>=' : '<=';
     
        if ($id > 0) {
            $sql = "select * from contacts where id=:id";
            $sql_data = ['id' =>$id];
            $stmt = $pdo->prepare($sql);
            $stmt->execute($sql_data);
            $result = $stmt->fetch();
        } else {
            $sql = "SELECT
				officer.id AS off_id,
				officer.name AS name,
				contacts.id AS id,
				contacts.date_created AS date_created,
				contacts.date_updated AS date_updated,
				contacts.disabled,
				contacts.`date` AS `date`,
				contacts.`time` AS `time`,
				contacts.description AS description,
				contacts.is_done,
				contacts.comments,
				contacts.updated_by,
				contacts.officer
				FROM officer Inner Join contacts ON contacts.officer = officer.id
				Where DATE(date) $filter  '$date' ORDER BY  date ASC, time ASC";
							
        }
			$userData = mysqli_query($con,$sql );


			//$response = array();

			
			$extra = array('userName' => $userName, 'debug' => $debug);
			//echo json_encode(array('result'=>$response, 'message'=> $extra));
			
			 // Create empty array to hold query results
			  $response = [];

			  // Loop through query and push results into $someArray;
			  while ($row = mysqli_fetch_assoc($userData)) {
                $mydate= date('d-m-Y', strtotime( $row['date']));
                $mydate= en2bnNumber($mydate);
                
                $bnday = BNday($row['date']);
                $time = en2bnNumber($row['time']);
				array_push($response, [
				  'id'   => $row['id'],
                  'date' => $mydate,
                  'day' =>  $bnday,
				  'time' => $time,
				  'description' => $row['description'],
				  'comments' => $row['comments']
				]);
			  }
				echo json_encode(array('result'=>$response, 'message'=> 'success','isAuthenticate' => $isAuthenticate, 'debug'=> $extra));
			  // Convert the Array to a JSON String and echo it
			  //$someJSON = json_encode($someArray);
			  //echo $someJSON;
		
		//$extra = array('isAuthenticate' => $isAuthenticate,'userName' => $userName,'debug' => $debug);
		//echo json_encode(array('result'=> $stmt->fetch(),'message'=> $extra));
	
        break;
    case 'POST':
	
        $id = isset($_POST['id']) ? intval($_POST['id']) : '';
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        if ($action == 'delete' && $id > 0) {
            $sql = "DELETE FROM contacts WHERE id=$id";
			if ($con->query($sql) === TRUE) {
			  echo "Record deleted successfully";
			} else {
			  echo "Error deleting record: " . $con->error;
			}

			$con->close();
			
			exit;
			
        }elseif($action=="inactive"){
			
		}
		else {
            $date = isset($_POST['date']) ? $_POST['date'] : '';
            $time = isset($_POST['time']) ? $_POST['time'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $comments = isset($_POST['comments']) ? $_POST['comments'] : '';
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $officer = isset($_POST['officer']) ? intval($_POST['officer']) : 0;

            if ($id > 0) {
                // change existing record
                $sql = "update contacts set date = :date, time = :time, description=:description,comments=:comments,  officer=:officer, date_updated = NOW() where id = :id";
                $sql_data = [
                    'date' => $date,
                    'time' => $time,
                    'description' => $description,
                    'comments' => $comments,
                    'officer' => $officer,
                    'id' => $id,
                ];
                $stmt = $pdo->prepare($sql);
                $stmt->execute($sql_data);
                // $result = $stmt->fetch();

            } else {
                // add new record
                $sql = "insert into contacts (date, time, description, comments,  officer, date_created) values (:date, :time, :description, :comments, :officer,  NOW())";
                $sql_data = [
                    'date' => $date,
                    'time' => $time,
                    'description' => $description,
                    'comments' => $comments,
                    'officer' => $officer,
                ];
                $stmt = $pdo->prepare($sql);
                $stmt->execute($sql_data);
                // $result = $stmt->fetch();

            }
        }
        break;
}

if ($method == 'GET') {
	//$debug=$_REQUEST;
		
		//$data = $stmt->fetchAll();
		/*$data = $stmt->fetchAll();
		$json_data=array();//create the array  
		foreach($data as $row)
		{  
		$json_array['id']=$row['id'];  
			$json_array['date']=$row['date'];  
			$json_array['time']=$row['time'];  
			$json_array['description']=$row['description'];  
			$json_array['comments']=$row['comments'];  
			$json_array['officer']=$row['officer'];  
			array_push($json_data,$json_array);  
		} 
		$debug=$json_data;
		*/
		

 
		  
		//echo json_encode(array('result'=> $json_data,'message'=> $extra));
		
	
} elseif ($method == 'POST') {
    //echo json_encode($stmt->rowCount());
} else {
    //echo $stmt->$rowCount();
}
function en2bnNumber ($number){
    $replace_array= array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
    $search_array= array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
    $bn_number = str_replace($search_array, $replace_array, $number);

    return $bn_number;
}
function BNday($date=''){
    $days = ['রবি বার', 'সোমবার', 'মঙ্গলবার', 'বুধবার','বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'];
    
    //$daynum = dayNumber($date);
    //return $days[$daynum];

    //$days = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
    $day = date('w',strtotime($date));
    $date =  ($days[$day]);
    return $date;
}
function dayNumber($date=''){
    if($date==''){
        $t=date('d-m-Y');
    } else {
        $t=date('d-m-Y',strtotime($date));
    }

    $dayName = strtolower(date("D",strtotime($t)));
    $dayNum = strtolower(date("d",strtotime($t)));
    $return = floor(($dayNum - 1) / 7) + 1;
    return $return;
}