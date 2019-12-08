# Bypassing CSRF Defenses

When you check requests for sensitive operations, they tend to implement CSRF protection. Sometimes it may be a CSRF token in the request body, it may also be a referer field detection, or sometimes it is a special HTTP header field or cookie field.    

But CSRF's defense does not mean that it cannot be bypassed. In this document we will discuss some techniques for how I bypass CSRF defenses.

### Click Hijack (Change request method)
Regardless of which CSRF defenses are deployed, there are two things you can try first: Click hijacking and change request methods.     

Another method worth trying is to change the request method. If the sensitive request to be forged was sent via the POST method, then try to convert it to a GET request. If the operation was sent via the GET method, then try converting to the POST method. Applications may still perform operations and usually have no protection mechanisms.     
For example, the following request:    
```
POST /change_password
POST body:
new_password=qwerty
```
Can be rewritten as : 
```
GET /change_password?new_password=qwerty
```
### CSRF token defense 
Because a site using a CSRF token does not mean that the token is effectively validating the corresponding request operation, you can try the following methods to bypass the CSRF token protection.    

#### Delete the token parameter or send an empty token
You can request data normally without sending a token because this logic error is very common in applications: applications sometimes check the validity of a token when the token exists or when the token parameters are not empty. In this case, if a request does not contain a token or the token value is empty, it is also possible to bypass the defense of CSRF.    
For example, a legitimate request is as follows    
```
POST /change_password
POST body:
new_password=qwerty &csrf_tok=871caef0757a4ac9691aceb9aad8b65b
```
Then implement this request: 
```
POST /change_password
POST body:
new_password=qwerty
```
Or this:    
```
POST /change_password
POST body:
new_password=qwerty&csrf_tok=
```
#### Use CSRF token for another session
The application may just check if the token is legal, but not check if the token actually belongs to the current user. If this is the case, you can hard-code a valid token in the payload.   
If a victim's token is 123abc456def789ghijklmno910234acb and your own token is YOUR_TOKEN, then you can easily obtain your own token but it is difficult to obtain the victim's token. Try to provide your own token in the payload to bypass the CSRF defense.   
In other words, the following request should have been sent:   
```
POST /change_password
POST body:
new_password=qwerty &csrf_tok=123abc456def789ghijklmno910234acb
```
But instead send this request:    
```
POST /change_password
POST body:
new_password=qwerty &csrf_tok=YOUR_TOKEN
```

### Fixed Sessions 
Sometimes a site uses a dual submission cookie as a CSRF defense. This indicates that the request needs to contain a cookie whose value is a random token value, and at the same time, a field value in the request parameter also contains the random token value. If the values ​​are the same, the request is valid. This form of defense is very common.    
If a dual-commit cookie is used in defense, then the application may not have a valid token stored on the server. So it has no way to specify whether the token is legal, and it is also possible to rarely check whether the token value in the cookie and the token value in the parameter are the same. This means that you can send a fake token and still be able to effectively implement a CSRF attack.   
This attack involves two steps: first, you use a session fixation technique to confirm that the victim's browser is using your provided session containing a fake token, and then the second step uses the same token in the parameters to execute This CSRF attack.   
1. session is fixed. This is an attack that allows you to control the victim's cookie storage;
2. Perform the following request to implement a CSRF attack    
    
```
POST /change_password
Cookie: CSRF_TOK=FAKE_TOKEN;
POST body:
new_password=qwerty &csrf_tok=FAKE_TOKEN
```
### CSRF defense in the Referer Field 
If attack.com is a controllable domain name, bank.com is a domain name to be attacked. This site does not use a CSRF token but checks the referer field. What should you do?

#### Remove referer field
Same as sending an empty token value, sometimes you can simply bypass the CSRF defense by simply removing the referer field. You can add the following meta tags to vulnerable pages.   
`<meta name =“referrer”content =“no-referrer”>`
The application may only verify after sending it, in which case you can bypass its CSRF defense.   

#### Bypassing regular expressions
If the referer check is based on a whitelist, you can try to bypass the regular expression for validating URLs. For example, you can try to place the victim's domain name in the secondary URL area or URL directory area in the referer's URL.    
If a site checks the "bank.com" field in the referer field, then "bank.com.attacker.com" or "attakcer.com/bank.com" may bypass this detection    
