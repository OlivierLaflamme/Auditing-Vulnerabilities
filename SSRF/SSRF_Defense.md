


#### How to prevent SSRF 
1.  It is easier to filter the returned information and verify the response of the remote server to the request. If the web application is to get a certain type of file. Then verify that the returned information meets the standards before displaying the returned results to the user.    
2.  Disable unwanted protocols and only allow http and https requests. Prevent problems like file: //, gopher: //, ftp: //, etc.    
3. Set URL whitelist or restrict intranet IP (use gethostbyname () to determine if it is an intranet IP)    
4. limit the requested port to the port commonly used by http, such as 80, 443, 8080, 8090
5. Unified error information to avoid users from judging the port status of the remote server based on the error information.
6. Restricted request port can only be web port, only allow access to HTTP and HTTPS requests
7. Restricting Intranet IPs That Cannot Be Accessed to Prevent Attacks on the Intranet
8. Block return details

