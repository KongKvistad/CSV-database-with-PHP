<?php
    class dataFrame{
        
        public $data = [];
        public $file;
        public $header;
        //stores the assoc array. 
        //formatted as an array of key-value pairs, kinda like in JS, E.G; 
        
        //  "data" : [
        //     {
        //     "studnr" : "1",
        //     "name" : "eirik",
        //     "surname" : "kvistad"
        //     },
        //     {
        //     "studnr" : "2",
        //     "name" : "lars",
        //     "surname" : "gimle"
        //     },
        //  ]

        //initializes by conveerting csv to assoc
        public function __construct($fileName) 
        {   

            
            // applies str_getcsv to every single line in the file 
            $rows = array_map('str_getcsv', file($fileName));

            //pops element at index 0
            $header = array_shift($rows);

            //keep it for later LOL
            $this->header = $header;
            
            // seeing how there are just as many columns as there are
            // types of fields, you can use the array above as keys
            // for the actual row data, using array_combine

            foreach($rows as $row) {
                array_push($this->data, array_combine($header, $row));
                
            }

            //set the file for the object
            $this->file = $fileName;
            
        }
        //append new line
        public function append($param){
            $file_data = fopen($this->file, 'a');
            fputcsv($file_data, $param);
        }

        // overwrite lines
        public function overWrite($newArr, $added){
            $fp= fopen($this->file, 'w');
        
            fputcsv($fp, $this->header);

            foreach ($newArr as $fields) {
                fputcsv($fp, $fields);
            }
            fputcsv($fp, $added);
            
        }
        // partition the file
        public function extractCols($columns){

            //values in input needs to be converted to assoc array.
            $newArr = array_flip($columns);
            

            //set the new val equal to that of the input file
            foreach($this->data[0] as $key => $val){

                if(isset($newArr[$key])){
                    $newArr[$key] = $val;
                    
                }
                
            }  
            //overwrite the data atrribute - the plan is to instantiate a new object
            // then call extraxtCols for all the different partittions i need to make,
            // such that all the partitions become seperate objects
            $this->data = [$newArr];
            
            
        }

        //function to get unique entries in relational tables;
        public function OnlyUnique($df, $target){
            
            $results = [];
            
            foreach($df as $key => $value){
                $hit = $value[$target];
                array_push($results, array($target => $hit));
            }
            //removal of duplicates, credits to https://www.tutsmake.com/php-remove-duplicates-from-multidimensional-array-by-key-value/
            return array_map("unserialize", array_unique(array_map("serialize", $results)));
        }

        // sorts the table according to some paramtere. DUH.
        public function sortTable($df, $param){
            $keys = array_column($df, $param);

            array_multisort($keys, SORT_DESC, $df);
        
            $this->data = $df;
        }

        

    }
    
    class StudView extends dataFrame{
        
        
        
        // the function below is a frequency pattern that counts the amount of ocurrences
        // of a particular grade. If student to grade relationship does not exist,
        // no counter-value is made for that grade.
        
        public function compCourses($courses){
            
            $students = $this->data;

            
            //get only unique students
            $uniqStud = $this->OnlyUnique($courses, "studnr");

            foreach($courses as $course){

                $grade = $course["grade"];
                $cCode = $course["c.code"];
                 
                foreach($uniqStud as $key => $stud){
                    if($stud["studnr"] === $course["studnr"]){
                        //option #1: key exists in frequency counter and the specifc grade exists
                        if(array_key_exists($grade."-count", $uniqStud[$key])){
                            $uniqStud[$key][$grade."-count"] = $uniqStud[$key][$grade."-count"] + 1;
                        } else {
                            //option #2: key and / or grade does not exist in frequency counter
                            $uniqStud[$key][$grade."-count"] = 1;
                        }
                        
                        
                        
                    }
                }
            }

        
        
            //append the results to dataframe
            foreach($students as $key => $val){
                
                //initialize value 
                $students[$key]["passed"] = 0;
                $students[$key]["failed"] = 0;
                

                foreach($uniqStud as $stud){
                    if($stud["studnr"] === $students[$key]["studnr"]){
                        
     
                        // I have angered the PHP Gods; 
                        // switch statements where apparently out of the question.

                        if(isset($stud["A-count"])){
                            $students[$key]["passed"] += $stud["A-count"];

                        }
                        if(isset($stud["B-count"])){
                            $students[$key]["passed"] += $stud["B-count"];
                        }

                        if(isset($stud["C-count"])){
                            $students[$key]["passed"] += $stud["C-count"];
                        }
                        if(isset($stud["D-count"])){
                            $students[$key]["passed"] += $stud["D-count"];
                        }

                        if(isset($stud["E-count"])){
                            $students[$key]["passed"] += $stud["E-count"];
                        }
                        if(isset($stud["F-count"])){
                            $students[$key]["failed"] += $stud["F-count"];
                        }
                    }
                }
            }
            
            array_push($this->header, "passed", "failed");
            
            $this->data = $students;
            
            

        }

        public function compGPA($courses, $stud_course_Data){
            //The GPA of a student equals to sum(course_credit x grade) / sum(credits_taken).
            $students = $this->data;
            $results = [];

            // translates grade into value
            function calcGrade($grade, $credits){
                
                switch($grade){
                    case $grade === "A":
                        return 5 * $credits;
                    case $grade === "B":
                        return 4 * $credits;
                    case $grade === "C":
                        return 3 * $credits;
                    case $grade === "D":
                        return 2 * $credits;
                    case $grade === "E":
                        return 1 * $credits;
                    case $grade === "F":
                        return 0 * $credits;
                }
                
            }
            
            // need to multiply the course credit with each respective grade for each student
            foreach($stud_course_Data as $line){
                
                foreach($courses as $key => $course){
                    if($line["c.code"] === $course["c.code"]){
                        
                        
                            array_push($results, [
                            "studnr" => $line["studnr"],
                            "c.code" => $course["c.code"],
                            "credits" => $course["credits"], 
                            "grade_basis" => calcGrade($line["grade"], $course["credits"])
                            
                            ] );
                        
                    }
                    
                        
                }
                
       
            }

            // get only unique instances and accmulate cradits and points for
            //final GPA calculation

            $uniqueStud = $this->OnlyUnique($results, "studnr");
           
            foreach($results as $res){

                foreach($uniqueStud as $key => $stud){
                    if($stud["studnr"] === $res["studnr"]){
                        
                        if(!array_key_exists("points", $uniqueStud[$key])){
                            $uniqueStud[$key]["points"] = 0;
                        }
                        if(!array_key_exists("credits", $uniqueStud[$key])){
                            $uniqueStud[$key]["credits"] = 0;
                        }
                        if(array_key_exists("credits", $uniqueStud[$key])){
                            $uniqueStud[$key]["credits"] += $res["credits"];
                        }
                        if(array_key_exists("points", $uniqueStud[$key])){
                            $uniqueStud[$key]["points"] += $res["grade_basis"];
                      
                        }

                    }
                }
            }
            //append the results to dataframe
            foreach($students as $key => $val){
                
                //initialize value 
                $students[$key]["GPA"] = 0;
                

                foreach($uniqueStud as $stud){
                    if($stud["studnr"] === $students[$key]["studnr"]){
                        
                       $students[$key]["GPA"] += number_format($stud["points"] / $stud["credits"], 2);
                            
                    }
                }
            }
        
            array_push($this->header, "GPA");
            
            $this->data = $students;
             
            
            
        }
    }
    
    class CourseView extends dataFrame{
        
        //i guess we're doing the same frequency counter as with the students.
        // i should probably abstract this functionality.

        // but then again there's a lot of stuff i probably should be doing.

        public function countStud($stud_course){

            $courses = $this->data;

            $uniqueCourse = $this->OnlyUnique($stud_course, "c.code");

            $res = [];

            foreach($uniqueCourse as $unique){


                foreach($stud_course as $course){
                    if($unique["c.code"] === $course["c.code"]){
                        if(!array_key_exists("countStud", $unique)){
                            $unique["countStud"] = 0;
                        }
                    }
                }
            }
            


        }
    }
     
?>