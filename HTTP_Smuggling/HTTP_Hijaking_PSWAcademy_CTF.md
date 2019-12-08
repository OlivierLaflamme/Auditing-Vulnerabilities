## LAB 0   
HTTP request smuggling, basic CL.TE vulnerability    
Hint: Manually fixing the length fields in request smuggling attacks can be tricky. Our HTTP Request Smuggler Burp extension was designed to help. You can install it via the BApp Store.    

intercept with burp and this is what we got. 
![Capture](https://user-images.githubusercontent.com/25066959/70396681-18a0a400-19d9-11ea-83a9-d6a7ca90a609.PNG)    

so lets change it up... 
![Capture](https://user-images.githubusercontent.com/25066959/70396737-a2e90800-19d9-11ea-967d-74f589aeae9e.PNG)    

send it again
![Capture](https://user-images.githubusercontent.com/25066959/70396766-cf048900-19d9-11ea-9980-7d575a2cd289.PNG)

So, the front-end server uses the Content-Length header and the back-end server uses the Transfer-Encoding header.   
so the actual content length is 6 but we smuggle in 10? I'm a little confused tbh. Becuase I tried differnet content length with more text but...    

