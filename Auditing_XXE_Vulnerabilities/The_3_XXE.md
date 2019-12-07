# What is XXE

XML external entity (XXE) injection is a serious flaw that allows an attacker to read local files on the server, access internal networks, scan internal ports or execute commands on remote servers. It is targeted at applications that parse XML.   

This attack occurs when XML input containing references to external entities is processed by a weakly configured XML parser. Attackers exploit this by embedding malicious inline DOCTYPE definitions in XML data.   

When the web server processes malicious XML input, the entity will be extended, which may cause an attacker to access the web server's file system, remote file system access, or establish a connection to any host via HTTP / HTTPS.   

Example attack senarios:   
A. Local file hijacking from the server   
B. Accessing server files via file upload   
C. DOS attack with recursive entity extension   

## Attack scenario 1: Local file hijacks the server  

When an attacker sends a malformed XML payload in a request, the server processes the payload and sends back a response containing sensitive information, such as the server's local files, application configuration files, internal network details, etc.    

In a few cases, when submitting an HTTP request using a well-designed XXE payload, the server responds with the server /etc/passwd/.   
![1](https://user-images.githubusercontent.com/25066959/70176142-ee33ab80-16a5-11ea-9025-9f22bd687186.PNG)     
(HTTP request with malicious INLINE DOCTYPE definition-with corresponding response)    

However, in many cases, the server may not send back a response. Another way that an attacker can take advantage of this method is to include the URL (the server controlled by the attacker) in the XXE payload.  

When the server parses the payload, it makes additional calls to the server controlled by the attacker, so the attacker listens to the victim's server and captures local files, server configuration files, and other server details.   

The following images (snapshots 2 and 3) show that the URL is included in the XXE payload. After submitting an HTTP request, the server makes additional calls to an attacker-controlled server. therefore,   

Attacker listens for requests from the victim's system and captures server details (/ etc / passwd /)     
![1](https://user-images.githubusercontent.com/25066959/70176297-3eab0900-16a6-11ea-9fe0-623ccb51b791.PNG)   
(HTTP Request with Attack Control URL)
![1](https://user-images.githubusercontent.com/25066959/70176364-57b3ba00-16a6-11ea-8197-06887e866189.PNG)   
victim's server makes additional calls to attacker's server   

## Attak Scenario 2: Accessing server files via the "File Upload" feature

Many applications support "file upload" functionality (XLSX, DOCX, PPTX, SVG or any XML MIME type format) for further processing. Usually, these files are of XML MIME type.   

An attacker can use the inherent XML type and upload a malicious file embedded with the XXE payload. When the server parses the file, it executes the file containing the XXE payload, causing the sensitive information of the client server to leak.   

Note that libraries (such as APIs) that parse XML on a part of the site may differ from libraries that parse uploaded files.   
![1](https://user-images.githubusercontent.com/25066959/70176467-8467d180-16a6-11ea-9277-7c3ed5b694e6.PNG)
(Embed the XXE payload into a Docx file. Docx (like pptx and xlsx) is essentially an Open XML (OXML) file.)     
![image](https://user-images.githubusercontent.com/25066959/70188424-ae2cf280-16be-11ea-9b10-f1f8f77b71d7.png)
(Upload malicious docx file to (example) application)   
![image](https://user-images.githubusercontent.com/25066959/70188459-c3098600-16be-11ea-97d7-517e9608f3db.png)
(After the file is submitted, the server will respond to the sensitive information of the server / etc / passwd)    

## Attack Senario 3: DOS attack using recersive entity extensions
This attack is also known as "Billion Laugh attack", XML Bomb or recursive entity extension attack. This attack occurs when the parser keeps expanding every entity within itself, which overloads the server and causes the server to shut down.    
![image](https://user-images.githubusercontent.com/25066959/70188567-06fc8b00-16bf-11ea-8f22-aad9a02a0143.png)
From the figure above, we see that when the parser started parsing the XML file, it first `＆lol9;` referred to the entity `lol9` to get the value, but `lol9` itself itself referenced the `lol8` entity again.  

methods to stop XXE attacks include.     

1. Disable external entities. Only allow restricted and trusted external links when necessary  
2. Turn off entity extensions in XML  
3. Double check that the version of the XML library used is vulnerable to XXE attacks.  
4. Validate user-supplied external / internal entities and INLINE DOCTYPE-defined inputs before parsing  

