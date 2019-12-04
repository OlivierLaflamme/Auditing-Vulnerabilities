## NOTE
JAVA has more and more methods for parsing XML. There are four common types, namely: DOM , DOM4J, JDOM, and SAX. Take these four as examples to show the XXE vulnerability.    

1. DOM READ XML    
```
HttpServletResponse  response )  throws  ServletException ,  IOException  {       
        String  result = "" ; 
        try  { 
            // DOM Read XML 
            DocumentBuilderFactory  dbf  =  DocumentBuilderFactory . newInstance ();      
            DocumentBuilder  db  =  dbf . newDocumentBuilder ();                   
            Document  doc  =  db . parse (Request . the getInputStream ());

            String  username  =  getValueByTagName ( doc , "username" ); 
            String  password  =  getValueByTagName ( doc , "password" ); 
            if ( username . Equals ( USERNAME )  &&  password . Equals ( PASSWORD )) { 
                result  =  String . Format ( "<result > <code>% d </ code> <msg>% s </ msg> </ result> " , 1 , username );
            } else{ 
                result  =  String . format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 0 , username ); 
            } 
        }  catch  ( ParserConfigurationException  e )  { 
            e . printStackTrace (); 
            result  =  String . format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 3 , e . getMessage ());
        }  catch  ( SAXException  e )  {
            e . printStackTrace (); 
            result  =  String . format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 3 , e . getMessage ()); 
        } 
        response . setContentType ( "text / xml; charset = UTF-8" ); 
        response . getWriter (). append ( result ); 
    }
```    


2. DOM4J Read XML 
```
protected  void  doPost ( HttpServletRequest  request ,  HttpServletResponse  response )  throws  ServletException ,  IOException  {           
        String  result = "" ; 
        try  { 
            // DOM4J Read XML 
            SAXReader  saxReader  =  new  SAXReader (); 
            Document  document  =  saxReader . read ( request . getInputStream ());

            String  username  =  getValueByTagName2 ( document , "username" ); 
            String  password  =  getValueByTagName2 ( document , "password" );

            if ( username . equals ( USERNAME )  &&  password . equals ( PASSWORD )) { 
                result  =  String . format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 1 , username ); 
            } else { 
                result  =  String . Format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 0 , username ); 
            }                

        }  catch  ( DocumentException   e )  { 
            System . out . println ( e . getMessage ()); 
        }  
        response . setContentType ( "text / xml; charset = UTF-8" ); 
        response . getWriter (). append ( result ); 
    }
```    

3. JDOM2 Read XML 
```
protected  void  doPost ( HttpServletRequest  request ,  HttpServletResponse  response )  throws  ServletException ,  IOException  {              
        String  result = "" ; 
        try  { 
            // JDOM2 Read XML     
            SAXBuilder  builder  =  new  SAXBuilder ();   
            Document  document  =  builder . build ( request . getInputStream ());

            String  username  =  getValueByTagName3 ( document , "username" ); 
            String  password  =  getValueByTagName3 ( document , "password" );

            if ( username . equals ( USERNAME )  &&  password . equals ( PASSWORD )) { 
                result  =  String . format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 1 , username ); 
            } else { 
                result  =  String . Format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 0 , username ); 
            }

        }  catch  ( JDOMException   e )  { 
            System . out . println ( e . getMessage ()); 
        }  
        response . setContentType ( "text / xml; charset = UTF-8" ); 
        response . getWriter (). append ( result ); 
    }
```

4. SAX Read XML 
```
protected  void  doPost ( HttpServletRequest  request ,  HttpServletResponse  response )  throws  ServletException ,  IOException  {       
        //https://blog.csdn.net/u011024652/article/details/51516220 
        String  result = "" ; 
        try  { 
            // SAX Read XML 
            SAXParserFactory  factory   =  SAXParserFactory . NewInstance ();  
            SAXParser  saxparser  =  factory . NewSAXParser ();   
            SAXHandler  handler  = new  SAXHandler ();   
            saxparser . parse ( request . getInputStream (),  handler ); 
            // For simplicity, no data is extracted from the child elements, as long as parse () is called to parse the xml, the xxe vulnerability has been triggered 
            // no echo blind xxe 
             result  =  String . format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 0 , 1 );

        }  catch  ( ParserConfigurationException  e )  { 
            e . printStackTrace (); 
            result  =  String . format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result>" , 3 , e . getMessage ()); 
        }  catch  ( SAXException  e )  { 
            e . printStackTrace (); 
            result  =  String . format ( "<result> <code>% d </ code> <msg>% s </ msg> </ result > ", 3 ,e . getMessage ()); 
        } 
        response . setContentType ( "text / xml; charset = UTF-8" ); 
        response . getWriter (). append ( result ); 
    }
```

Other 1.   
```
public  class  XXE  {

    @RequestMapping ( value  =  "/ xmlReader" ,  Method  =  RequestMethod . The POST ) 
    @ResponseBody 
    public   String  xxe_xmlReader ( the HttpServletRequest  Request )  { 
        the try  { 
            String  xml_con  =  the getBody ( Request ); 
            the System . OUT . The println ( xml_con ); 
            the XMLReader  xmlReader  =  the XMLReaderFactory . createXMLReader (); 
            xmlReader. parse (  new  InputSource ( new  StringReader ( xml_con ))  );   // parse xml 
            return  "ok" ; 
        }  catch  ( Exception  e )  { 
            System . out . println ( e ); 
            return  "except" ; 
        } 
    }
```

Other 2. 
```
@RequestMapping ( value  =  "/ SAXBuilder" ,  Method  =  RequestMethod . The POST ) 
    @ResponseBody 
    public   String  xxe_SAXBuilder ( the HttpServletRequest  Request )  { 
        the try  { 
            String  xml_con  =  the getBody ( Request ); 
            the System . OUT . The println ( xml_con );

            SAXBuilder  builder  =  new  SAXBuilder (); 
            org . Jdom2 . Document  document  =  builder . Build (  new  InputSource ( new  StringReader ( xml_con ))  );   // cause xxe 
            return  "ok" ; 
        }  catch  ( Exception  e )  { 
            System . Out . println ( e ); 
            return  "except" ; 
        } 
    }
```

Other 3.   
```
@RequestMapping ( value  =  "/ SAXReader" ,  Method  =  RequestMethod . The POST ) 
    @ResponseBody 
    public   String  xxe_SAXReader ( the HttpServletRequest  Request )  { 
        the try  { 
            String  xml_con  =  the getBody ( Request ); 
            the System . OUT . The println ( xml_con );

            SAXReader  reader  =  new  SAXReader (); 
            org . Dom4j . Document  document  =  reader . Read (   new  InputSource ( new  StringReader ( xml_con ))  );  // cause xxe

            return  "ok" ; 
        }  catch  ( Exception  e )  { 
            System . out . println ( e ); 
            return  "except" ; 
        } 
    }
```

Other 4.    
```
@RequestMapping ( value  =  "/ the SAXParser" ,  Method  =  RequestMethod . The POST ) 
    @ResponseBody 
    public  String  xxe_SAXParser ( the HttpServletRequest  Request )  { 
        the try  { 
            String  xml_con  =  the getBody ( Request ); 
            the System . OUT . The println ( xml_con );

            The SAXParserFactory  SPF  =  the SAXParserFactory . The newInstance (); 
            the SAXParser  Parser  =  SPF . NewSAXParser (); 
            Parser . The parse ( new new  the InputSource ( new new  the StringReader ( xml_con )),  new new  the DefaultHandler ());   // the parse XML

            return  "test" ; 
        }  catch  ( Exception  e )  { 
            System . out . println ( e ); 
            return  "except" ; 
        } 
    }
```

Other 5.   
```
// There are echoed XXE, 
    @RequestMapping ( value  =  "/ DocumentBuilder_return" ,  Method  =  RequestMethod . The POST ) 
    @ResponseBody 
    public  String  xxeDocumentBuilderReturn ( the HttpServletRequest  Request )  { 
        the try  { 
            String  xml_con  =  the getBody ( Request ); 
            the System . OUT . The println ( xml_con );

            DocumentBuilderFactory  dbf  =  DocumentBuilderFactory . NewInstance (); 
            DocumentBuilder  db  =  dbf . NewDocumentBuilder (); 
            StringReader  sr  =  new  StringReader ( xml_con ); 
            InputSource  is  =  new  InputSource ( sr ); 
            Document  document  =  db . Parse ( is );   // parse xml

            // iterate xml node name and value 
            the StringBuffer  buf  =  new new  the StringBuffer (); 
            a NodeList  rootNodeList  =  Document . GetChildNodes (); 
            for  ( int  I  =  0 ;  I  <  rootNodeList . GetLength ();  I ++)  { 
                the Node  the rootNode  =  rootNodeList . item ( i ); 
                NodeList  child  =  rootNode . getChildNodes (); 
                for  ( int j  =  0 ;  j  <  child . getLength ();  j ++)  { 
                    Node  node  =  child . item ( j ); 
                    buf . append (  node . getNodeName ()  +  ":"  +  node . getTextContent ()  +  "\ n "  ); 
                } 
            } 
            sr . close (); 
            System . out . println ( buf .toString ()); 
            return  buf . toString (); 
        }  catch  ( Exception  e )  { 
            System . out . println ( e ); 
            return  "except" ; 
        } 
    }


    @RequestMapping ( value  =  "/ the DocumentBuilder" ,  Method  =  RequestMethod . The POST ) 
    @ResponseBody 
    public  String  the DocumentBuilder ( the HttpServletRequest  Request )  { 
        the try  { 
            String  xml_con  =  the getBody ( Request ); 
            the System . OUT . The println ( xml_con );

            DocumentBuilderFactory  dbf  =  DocumentBuilderFactory . NewInstance (); 
            DocumentBuilder  db  =  dbf . NewDocumentBuilder (); 
            StringReader  sr  =  new  StringReader ( xml_con ); 
            InputSource  is  =  new  InputSource ( sr ); 
            Document  document  =  db . Parse ( is );   // parse xml

            // iterate xml node name and value 
            the StringBuffer  Result  =  new new  the StringBuffer (); 
            a NodeList  rootNodeList  =  Document . GetChildNodes (); 
            for  ( int  I  =  0 ;  I  <  rootNodeList . GetLength ();  I ++)  { 
                the Node  the rootNode  =  rootNodeList . item ( i ); 
                NodeList  child  =  rootNode . getChildNodes (); 
                for  (int  j  =  0 ;  j  <  child . getLength ();  j ++)  { 
                    Node  node  =  child . item ( j ); 
                    // To parse XML normally, you need to determine whether it is ELEMENT_NODE type. Otherwise, extra nodes will appear. 
                    if ( child . item ( j ). getNodeType ()  ==  Node . ELEMENT_NODE )  { 
                        result . append (  node . getNodeName ()  +  ":"  + node . getFirstChild (). getNodeValue ()  +  "\ n"  ); 
                    } 
                } 
            } 
            sr . close (); 
            System . out . println ( result . toString ()); 
            return  result . toString (); 
        }  catch  ( Exception  e )  { 
            System . Out . Println ( e ); 
            return  "except" ; 
        } 
    }
```