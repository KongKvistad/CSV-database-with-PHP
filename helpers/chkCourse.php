<?php
function chkCourse($course_DF, $input){
    
    $cols = ["c.code","c.year","c.semester","credits"];

    //extract only relevant cols for checking the "courses" table
    $input->extractCols($cols);
    $inputData = $input->data;
    $courseData = $course_DF->data;
    
    // use to access specific lines
    $inputLine = $inputData[0];


    //im assuming that the course will not exist without an assigned instructor,
    //so im assuming the course exists if the control flow has made it this far. See the 
    //previous checks in "chkInstr.php" if unclear.

    $courseCode = $inputLine["c.code"];
    
    //validate all the fields based on c.code (unique key)
    foreach ($courseData as $key => $value) {
        
        if($value["c.code"] === $courseCode){
            switch($value){
                case $value["c.year"] !== $inputLine["c.year"]:
                    echo "wrong year!";
                    break;
                    return false;
                case $value["c.semester"] !== $inputLine["c.semester"]:
                    echo "wrong semester!";
                    break;
                    return false;
                case $value["credits"] !== $inputLine["credits"]:
                    echo "That course has different credits!";
                    break;
                    return false;
                default:
                    return true;
            }
        }
    }
     
        
    
   
    
    
    
}
?>