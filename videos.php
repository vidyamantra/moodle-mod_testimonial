<?php
/**
 * save and upload the audio video files into directories and link of files in database

 * @package    mod
 * @subpackage feedcam
 * @copyright  2014 krishna
 * @license    http://www.vidyamantra.com
 */
/*
    echo '<body>';
      echo '<fieldset><legend><font color="black"  size="4"><b style="font-family:  "Hoefler Text", Georgia, "Times New Roman", serif;">RECORDINGS </b></font> </legend>';

    

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
global $DB;

            if(isset($_POST['delete']))  {   
                if(isset($_POST['name'])){ 
                     $names=$_POST['name'];
                     
                        foreach($names as $value){
                                      echo "<div  style='float:right;'><font color='#A80707'><b>".$value." , </font></b></div>";
                               
                           if(!file_exists('uploads/'.$value)){
                                echo "Sorry Video had been currupted and did not stored on server<br /><br/>";
                                // mysqli_query($conn,"DELETE FROM videos WHERE name='$value' ");   //db
                                 
                                 $DB->delete_records("videos", array("name"=>$value));
                           }
                           
                            else{
                                 unlink('uploads/'.$value);
                                 
                                // mysqli_query($conn,"DELETE FROM videos WHERE name='$value' ");  //db
                                  $DB->delete_records("videos", array("name"=>$value));
                             }
                                 
                         }
                         
                      echo "<div><font color='#A80707'> Successfully Deleted </font></div>";   
                }
           }
 
   echo '<form action="" method=post>';
       
       
      // $DB->get_record_sql('SELECT * FROM {videos} WHERE firstname = ? AND lastname = ?', array('Martin', 'Dougiamas'));
  $query= $DB->get_records_sql('SELECT * FROM {videos}');
     
      //  $query= mysqli_query($conn,"SELECT * FROM videos "); // db 
        
    //     if (mysqli_num_rows($query) == 0){  
//db
        if(!$query){
                echo "<a href='index.php'><input type=button value='Back to Video Capture' name='home' /></a>";
                echo "<div  style='float:right;'><font color='#A80707'><b>No Video File Exist</font></b></div>";
          }
            
        else {  
            
            echo "<div align=center><a href='index.php'><input type=button value='Back to Video Capture' name='home' style='height: 40px; width: 180px;' /></a> | ";
            echo '<td colspan=3><input type="submit" value="Delete Videos" name="delete" title="Delete" style="height: 40px; width: 180px;" /></td></div><br/><br/>';
            
            echo "<div style='overflow: scroll; width: 500px; height: 600px;margin-left:40%;'><table cellpadding=36 cellspacing=2 bordercolor=green border=1>";
            echo "<tr><th>Id</th><th>Name</th><th>Delete</th></tr>";
            
           // while($row=$DB->get_records_list($query)){   //db
        
        foreach ($query as $value) { 
                $id=  $value->id;
                $name=$value->name;
                $urll=$value->url;

                echo "<tr><td>$id</td><td><a href='watch.php?id=$id'>$name</a><br /></td><td><input type=checkbox name=name[] value='$name' /></td></tr>";
            }
            
            echo "</table></div>";
        }
      
      echo '</form>';
      echo '</fieldset>';
   echo  '</body>';
   
