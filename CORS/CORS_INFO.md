## Cross-domain resource sharing (cors)

We'll cover: 
1. Summary of same-origin policy and cross-domain resource sharing (cors) introduction
2. Main content, cors vulnerability attack from entry to mastery
3. cors safety regulations

#### Same Origin Policy

The same-origin policy is a very important concept in browser security. A large number of client-side scripts support the same-origin policy, such as JavaScript.    
The same-origin policy allows scripts running on the page to have unlimited access to any method and property of other scripts on the same website (same source). When scripts from different website pages (non-same) try to access each other, most methods and properties are forbidden.    
This mechanism is very important for modern web applications, because they extensively rely on http cookies to maintain user permissions. The server will use cookies to determine whether the client is legitimate and whether it can send confidential information.    
The browser must strictly isolate the websites of two different sources in order to ensure the integrity and confidentiality of the data.    
Definition of "same source":    
1. domain name   
2. Protocol   
3. tcp port number    
As long as the above three values ​​are the same, we consider these two resources to be the same source.    
To better explain this concept, the following table    

| Verify url                               | result  | the reason                            |
|------------------------------------------|---------|---------------------------------------|
| http://www.example.com/dir/page.html     | success | Same domain, same protocol, same host |
| http://www.example.com/dir2/other.html   | success | Same domain, same protocol, same host |
| http://www.example.com:81/dir/other.html | failure | Different ports                       |
| https://www.example.com/dir/other.html   | failure | Different protocols                   |
| http://en.example.com/dir/other.html     | failure | Different hosts                       |
| http://example.com/dir/other.html        | failure | Different hosts                       |
| http://v2.www.example.com/dir/other.html | failure | Different hosts                       |   

The following figure shows: what happens after a malicious script makes a request if cors is not enabled   
![Capture](https://user-images.githubusercontent.com/25066959/70397423-15f57d00-19e0-11ea-8c8d-8b378a3b584a.PNG)   


#### More background information 
The same-origin policy has too many restrictions for large applications, such as the case of multiple subdomains.    

There are already a lot of technologies to relax the restriction of the same-origin policy. One of them is CORS.    
CORS is a mechanism. This mechanism adds a field to the http header. Generally, web application A tells the browser that it has permission to access application B. This allows the same description to be used to define "homologous" and "cross-origin" operations.    

CORS simply but is: by setting the http header field, the client is qualified to access resources across domains. After being authenticated and authorized by the server, it is the browser's responsibility to support these http header fields and to ensure that restrictions can be applied correctly. The main header field contains: `Access-Control-Allow-Origin`

#### Data Identity    
A server will also notify the client whether to send the user's identity data (cookie or other identity data). If the "Access-Control-Allow-Credentials" field in the http header is set to "true", then the client identity data will be Will be sent to the target server   

#### Allowing Multiple Sources / It's Restrictions    
 you can simply use spaces to separate multiple sources, such as:   
`Access-Control-Allow-Origin: https://example1.com https://example2.com`    
However, no browser supports such a syntax. LOL.

Moreover, It is generally not possible to use wildcards to trust all subdomains, such as:    
`Access-Control-Allow-Origin: *.example1.com`    

Currently only wildcards are supported to match domain names, such as the following:    
`Access-Control-Allow-Origin: *`

Although browsers can support wildcards, the credentials flag cannot be set to true at the same time.    

Just like this header configuration:    
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Credentials: true
```
It turns out that configuring the browser this way will report an error, because when responding to a request with credentials, the server must specify a single domain, and you cannot use wildcards. Simply using wildcards will effectively disable the "Access-Control-Allow-Credentials" field.

The result of these restrictions and behaviors is that many CORS implementations generate "AccessControl-Allow-Origin" values ​​based on the value of the "Origin" header field    

#### Header fields 
There are also some header fields about CORS. One of the fields is `Vary`.
According to CORS RFC, when `Access-Control-Allow-Origin` is dynamically generated, it must be specified with `Vary: Origin`. This header field indicates to the client that the content returned by the server will change based on the value of `Origin` in the request. 

If this header is not set, it may be exploited by certain attacks in some cases.    

## Attacking CORS    

#### Process for attacking CORS misconfigurations     
3 steps related to testing CORS:    
1. Identify
2. Analysis 
3. Use

#### 1. Identify 
APIs are a good choice because they often exchange information with different domains.    
Generally, CORS is configured when a server receives a request with an `Origin` field in its header, so it is easy to generate many such types of vulnerabilities.    
In addition, if the client receives a return message containing a field such as `Access-Control- *` but does not define a source, then the header of the return message is likely to be the "Origin" in the request Field to decide.      

Therefore, after finding the candidate interface, you can send a packet with "Origin" in the header. Testers should try to make the "Origin" field use a different value, such as a different domain name or "null". It's best to automate these tasks with some scripts.
such as:    
```
GET /handler_to_test HTTP/1.1
Host: target.domain
Origin: https://target.domain
Connection: close
```
Then see if the server ’s return message header has the `Access-Control-Allow- *` field    
```
HTTP/1.1 200 OK
…
Access-control-allow-credentials: true
Access-control-allow-origin: https://target.domain
…
```
The above return message indicates that the interface in this application has enabled CORS. If you see this it becomes necessary to test the configuration to determine if there are security flaws.    

#### 2. Analysis   
After step 1 now it is necessary to analyze the configuration as much as possible to find the correct utilization method.    

At this stage, start the `Origin` field in the header of the fuzzing request message and then observe the server's return message to see which domains are allowed. Then try to send a request with the header field `Origin` containing different values ​​to the server to see if the domain name controlled by you is allowed.

```
GET /handler_to_test HTTP/1.1
Host: target.domain
Origin: https://attaker.domain
Connection: close
```
Then see if the server ’s return message header has the `Access-Control-Allow- *` field    
```
HTTP/1.1 200 OK
…
Access-control-allow-credentials: true
Access-control-allow-origin: https://attacker.domain
…
```
In this test example, the header of the packet returned by the server indicates that the domain "attacker.domain" is fully trusted, and user credentials can be sent to this domain.    
#### 3. Use
From an attacker's point of view, your happy to see the target application's `AccessControl-Allow-Credentials` is set to `true`. In this case, the attacker will use configuration errors to steal the victim's private and sensitive data.

The following table briefly illustrates the availability of CORS-based configurations    


| "Access-Control-Allow-Origin" value | "Access-Control-Allow-Credentials" value | Availability |
|-------------------------------------|------------------------------------------|--------------|
| https://attacker.com                | true                                     | Yes          |
| null                                | true                                     | Yes          |
| *                                   | true                                     | No           |   
