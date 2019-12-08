# SSRF 
SSRF (Server-Side Request Forgery: server-side request forgery) is a fake exploit server-initiated requests. Generally, SSRF attacks target internal systems that are not accessible from the external network.     

### Types of SSRF 
A. Show response to attacker (basic)    
B. Do now show response (blind)    

### The basics of the vulnerability 
SSRF (Server-Side Request Forgery: Server-Side Request Forgery) is a security vulnerability constructed by an attacker to form a request initiated by the server. Generally, SSRF attacks target internal systems that are not accessible from the external network. (Because it is initiated by the server, it can request the internal system that is connected to it and isolated from the external network)

### Where it appears 
1. Social sharing function: Get the title of the hyperlink for display   
2. Transcoding service: Tuning the content of the original web page through the URL address to make it suitable for mobile phone screen browsing    
3. Online translation: translate the content of the corresponding web page to the website    
4. Image loading / downloading: For example, click in a rich text editor to download the image to the local area; load or download the image through the URL address
5. Picture / article collection function: It will take the content of the title and text in the URL address as a display for a good appliance experience    
6. Cloud service vendor: It will execute some commands remotely to determine whether the website is alive, etc., so if you can capture the corresponding information, you can perform ssrf test    
7. Website collection, where the website is crawled: Some websites will do some information collection for the URL you enter    
8. Database built-in functions: database's copyDatabase function such as mongodb   
9. Mail system: such as receiving mail server address    
10. Encoding processing, attribute information processing, file processing: such as fffmg, ImageMagick, docx, pdf, xml processor, etc.     
11. Undisclosed API implementation and other functions that extend the calling URL: You can use google syntax and add these keywords to find SSRF vulnerabilities    
12. Request resources from a remote server (upload from url such as discuz !; import & expost rss feed such as web blog; where the xml engine object is used such as wordpress xmlrpc.php)

### Vulnerability detection / Verifications 

1. Exclusion method: browser f12 checks the source code to see if the request was made locally (For example: If the resource address type is http://www.xxx.com/a.php?image=(address), an SSRF vulnerability may exist)     
2. dnslog and other tools to test to see if they are accessed (You can encode the uri and parameters of the currently prepared request into base64 in the blind typing background use case, so that after blind typing background decoding, you know which machine and which cgi triggered the request.)    
3. Capture and analyze whether the request sent by the server is sent by the server. If it is not a request from the client, it may be, and then find the internal network address where the HTTP service exists (Look for leaked web application intranet addresses from historical vulnerabilities in the vulnerable platform)
4. Banner, title, content and other information returned directly    
5. Pay attention to bool SSRF

