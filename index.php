
<?php
// echo 1; 
// error_reporting(E_ALL);
 ini_set('display_errors', 0);
// list cac dau so di dong
 $num_mobile = array ( array( 'id' => "086", 'name' => 'viettel', ), array( 'id' => "096", 'name' => 'viettel', ), array( 'id' => "097", 'name' => 'viettel', ), array( 'id' => "098", 'name' => 'viettel', ), array( 'id' => "016", 'name' => 'viettel', ), array( 'id' => "090", 'name' => 'mobifone', ), array( 'id' => "093", 'name' => 'mobifone', ), array( 'id' => "0120", 'name' => 'mobifone', ), array( 'id' => "012", 'name' => 'mobifone', ), array( 'id' => "092", 'name' => 'vietnamobile', ), array( 'id' => "018", 'name' => 'vietnamobile', ), array( 'id' => "099", 'name' => 'gmobile', ), array( 'id' => "019", 'name' => 'gmobile', ), array( 'id' => "0123", 'name' => 'vinaphone', ), array( 'id' => "0124", 'name' => 'vinaphone', ), array( 'id' => "0125", 'name' => 'vinaphone', ), array( 'id' => "0127", 'name' => 'vinaphone', ), array( 'id' => "0129", 'name' => 'vinaphone', ), array( 'id' => "091", 'name' => 'vinaphone', ), array( 'id' => "094", 'name' => 'vinaphone', ), array( 'id' => "088", 'name' => 'vinaphone', ), );
GLOBAL $target_dir;
GLOBAL $create_dir;


/*
 * 
 * truoc khi upload thi tao cac folder mac dinh nhu ben duoi
 * folder uploads/ la thu muc de chua file upload len, dinh dang text .txt
 * folder export/ laf thu muc dung de download file 
 * 
 * file name index.php 
 * file name download.php la file dung de xu ly download file 
 */

$target_dir = "uploads/";
$create_dir = "export/";

/* xoa tat ca cac file cu trong download */
$files = glob($create_dir.'*'); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file))
        unlink($file); // delete file
}

// ham search chuoi trong array
function searchArrayKeyVal($sKey, $id, $array) {
    foreach ($array as $key => $val) {
        if ($val[$sKey] === $id) {
            return $key;
        }
    }
    return false;
}


// doc tung dong file

$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        
        $uploadOk = 0;
    }
}

if($imageFileType == "txt" ) {
  
    $uploadOk = 1;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.<br>";


    } else {
        //html phan upload 
        echo '<!DOCTYPE html>
                <html>
                <body>
                
                <form action="index.php" method="post" enctype="multipart/form-data">
                    Select file to upload:
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Upload File" name="submit">
                </form>
                
                </body>
                </html>
';
    }
}



/*
 * phan xu ly cac ham lien quan toi file
 * 
 * 
 * 
 */

// lay fil luu tru va xu ly
if ($handle = opendir($target_dir)) {
    
    while (false !== ($entry = readdir($handle))) {
        
        if ($entry != "." && $entry != "..") {
            
            
            $myfile = fopen($target_dir.$entry, "r") or die("Unable to open file!");
            
            
            while(!feof($myfile)) {
                $getFile = fgets($myfile);
                $makeRows = explode("\n",$getFile);
                foreach($makeRows as $tmp_row){
                    if(strlen(trim($tmp_row)) > 0){
                        // 3 so dau substr($tmp_row, 0, 3)
                        // 4 so dau substr($tmp_row, 0, 4)
                        // raw number
                        $rawnumb = preg_replace('/\s+/', '', $tmp_row);
                        $chuoibaso = substr(preg_replace('/\s+/', '', $tmp_row), 0, 3);
                        $chuoibonso = substr(preg_replace('/\s+/', '', $tmp_row), 0, 4);
                        $arrayKey3 = searchArrayKeyVal("id", $chuoibaso, $num_mobile);
                        $arrayKey4 = searchArrayKeyVal("id", $chuoibonso, $num_mobile);
                        
                        if ($arrayKey4!==false) {// cho phan 4 so nhu 0123,0124 cua vinaphone
                            $result = $chuoibonso.$num_mobile[$arrayKey4]['name'];
                            //tao file
                            $myfile1 = fopen($create_dir.$num_mobile[$arrayKey4]['name'].".txt", "a+")or die("Unable to open file!");
                            
                            fwrite($myfile1, $rawnumb."\n");
                            
                        }else if ($arrayKey3!==false) { // cac phan so con lai cua mobiphone, viettel....
                            $result = $chuoibonso.$num_mobile[$arrayKey3]['name'];
                            $myfile1 = fopen($create_dir.$num_mobile[$arrayKey3]['name'].".txt", "a+")or die("Unable to open file!");
                            
                            fwrite($myfile1, $rawnumb."\n");
                            
                        }
                        else { // cac hang mang khac
                            
                            $myfile1 = fopen($create_dir."other.txt", "a+")or die("Unable to open file!");
                            
                            fwrite($myfile1, $rawnumb."\n");
                            
                        }
                        
                    }
                }
                
            }
            
        }
        
    }
    
    
}

/*
 * xu ly download file 
 * 
 * 
 */

if (is_dir($create_dir)){
    
    if ($dh = opendir($create_dir)){
        if ($handle = opendir($create_dir)) {
            $array_link = array();
            $i = 0;
            while (false !== ($entry = readdir($handle))) {
                
                if ($entry != "." && $entry != "..") {
                    
                    echo ' <a href="download.php?file='.$entry.'">'.$entry.'</a>'."<br>";
                    
                }
                
            }
            
        }
        
        closedir($dh);
    }
}
fclose($myfile); // dong file


// xoa file goc sau khi xuat link download cac file con
$files = glob($target_dir.'*'); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file))
        unlink($file); // delete file
}



?>



