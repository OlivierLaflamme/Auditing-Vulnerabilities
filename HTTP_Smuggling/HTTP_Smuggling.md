# HTTP-Smuggling 
![20191205171217](https://user-images.githubusercontent.com/25066959/70326865-8d21ea00-1803-11ea-8362-e9a51d7937d2.jpeg)    

### Inspiration: In Defcon 27 in 2019, @James Kettle proposed HTTP Desync Attacks: Smashing into the Cell Next Door ), explaining how to use PayPal's vulnerabilities to be discovered using HTTP Smuggling technology.        

#### Things we need to understand to understand this attack go over Transfer encoding, Message Body, Pipelining.

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

`Olivier Laflamme in \ r \ n \ r \ n chunks.`   
We can send it like this:
 ```
POSTT / xxx HTTP / 1.1
Host : xxx
Content-Type : text / plain
Transfer-Encoding : chunked

7 \ r \ n
Olivier \ r \ n
8 \ r \ n
Laflamme \ r \ n
e \ r \ n
in \ r \ n \ r \ nchunks. \ R \ n
0 \ r \ n
\ r \ n
```
