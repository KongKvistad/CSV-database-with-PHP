

   <html>
<head>
	<title>students</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<?php


include_once('./classes/dataFrame.php');
    $students = new StudView("./data/students.csv");
    $stud_course = new DataFrame("./data/stud_course.csv");
    $courses = new DataFrame("./data/courses.csv");
    
    $courseData = $courses->data;
    $stud_course_Data = $stud_course->data;
    //append the derived values to dataframe
    $students->compCourses($stud_course_Data);
    // calc the gpa
    $students->compGPA($courseData, $stud_course_Data);
    // sort by GPA
    $students->sortTable($students->data, "GPA");
    
    $studData = $students->data;
    
    $headers = $students->header;
    
   
    
    
    


    $numStud = count($studData);
    echo "<h1> Total number of students: ".$numStud."</h1><br>";

    //convert unix timestamp to date
    function isdate($key, $value){
        return $key === "bday" ? gmdate("M d", $value) : $value;
    }
    

    

?>
    <table border="1">
            <tr>
            <?php foreach ($headers as $header): ?>
                <th><?php echo $header; ?></th>
            <?php endforeach; ?>
            </tr>
        <?php foreach ($studData as $data_cell): ?>
            <tr>
            <?php foreach($data_cell as $key => $value): ?>
                <td><?php echo isdate($key, $value); ?></td>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </table>

</body>
</html>