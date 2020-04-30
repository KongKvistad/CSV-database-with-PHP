

   <html>
<head>
	<title>courses</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<?php


include_once('./classes/dataFrame.php');
    $students = new DataFrame("./data/students.csv");
    $stud_course = new DataFrame("./data/stud_course.csv");
    $inst_course = new DataFrame("./data/inst_course.csv");
    $instructors = new DataFrame("./data/instructors.csv");

    $courses = new CourseView("./data/courses.csv");
    
    // get number of students registered, passed and failed:

    $courses->countStud($stud_course->data);
    
    //average grade taken:
    
    $courseData = $courses->data;
    $headers = $courses->header;

    
    
    
    


    $numCourse = count($courseData);
    echo "<h1> Total number of courses: ".$numCourse."</h1><br>";

    
    

    

?>
    <table border="1">
            <tr>
            <?php foreach ($headers as $header): ?>
                <th><?php echo $header; ?></th>
            <?php endforeach; ?>
            </tr>
        <?php foreach ($courseData as $data_cell): ?>
            <tr>
            <?php foreach($data_cell as $key => $value): ?>
                <td><?php echo $value; ?></td>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </table>

</body>
</html>