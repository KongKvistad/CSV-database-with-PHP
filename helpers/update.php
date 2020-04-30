<?php
function update($stud_DF, $stud_course_DF, $input1, $input2){
   
    $studData = $stud_DF->data;
   
    $stud_course = $stud_course_DF->data;
   
    //INPUTFILE; FIRST PARTITION
    //extract only relevant cols for checking the "students" table
    $input1->extractCols(["studnr", "name", "surname", "bday"]);
    $inputData = $input1->data;

    // use to access specific lines
    $inputLine = $inputData[0];
    
    //INPUTFILE; SECOND PARTITION
    //extract only the cols for writing to
    $input2->extractCols(["c.code","studnr","grade"]);
    $inputData2 = $input2->data;
    
    // use to access specific lines
    $inputLine2 = $inputData2[0];
        
    
        $newLine = False;
        $spliced_line;
        
        foreach ($studData as $key => $value) {
            
            
            //if studnr exists (key)
            if($inputLine["studnr"] === $value["studnr"]){
                $newLine = True;
                $spliced_line = array_splice($studData, $key, 1);
            }    
            
        }
        
        //function to check for duplicate entries in stud_course
        function duplicate($inp, $stud_course){
            $flag = false;

            foreach($stud_course as $key => $value){
                if($inp["c.code"] === $value["c.code"] && $inp["studnr"] === $value["studnr"])
                    $flag = true;
            }
            return $flag;
        }


        //if there's no match, append to students and stud_course
        if(!$newLine){
            $stud_DF->append($inputLine);
            echo "appended data to students,"."<br>";
            
            //unnless the student has already enrolled / recieved a grade
            if(duplicate($inputLine2, $stud_course)){
                echo "but you cannot enroll in the same class twice / change your grade!";
            } else {
                echo "+ you made a new entry in stud_course"."<br>";
                $stud_course_DF->append($inputLine2);
            }
            
            
            
        
        //alternatively, write to existing line in students + append to stud_course
        } else {
            echo "student data has been altered,"."<br>";
            $stud_DF->overWrite($studData, $inputLine);
            
            //unnless the student has already enrolled / recieved a grade
            if(duplicate($inputLine2, $stud_course)){
                echo "but you cannot enroll in the same class twice / change your grade!";
            } else {
                echo "+ you made a new entry in stud_course"."<br>";
                $stud_course_DF->append($inputLine2);
            }
        }
        
    
}
?>