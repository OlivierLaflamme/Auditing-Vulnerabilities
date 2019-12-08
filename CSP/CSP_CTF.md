# We'll be doing different difficulties of CSP Bypasses on Damn Vulnerable Web Application   

CSP description in the above example is as follows:   

script-src , script: only trust the current domain name   
object-src : Do not trust any URL, ie do not load any resources   
style-src , stylesheet: trust only cdn.example.org and third-party.org   
child-src : Must be loaded using the HTTPS protocol. This has been removed from the web standard and may not be supported in newer browsers.   
Other resources: no restrictions on other resources   

When CSP is enabled, non-CSP-compliant external resources are prevented from loading.    

So how can an attacker bypass the CSP and attack? Lets do this CTF and find out.   

#### Low
Source code: 
```php
<?php

$headerCSP = "Content-Security-Policy: script-src 'self' https://pastebin.com  example.com code.jquery.com https://ssl.google-analytics.com ;"; // allows js from self, pastebin.com, jquery and google analytics.

header($headerCSP);

# https://pastebin.com/raw/R570EE00

?>
<?php
if (isset ($_POST['include'])) {
$page[ 'body' ] .= "
    <script src='" . $_POST['include'] . "'></script>
";
}
$page[ 'body' ] .= '
<form name="csp" method="POST">
    <p>You can include scripts from external sources, examine the Content Security Policy and enter a URL to include here:</p>
    <input size="50" type="text" name="include" value="" id="include" />
    <input type="submit" value="Include" />
</form>
';
```    
Observe headers, list websites that allow JavaScript (script-src)    
`$headerCSP = "Content-Security-Policy: script-src 'self' https://pastebin.com  example.com code.jquery.com https://ssl.google-analytics.com ;"; `   
We can also observe the same results through the developer tools when submitting information on the web page.    
![Capture](https://user-images.githubusercontent.com/25066959/70394985-682aa400-19c8-11ea-95bf-5759d3ffba3d.PNG)   

At this time, you can write a javascript code alert (“hahaha”) on pastebin website, remember the link after saving,    
https://pastebin.com/raw/zSLDySJn

Then enter the link in the above interface, the result is as follows   
![Capture](https://user-images.githubusercontent.com/25066959/70395029-b344b700-19c8-11ea-8955-864739d9e70c.PNG)   

![Capture](https://user-images.githubusercontent.com/25066959/70395043-ceafc200-19c8-11ea-8b7f-09ebff146eda.PNG)
See, the js code saved on pastebin is executed. That's because the pastebin website is trusted. An attacker can save malicious code on a trusted website, and then send the link to the user to click to achieve injection.    

you can do the same in pastbin for like `alert(document.cookie)`   

### Medium 
lets look at the source code.    
```php 
<?php

$headerCSP = "Content-Security-Policy: script-src 'self' 'unsafe-inline' 'nonce-TmV2ZXIgZ29pbmcgdG8gZ2l2ZSB5b3UgdXA=';";

header($headerCSP);

// Disable XSS protections so that inline alert boxes will work
header ("X-XSS-Protection: 0");

# <script nonce="TmV2ZXIgZ29pbmcgdG8gZ2l2ZSB5b3UgdXA=">alert(1)</script>

?>
<?php
if (isset ($_POST['include'])) {
$page[ 'body' ] .= "
    " . $_POST['include'] . "
";
}
$page[ 'body' ] .= '
<form name="csp" method="POST">
    <p>Whatever you enter here gets dropped directly into the page, see if you can get an alert box to pop up.</p>
    <input size="50" type="text" name="include" value="" id="include" />
    <input type="submit" value="Include" />
</form>
';
```   
![Capture](https://user-images.githubusercontent.com/25066959/70395120-978de080-19c9-11ea-8397-56e68109bf68.PNG)   

You can see there are `nonce` and `unsafe-inline` here I think the inspection point is the understanding of the parameters (special values) in the script-src moreover, The legal source of script-src in the http header has changed.    
1. unsafe-inline, which allows the use of inline resources such as inline <script> elements, javascript: URLs, inline event handlers (such as onclick), and inline <style> elements. Must include single quotes.    
2. nonce-source, only specific inline script blocks are allowed, nonce = "TmV2ZXIgZ29pbmcgdG8gZ2l2ZSB5b3UgdXA"    

Basically.... It's even easier now, you can enter the following code directly:    
`<script nonce="TmV2ZXIgZ29pbmcgdG8gZ2l2ZSB5b3UgdXA=">alert("document.cookie")</script>`   
The result is injected successfully.    
![Capture](https://user-images.githubusercontent.com/25066959/70395151-f3f10000-19c9-11ea-923b-df6c3a2f2f22.PNG)   

### Hard 
![Capture](https://user-images.githubusercontent.com/25066959/70395175-1b47cd00-19ca-11ea-9989-eae064208cc3.PNG)   
continue to look at the source code    
```php 
<?php
$headerCSP = "Content-Security-Policy: script-src 'self';";

header($headerCSP);

?>
<?php
if (isset ($_POST['include'])) {
$page[ 'body' ] .= "
    " . $_POST['include'] . "
";
}
$page[ 'body' ] .= '
<form name="csp" method="POST">
    <p>The page makes a call to ' . DVWA_WEB_PAGE_TO_ROOT . '/vulnerabilities/csp/source/jsonp.php to load some code. Modify that page to run your own code.</p>
    <p>1+2+3+4+5=<span id="answer"></span></p>
    <input type="button" id="solve" value="Solve the sum" />
</form>

<script src="source/high.js"></script>
';

vulnerabilities/csp/source/high.js
function clickButton() {
    var s = document.createElement("script");
    s.src = "source/jsonp.php?callback=solveSum";
    document.body.appendChild(s);
}

function solveSum(obj) {
    if ("answer" in obj) {
        document.getElementById("answer").innerHTML = obj['answer'];
    }
}

var solve_button = document.getElementById ("solve");

if (solve_button) {
    solve_button.addEventListener("click", function() {
        clickButton();
    });
}
```
The High level is not the same as the previous level, here is the result of clicking the button... We can also see the script-src 'self' here.    
You really need the source code for this one because you can see the logic from function.clickButton....    

Whats basically happening is the client click on the button, it will create in html <script src = "HTTP://xxxx:yyyy/Vulnerabilities/csp/Source/jsonp.php solveSum callback =?"> </script>` tag such
as script different from Ajax, so the server that can send across domains will return solveSum ({"answer": "15"}) according to the callback request, and you can call solveSum in high.js a little bit around.    
In other words CSP header settings, only allow itself to load JS (script-src 'self').

I dont know how to solve this since there  is no callback in the url, and only access to resources on this site is allowed... LOL...   
