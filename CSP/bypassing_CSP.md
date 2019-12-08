#### CSP (Content Security Policy) is there / in-place to mitigate some attacks, such as xss, csrf. It behaves as a whitelist mechanism for resources loaded or executed on the website, and is defined by HTTP headers or meta elements.    

Although CSP provides strong security protection, it also causes the following problems: 

1. Eval and related functions are disabled     
2. Embedded JavaScript code will not be executed   
3. remote scripts can only be loaded through whitelisting    

Basically, there are not many xss vulnerabilities that are prone to exist on CSP, unless you insist on using 'unsafe-inline'. If the configuration is improper, even the website cannot be used normally) Otherwise, xss will be greatly reduced, and bypass CSP is more csrf that is not easy to be killed by csp.    

![Capture](https://user-images.githubusercontent.com/25066959/70393684-90aba180-19ba-11ea-9d42-6f2458c102c6.PNG)    
![Capture](https://user-images.githubusercontent.com/25066959/70393830-7672c300-19bc-11ea-9e65-685a7a01beb8.PNG)   
This causes XSS to fail.   
