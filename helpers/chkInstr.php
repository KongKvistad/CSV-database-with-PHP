<?php
// THIS DOES NOT ACCOUNT FOR THE SAME INSTRUCTOR TEACHING MULTIPLE COURSES.
function chkInstr($input, $inst_DF, $inst_course_DF){

    $instData = $inst_DF->data;

    $inst_course = $inst_course_DF->data;

    $input->extractCols(["inst.name", "c.code"]);
    $inputData = $input->data;

    // use to access specific lines
    $inputLine = $inputData[0];
    
    $instKey = false;

    //get the instructor id from the name
    foreach ($instData as $key => $value) {
        
            if($value["inst.name"] === $inputLine["inst.name"]){
                $instKey = $value["inst.id"];
            }
        
    }
    
    if(!$instKey){
        echo "there is no such instructor!";
        return false;
    } else {
        
        // get the course code that instructor teaches in.
        $courseCode = false;
        
        foreach ($inst_course as $key => $value) {
            if($instKey === $value["inst.id"]){
                $courseCode = $value["c.code"];
            }
        }
        // check to see if the course code corresponds with what that instructor teaches
        if($courseCode !== $inputLine["c.code"]){
            echo "that instructor teaches a different course, or none at all!";
            return false;
            
        } else {
            
            return true;
        }

    }
     
}

?>