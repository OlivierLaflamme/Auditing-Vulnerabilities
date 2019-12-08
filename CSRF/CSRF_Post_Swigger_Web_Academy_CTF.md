## LAB 0
CSRF Vulnerability with no defense    
Hint: Hint: None. Point of the lab is to construct a web page that will launch a CSRF attack and change the users email address.    
The credentials are: carlos / montoya.    
![Capture](https://user-images.githubusercontent.com/25066959/70395656-f43fca00-19ce-11ea-845b-6fc7abaa12b2.PNG)   
Its a POST method and there is a cookie in the request header.
![Capture](https://user-images.githubusercontent.com/25066959/70395738-bd1de880-19cf-11ea-99bf-8c8bb75ff5fa.PNG)

Now we go to the exploit server and craft our response 
![Capture](https://user-images.githubusercontent.com/25066959/70396119-02441980-19d4-11ea-8084-493ac0606ea1.PNG)

and voila    
![Capture](https://user-images.githubusercontent.com/25066959/70396131-1be56100-19d4-11ea-9be0-293a7aef4f43.PNG)

