### CSRF (Cross-site request forgery) AKA one click attack / session riding, CSRF / XSRF.    

#### That this all about? 
CSRF (Cross-site request forgery) cross-site request forgery: also known as "One Click Attack"
 or Session Riding, usually abbreviated as CSRF or XSRF, is a malicious use of a website. 
 Although it sounds like cross-site scripting (XSS), it is very different from XSS, which leverages trusted 
 users within the site, while CSRF leverages trusted websites by disguising requests from trusted users. 
 Compared with XSS attacks, CSRF attacks are often less prevalent (hence the resources to prevent them) 
 and difficult to prevent, so they are considered more dangerous than XSS attacks.   
 
 In other words CSRF leverages trusted websites. It attacks specifically target state-changing requests 
 it is an attack that forces an end user to execute unwanted actions on a web application in which they're 
 currently authenticated.
  
#### Cause 

In fact, the reason for the csrf vulnerability is that the website's cookies do not expire in the browser. 
As long as you do not close the browser or log out, then as long as you visit this website, you will be logged 
in by default. During this period, the attacker sends the constructed csrf script or a link containing the 
csrf script, which may perform some functions that the user does not want to do 
(such as adding an account, etc.). This operation is not what the user really wants to perform.

### Harm 
Attackers have stolen your identity and sent malicious requests on your behalf. The things that CSRF can do 
include: send emails, send messages on your behalf, stealing your account, and even purchase goods, 
virtual currency transfers ... The problems caused include: leakage of personal privacy and property security.