<?php

/**
 * Swift Mailer Unit Test Case for the EmbeddedFile class.
 * @package Swift
 * @subpackage Tests
 * @author Chris Corbyn <chris@w3style.co.uk>
 */


/**
 * Swift Mailer Unit Test Case for the EmbeddedFile class.
 * @package Swift
 * @subpackage Tests
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class TestOfEmbeddedFile extends UnitTestCase
{
  function setUp()
  {
    Swift_Errors::reset();
  }
  /**
   * Embedded files need headers like attachments, but inline and with content-id.
   */
  function testFileHasNeededHeaders()
  {
    $file = new Swift_Message_EmbeddedFile("some string");
    $structure =& $file->build(); $structure = $structure->readFull();
    $this->assertPattern(
      "~Content-Type: application/octet-stream;\\s+name=.*?\r\nContent-Transfer-Encoding: base64\r\n".
      "Content-Disposition: inline;\\s* filename=(\"?).*?\\1\r\n" .
      "Content-ID: <.*?>\r\n\r\n.*~s", $structure);
    
    $file = new Swift_Message_EmbeddedFile(new Swift_File("../files/manchester.jpeg"));
    $structure =& $file->build(); $structure = $structure->readFull();
    $this->assertPattern(
      "~Content-Type: application/octet-stream;\\s+name=.*?\r\nContent-Transfer-Encoding: base64\r\n".
      "Content-Disposition: inline;\\s* filename=(\"?).*?\\1\r\n" .
      "Content-ID: <.*?>\r\n\r\n.*~s", $structure);
  }
  /**
   * The content-id header must be settable & gettable.
   */
  function testContentIdCanBeSetAndRetreived()
  {
    $file = new Swift_Message_EmbeddedFile("some string", "foo", "image/jpeg", "my_cid");
    $this->assertEqual("my_cid", $file->getContentId());
    $structure =& $file->build(); $structure = $structure->readFull();
    $this->assertPattern(
      "~Content-Type: image/jpeg;\\s+name=.*?\r\nContent-Transfer-Encoding: base64\r\n".
      "Content-Disposition: inline;\\s* filename=foo\r\n" .
      "Content-ID: <my_cid>\r\n\r\n.*~s", $structure);
    
    $file->setContentId("another_cid");
    $this->assertEqual("another_cid", $file->getContentId());
    $structure =& $file->build(); $structure = $structure->readFull();
    $this->assertPattern(
      "~Content-Type: image/jpeg;\\s+name=.*?\r\nContent-Transfer-Encoding: base64\r\n".
      "Content-Disposition: inline;\\s* filename=foo\r\n" .
      "Content-ID: <another_cid>\r\n\r\n.*~s", $structure);
  }
  
  function testFilenameSetsInConstructor()
  {
    $file = new Swift_Message_EmbeddedFile("some string", "foo", "image/jpeg", "my_cid");
    $this->assertEqual("foo", $file->getFileName());
    $structure =& $file->build();
    $structure = $structure->readFull();
    $this->assertPattern(
      "~Content-Type: image/jpeg;\\s+name=foo\r\nContent-Transfer-Encoding: base64\r\n".
      "Content-Disposition: inline;\\s* filename=foo\r\n" .
      "Content-ID: <my_cid>\r\n\r\n.*~s", $structure);
      
    $file = new Swift_Message_EmbeddedFile(new Swift_File("../files/gecko.png"), "foo.png", "image/jpeg", "my_cid");
    $this->assertEqual("foo.png", $file->getFileName());
    $structure =& $file->build();
    $structure = $structure->readFull();
    $this->assertPattern(
      "~Content-Type: image/jpeg;\\s+name=foo\\.png\r\nContent-Transfer-Encoding: base64\r\n".
      "Content-Disposition: inline;\\s* filename=foo\\.png\r\n" .
      "Content-ID: <my_cid>\r\n\r\n.*~s", $structure);
  }
}
