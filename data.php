<!-- assignment assumtions:

1: i'm assuming by "input file", you mean a literal file that gets uploaded
2: i'm assuming the input files has column names. to be fair, the alternative is
   assuming they are indexed a particular way.  -->

<html>
<head>
	<title>upload data</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>


        <h2>file Upload</h2>

        <form action="" method="POST" enctype="multipart/form-data">

            Upload file:
            <input type="file" name="fileupload">

            <br> 

            <input type="submit" name="submit" value="Submit">

        </form>
<?php


include_once('./classes/dataFrame.php');
include_once("./helpers/update.php");
include_once("./helpers/chkInstr.php");
include_once("./helpers/chkCourse.php");


if ( isset($_POST["submit"]) ) {

    if ( isset($_FILES["fileupload"])) {
        
        $file_data = $_FILES['fileupload']['tmp_name'];

        // get assoc array for  all the relevant files
        $stud_DF = new dataFrame("./data/students.csv");
        
        $input = new dataFrame($file_data);
        
        $inst_DF = new dataFrame("./data/instructors.csv");

        $inst_course_DF = new dataFrame("./data/inst_course.csv");

        $stud_course_DF = new dataFrame("./data/stud_course.csv");

        $course_DF = new dataFrame("./data/courses.csv");

         // checks input inst.name with instructor and inst_course to see if instructor teaches a course
         // with the corresponding c.code. if yes: continue. if not: notify user.

        if(chkInstr($input, $inst_DF, $inst_course_DF)){
            
            // have to make new dataframe, because the previous one has been partitioned / cropped.
            $input = new dataFrame($file_data);
            
            // if c.code and instructor is correct but the relevant course data is wrong, notify the user.

            if (chkCourse($course_DF, $input)){
                
                //need to new partitions
                $input1 = new dataFrame($file_data);
                $input2 = new dataFrame($file_data);

                //if every check above is ok:
                // write or append to the students table based on the relevant columns.
                // write a new line to stud_course with the appropriate keys and grade.
                update($stud_DF, $stud_course_DF, $input1, $input2);
            
            } 
        }
        

    }
        
}



?>


</body>
</html>