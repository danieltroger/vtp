<?php
include_once "pwds.php";
header("Content-type: text/plain");
$fail1 = true; // if file_put_contents failed
$fail2 = true; // if md5 signature verification failed
$fail3 = true; // if password is wrong
if(isset($_POST['json']) && isset($_POST['password']))
{
  $data = $_POST['json']; // the posted data
  $data = str_replace("<","&lt;",str_replace(">","&gt;",$data)); // fix CSRF issues (dirty but should do it)
  if($_POST['password'] == hash("sha256",password2))
  {
    $fail3 = false;
    if(file_put_contents(".htvtpdata.json",$data) !== FALSE) // try to write it to file
    {
      $fail1 = false;
    }
    $md5_1 = md5($data); // calculate md5
    $md5_2 = md5_file(".htvtpdata.json"); // compute md5 of (hopefully) written file
    if($md5_1 == $md5_2)
    {
      $fail2 = false;
    }
  }
}
echo json_encode(array('allvars' => print_r(get_defined_vars(),1),'data' => @$data, 'fail1' => $fail1, 'fail2' => $fail2, 'fail3' => $fail3, 'writeable' => is_writeable(".htvtpdata.json")));
?>
