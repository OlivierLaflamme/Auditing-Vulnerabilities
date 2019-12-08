# Auditing-Vulnerabilities
In this repository I'll host my research and methodologies for auditing vulnerabilities. Some of these concepts are new to me and I just want to udnerstand them document them, and have a quick reference for these vectors.  <b>Perpetually in the works.</b>   
Some of my images are in chinese. I've got a few chinese VM's that I use so dont be shocked. And whatever text is in chinese you should understand / have seen similar interfaces to wit in your past.   
我提供了的一些中文图片信息，因为我有少量虚拟机。你应该能够理解我提供的中文信息，并且你很可能已经通过别的渠道见过了。 
___

### XXE 
Talking about JAVA and PHP XXE. I'll be documenting what I've learned going over basic concepts, from their i'll document basic  principle / methodology, its utilization how can be audited, and defense.     
0x0 [XXE Attack Methodes | The Quick and Dirty](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/Auditing_XXE_Vulnerabilities/The_3_XXE.md)   
0x1 [Introduction](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/Auditing_XXE_Vulnerabilities/Introduction.md)   
0x2 [Restrictions and Solutions to XML XXE](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/Auditing_XXE_Vulnerabilities/Restrictions_and_Solutions_XML_XXE.md)     
0x3 [Summary of Use](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/Auditing_XXE_Vulnerabilities/Summary_of_Use.md)    
0x4 [Everything JAVA](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/tree/master/Auditing_XXE_Vulnerabilities/JAVA)   
0x5 [Everything PHP](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/tree/master/Auditing_XXE_Vulnerabilities/PHP)   
0x6 [The 1 Python Thing](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/tree/master/Auditing_XXE_Vulnerabilities/Python)    

### HTTP Smuggling 
Talking about HTTP-Smuggling and how it leverages the different ways that a particularly crafted HTTP message can be parsed and interpreted by different agents (browsers, web caches, application firewalls)...    
0x0 [HTTP-Smuggling](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/HTTP_Smuggling/HTTP_Smuggling.md)   
0x1 [Preventing HTTP-Smuggling & Defense](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/HTTP_Smuggling/Preventing_HTTP_Smuggling.md)   

### CSRF  
Discuss what is CSRF and some techniques for how I bypass CSRF defenses.  
0x0 [CSRF Introduction](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/CSRF/CSRF.md)    
0x1 [Bypassing WAF Defense](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/CSRF/Bypassing_CSRF_defense.md)    
0x2 [Defense](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/CSRF/CSRF_Defense.md)    
   
### SSRF   
Looking into SSRF what they are and how its vector works / affects. Generally, SSRF attacks target internal systems that are not accessible from the external network. Lets figure out how...    
0x0 [Understanding SSRF](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/SSRF/Understanding_SSRF.md)    
0x1 [SSRF Bypassing](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/SSRF/SSRF_Bypass.md)   
0x2 [SSRF Defense](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/SSRF/SSRF_Defense.md)    
0x3 [SSRF Python Bypassing](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/tree/master/SSRF/PHP)   
0x4 [SSRF in CTF's](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/SSRF/SSRF_For_CTF.md)     
0x5 [SSRF MAP](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/SSRF/SSRF_Map.md)    

### CSP 
Learning about CSP why its used and how to bypass. 
0x0 [About CSP](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/CSP/CSP.md)     
0x1 [Bypassing CSP](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/CSP/bypassing_CSP.md)    


### WAF 
0x0 [Bypassing WAF at HTTP protocl level](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/WAF/Bypassing_WAF_at_the_HTTP_Protocol_Level.md)   

### CRLF
TODO    

### CORS   
TODO

___
### References and Related Materials: 
[XXE References](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/Auditing_XXE_Vulnerabilities/References.txt)   
[CSP References](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/CSP/References.txt)    
[HTTP-Hijacking References](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/HTTP_Smuggling/references.txt)   
[SSRF References](https://github.com/OlivierLaflamme/Auditing-Vulnerabilities/blob/master/SSRF/References_and_Related_Materials.txt)   
[CSRF References]()    
[WAF References]()

___
### TODO:    
1. Improve XXE PHP File it's kinda shit as it stands.     
2. CSRF improuve.    
3. WAF section is shit.    
4. "Understanding SSRF" format isnt compliant with other documents - shit formatting
5. SSRF Bypassing needs to add / fix php and python
6. ADD WAF and CSRF Reference material and linkes i find smart and useful 



