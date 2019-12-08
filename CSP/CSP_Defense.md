1. Adding policies through meta tags: The preferred setting method for CSP is the HTTP header, which is very useful, but it is more straightforward to 
set through tags or scripts. WebKit has implemented the feature of setting permissions through meta elements , so you can now try the following 
settings in Chrome: add <metahttp-equiv = "X-WebKit-CSP" content = "[POLICY GOES HERE]" in the header of the document >.   

2. DOM API: If this feature is added in the next iteration of CSP, you can query the current security policy of the page through 
Javascript and adjust it according to different situations. For example, if eval () is available, your code implementation may be slightly different.   

#### Content security policy applies to all common resources
3. content-src: limit the type of connection (such as XHR, WebSockets, and EventSource)   
4. font-src: Controls the source of web fonts. For example, you can use Google's web fonts through font-src    
5. img-src: defines the source of the loadable image.   
6. media-src: Restrict video and audio sources.   
7. object-src: Restrict sources of Flash and other plugins.   
8. style-src: Similar to Script-src, but only works on css files.   


### Under the CSP 1 specification, you can also set the following rules:

img-src Valid image source    
connect-src Apply to XMLHttpRequest (AJAX), WebSocket or EventSource    
font-src Valid font source   
object-src Effective plug-in source <object>(eg, <embed>, <applet>, )    
media-src Valid  <audio> and  <video> source   

### The CSP 2 specification contains the following rules:

child-src Valid web workers and element sources, such as  <frame> and  <iframe> (this directive replaces the obsolete frame-src directive in CSP 1  )    
form-action Can be a <form> valid source of HTML  actions   
frame-ancestors Use  <frame>, <iframe>, <object>, <embed> or  <applet> useful source embedded resources   
upgrade-insecure-requests Command the user agent to rewrite the URL protocol and change HTTP to HTTPS (for some websites that need to rewrite a lot of stale URLs).    

## Defense Summary 
CSP is especially important for your users: they no longer need to be exposed to any unsolicited script, content or XSS threats on your website.    

The most important advantage of a CSP for a website maintainer is perception. If you set strict rules on the source of the picture, a script kid tries to insert an image of an unauthorized source on your website, then the picture will be banned, and you will receive a reminder as soon as possible .   

Developers also need to know exactly what their front-end code is doing, and CSP can help them control everything. Will prompt them to refactor parts of their code (avoid inline functions and styles, etc.) and prompt them to follow best practices.    