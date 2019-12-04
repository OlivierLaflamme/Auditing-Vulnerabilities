```
<? php 
libxml_disable_entity_loader ( false ); // This function was originally forbidden to load external entities, false is to allow 
$ user1  =  $ _POST [ 'user1' ]; 
$ xmlfile  =  file_get_contents ( 'php: // input' );  // receive xml data 
$ aa  =  str_replace ( '<! ENTITY' , '**' , $ xmlfile ); 
$ dom  =  new  DOMDocument (); 
$ dom- > loadXML ( $ aa ,  LIBXML_NOENT  |  LIBXML_DTDLOAD ); 
$ creds  = simplexml_import_dom ( $ dom ); 
$ user  =  $ creds- > user ; 
?>
<! DOCTYPE html> 
<head> 
<title> 08067 </ title> 
<meta name = "description" content = "slick Login"> 
<meta name = "author" content = "MRYE +"> 
<link rel = "stylesheet" type = "text / css" href = "./ xxe / style.css" /> 
<style type = "text / css"> 
textarea {resize: none; width: 400px; height: 200px; margin: 10px auto;} 
</ style> 
<script type = "text / javascript" src = "./ xxe / jquery-latest.min.js"> </ script> 
<script type = "text / javascript" src = "./ xxe / placeholder .js "> </ script> 
</ head> 
<body> 
<form id =" slick-login "action =" ./ index.php "method =" post "> 
<label for =" user "> user </ label> 
<input type =" text "name =" user1 "class =" placeholder "placeholder =" user? "> 
<input type =" submit "value =" search ">
<textarea name = "comment" form = "usrform" class = "placeholder" placeholder = "......"> <? php  echo  " $ user " ; echo  " $ user1 " ; ?> </ textarea> < !-Here user1 receives and prints> 
<user parameter is the xml echo-> 
</ form> 
</ body> 
</ html>
```
Note: This code is taken from a certain ctf