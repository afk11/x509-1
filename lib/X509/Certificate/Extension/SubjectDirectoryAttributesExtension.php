<?php

namespace X509\Certificate\Extension;

use ASN1\Element;
use ASN1\Type\Constructed\Sequence;
use X501\ASN1\Attribute;
use X509\Feature\AttributeContainer;


/**
 * Implements 'Subject Directory Attributes' certificate extension.
 *
 * @link https://tools.ietf.org/html/rfc5280#section-4.2.1.8
 */
class SubjectDirectoryAttributesExtension extends Extension implements 
	\Countable, \IteratorAggregate
{
	use AttributeContainer;
	
	/**
	 * Constructor
	 *
	 * @param bool $critical
	 * @param Attribute ...$attribs One or more Attribute objects
	 */
	public function __construct($critical, Attribute ...$attribs) {
		parent::__construct(self::OID_SUBJECT_DIRECTORY_ATTRIBUTES, $critical);
		$this->_attributes = $attribs;
	}
	
	protected static function _fromDER($data, $critical) {
		$attribs = array_map(
			function (Element $el) {
				return Attribute::fromASN1(
					$el->expectType(Element::TYPE_SEQUENCE));
			}, Sequence::fromDER($data)->elements());
		return new self($critical, ...$attribs);
	}
	
	protected function _valueASN1() {
		if (!count($this->_attributes)) {
			throw new \LogicException(
				"SubjectDirectoryAttributes must have at least one Attribute");
		}
		$elements = array_map(
			function (Attribute $attr) {
				return $attr->toASN1();
			}, array_values($this->_attributes));
		return new Sequence(...$elements);
	}
}
