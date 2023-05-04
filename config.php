<?php

$customhost = "database-2.cbyhk7lkaodh.us-east-1.rds.amazonaws.com"; 
$customuser = "aws_user"; 
$custompass = "Bait9999"; 
$customdb = "speed_db"; 
$custombucket = "99speedmartbucket";
$customregion = "us-east-1";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

require 'vendor/autoload.php';

$app = new \Slim\Slim();

$bucket = 'custombucket';
$region = 'customregion';

$db_conn = new mysqli('customhost', 'customuser', 'custompass', 'customdb');

if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}

$output = array();
$table = 'employee';

$app->get('/', function() use ($app) {
    $app->render('AddEmp.html');
});

$app->post('/about', function() use ($app) {
    $app->render('www.intellipaat.com');
});

$app->post('/addemp', function() use ($app, $db_conn, $bucket, $region) {
    $emp_id = $app->request->post('emp_id');
    $first_name = $app->request->post('first_name');
    $last_name = $app->request->post('last_name');
    $pri_skill = $app->request->post('pri_skill');
    $location = $app->request->post('location');
    $emp_image_file = $_FILES['emp_image_file'];

    $insert_sql = "INSERT INTO employee VALUES (?, ?, ?, ?, ?)";
    $stmt = $db_conn->prepare($insert_sql);

    if ($emp_image_file['name'] == "") {
        $app->response->setStatus(400);
        $app->response->setBody("Please select a file");
        return;
    }

    try {
        $stmt->bind_param('sssss', $emp_id, $first_name, $last_name, $pri_skill, $location);
        $stmt->execute();
        $stmt->close();

        $emp_name = $first_name . " " . $last_name;

        // Upload image file in S3
        $emp_image_file_name_in_s3 = "emp-id-" . strval($emp_id) . "_image_file";
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $region
        ]);

        try {
            $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $emp_image_file_name_in_s3,
                'Body'   => fopen($emp_image_file['tmp_name'], 'rb'),
                'ACL'    => 'public-read'
            ]);
            $object_url = $s3->getObjectUrl($bucket, $emp_image_file_name_in_s3);
        } catch (S3Exception $e) {
            $app->response->setStatus(500);
            $app->response->setBody($e->getMessage());
            return;
        }
    } catch (mysqli_sql_exception $e) {
        $app->response->setStatus(500);
        $app->response->setBody($e->getMessage());
        return;
    }

    $app->render('AddEmpOutput.html', array('name' => $emp_name));
});

$app->run();
