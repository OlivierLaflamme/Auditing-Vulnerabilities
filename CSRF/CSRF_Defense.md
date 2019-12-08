### On the server side   

#### Add verification code:  
The process of a CSRF attack is often to construct a network request without the user's knowledge. Therefore, if a verification code is used, the user needs to interact with each operation, thereby simply and effectively preventing CSRF attacks.    

#### Verify referer:  
the HTTP header called Referer, which records the source address of the HTTP request. In general, requests to access a restricted security page must come from the same website.... 
website only needs to verify its Referer value for each transfer request. If the domain name starts with the website. It means that the request is from the website's own request is legitimate. If Referer is another website, it may be a CSRF attack, and the request should be rejected.    

#### Using a one-time-token:
Whenever we visit the page, the server will generate a random token value according to the timestamp, user ID, random string and other factors and return it to the front-end form. 
When we submit the form, the token will be submitted as a parameter to The server performs verification 
In this request process, the value of the token is also unpredictable by the attacker, and because of the limitation of the same-origin policy, the attacker cannot use JavaScript to obtain the token value of other domains. Therefore, this method can successfully prevent CSRF attacks.   

However, it should be noted that the token generation must be random, that is, it cannot be predicted by the attacker, otherwise this defense will be useless. In addition, if the token is displayed in the url as a parameter of the GET request, it is easy to leak it in the Referer. There is a more important point: if there are XSS vulnerabilities in the same domain, the token-based CSRF defense will be easily broken.   


#### Limit session life cycle:   
As the name implies, shorten the effective time of Session.    