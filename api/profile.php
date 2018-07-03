<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "test";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    

    //http://stackoverflow.com/questions/18382740/cors-not-working-php
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {    
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
    //http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
    $postdata = file_get_contents("php://input");
    if (isset($postdata)) {
        $request = json_decode($postdata);
        $id = $request->id;
        //SELECT user_posts.post , users.username FROM user_posts INNER JOIN users ON users.id = 8
        $sql = "SELECT users.username,users.name,users.surname,users.role,users.email,users.profile_image,user_posts.post,user_posts.image_post FROM user_posts INNER JOIN users ON users.id=".$id." AND user_posts.user_id=".$id;
        $result = $conn->query($sql);
        $json = array();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $json[] = $row;
            }
            $publication = new stdClass();
            $publication->post = $json[0]['post'];
            $publication->image_post = $json[0]['image_post'];
            unset($json[0]['image_post']);
            $json[0]["post"] = array();
            array_push($json[0]["post"] , $publication);
            
            for($i = 1; $i < count($json); ++$i){
                $publication->post = $json[$i]['post'];
                $publication->image_post = $json[$i]['image_post'];
                array_push($json[0]['post'] , $publication);
            }
            echo json_encode($json[0]);
        }
        else{
            echo "error";
        }
        
    }
    $conn->close();
?>