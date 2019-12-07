This document is shit at the moment but we should know the harmfulness of HTTP request smuggling, so how to prevent it? There are roughly three general defenses that are not targeted at a particular server.    

1. Disable the reuse of TCP connections between the proxy server and the back-end server.    
2. Uses the HTTP / 2 protocol.    
3. The front and back end use the same server.    

Some of the above measures cannot fundamentally solve the problem, and there are many deficiencies, such as disabling the reuse of TCP connections between the proxy server and the back-end server, which will increase the pressure on the back-end server.       

The use of HTTP / 2 cannot be promoted at all under current network conditions. Even servers supporting the HTTP / 2 protocol will be compatible with HTTP / 1.1. Therefore the cause of HTTP request smuggling is not a problem of protocol design, but a problem of different server implementations. Honestly the best yet least simple solution is to implement RFC7230-7235, but this is also The hardest thing to do.     

However, I have consulted many attack articles and did not mention why HTTP / 2 can prevent HTTP Smuggling. And it's been said by other sources that “Use HTTP / 2 for back-end connections, as this protocol prevents ambiguity about the boundaries between requests.”   

Then I went to check the differences between HTTP / 2 and HTTP / 1.1. I personally think that Request multiplexing over a single TCP connection is mainly added to HTTP / 2, which means that using HTTP / 2 can use a single TCP connection to request resources. This reduces the possibility of TCP connection reuse, even if you can smuggle, you can only hit yourself; and the introduction of a new binary framing mechanism also limits this attack.     

