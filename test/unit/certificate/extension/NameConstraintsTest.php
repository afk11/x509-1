<?php

use ASN1\Type\Constructed\Sequence;
use X509\Certificate\Extension\Extension;
use X509\Certificate\Extension\NameConstraints\GeneralSubtree;
use X509\Certificate\Extension\NameConstraints\GeneralSubtrees;
use X509\Certificate\Extension\NameConstraintsExtension;
use X509\Certificate\Extensions;
use X509\GeneralName\DirectoryName;
use X509\GeneralName\UniformResourceIdentifier;


/**
 * @group certificate
 * @group extension
 */
class NameConstraintsTest extends PHPUnit_Framework_TestCase
{
	const PERMITTED_URI = ".example.com";
	const PERMITTED_DN = "cn=Test";
	const EXCLUDED_URI = "nope.example.com";
	
	public function testCreatePermitted() {
		$subtrees = new GeneralSubtrees(
			new GeneralSubtree(
				new UniformResourceIdentifier(self::PERMITTED_URI)), 
			new GeneralSubtree(DirectoryName::fromDNString(self::PERMITTED_DN)));
		$this->assertInstanceOf(GeneralSubtrees::class, $subtrees);
		return $subtrees;
	}
	
	public function testCreateExcluded() {
		$subtrees = new GeneralSubtrees(
			new GeneralSubtree(new UniformResourceIdentifier(self::EXCLUDED_URI)));
		$this->assertInstanceOf(GeneralSubtrees::class, $subtrees);
		return $subtrees;
	}
	
	/**
	 * @depends testCreatePermitted
	 * @depends testCreateExcluded
	 *
	 * @param GeneralSubtrees $permitted
	 * @param GeneralSubtrees $excluded
	 */
	public function testCreate(GeneralSubtrees $permitted, 
			GeneralSubtrees $excluded) {
		$ext = new NameConstraintsExtension(true, $permitted, $excluded);
		$this->assertInstanceOf(NameConstraintsExtension::class, $ext);
		return $ext;
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Extension $ext
	 */
	public function testOID(Extension $ext) {
		$this->assertEquals(Extension::OID_NAME_CONSTRAINTS, $ext->oid());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Extension $ext
	 */
	public function testCritical(Extension $ext) {
		$this->assertTrue($ext->isCritical());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Extension $ext
	 */
	public function testEncode(Extension $ext) {
		$seq = $ext->toASN1();
		$this->assertInstanceOf(Sequence::class, $seq);
		return $seq->toDER();
	}
	
	/**
	 * @depends testEncode
	 *
	 * @param string $der
	 */
	public function testDecode($der) {
		$ext = NameConstraintsExtension::fromASN1(Sequence::fromDER($der));
		$this->assertInstanceOf(NameConstraintsExtension::class, $ext);
		return $ext;
	}
	
	/**
	 * @depends testCreate
	 * @depends testDecode
	 *
	 * @param Extension $ref
	 * @param Extension $new
	 */
	public function testRecoded(Extension $ref, Extension $new) {
		$this->assertEquals($ref, $new);
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param NameConstraintsExtension $ext
	 */
	public function testPermitted(NameConstraintsExtension $ext) {
		$subtrees = $ext->permittedSubtrees();
		$this->assertInstanceOf(GeneralSubtrees::class, $subtrees);
		return $subtrees;
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param NameConstraintsExtension $ext
	 */
	public function testExcluded(NameConstraintsExtension $ext) {
		$subtrees = $ext->excludedSubtrees();
		$this->assertInstanceOf(GeneralSubtrees::class, $subtrees);
		return $subtrees;
	}
	
	/**
	 * @depends testPermitted
	 *
	 * @param GeneralSubtrees $substrees
	 */
	public function testCount(GeneralSubtrees $subtrees) {
		$this->assertCount(2, $subtrees);
	}
	
	/**
	 * @depends testPermitted
	 *
	 * @param GeneralSubtrees $substrees
	 */
	public function testIterator(GeneralSubtrees $subtrees) {
		$values = array();
		foreach ($subtrees as $subtree) {
			$values[] = $subtree;
		}
		$this->assertCount(2, $values);
		$this->assertContainsOnlyInstancesOf(GeneralSubtree::class, $values);
	}
	
	/**
	 * @depends testPermitted
	 *
	 * @param GeneralSubtrees $substrees
	 */
	public function testPermittedURI(GeneralSubtrees $subtrees) {
		$this->assertEquals(self::PERMITTED_URI, 
			$subtrees->all()[0]->base()
				->string());
	}
	
	/**
	 * @depends testPermitted
	 *
	 * @param GeneralSubtrees $substrees
	 */
	public function testPermittedDN(GeneralSubtrees $subtrees) {
		$this->assertEquals(self::PERMITTED_DN, 
			$subtrees->all()[1]->base()
				->string());
	}
	
	/**
	 * @depends testExcluded
	 *
	 * @param GeneralSubtrees $substrees
	 */
	public function testExcludedURI(GeneralSubtrees $subtrees) {
		$this->assertEquals(self::EXCLUDED_URI, 
			$subtrees->all()[0]->base()
				->string());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param NameConstraintsExtension $ext
	 */
	public function testExtensions(NameConstraintsExtension $ext) {
		$extensions = new Extensions($ext);
		$this->assertTrue($extensions->hasNameConstraints());
		return $extensions;
	}
	
	/**
	 * @depends testExtensions
	 *
	 * @param Extensions $exts
	 */
	public function testFromExtensions(Extensions $exts) {
		$ext = $exts->nameConstraints();
		$this->assertInstanceOf(NameConstraintsExtension::class, $ext);
	}
}
