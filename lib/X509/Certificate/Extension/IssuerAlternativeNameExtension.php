<?php

namespace X509\Certificate\Extension;

use ASN1\Type\Constructed\Sequence;
use X509\GeneralName\GeneralNames;


/**
 * Implements 'Issuer Alternative Name' certificate extension.
 *
 * @link https://tools.ietf.org/html/rfc5280#section-4.2.1.7
 */
class IssuerAlternativeNameExtension extends Extension
{
	/**
	 * Names.
	 *
	 * @var GeneralNames
	 */
	protected $_names;
	
	/**
	 * Constructor
	 *
	 * @param bool $critical
	 * @param GeneralNames $names
	 */
	public function __construct($critical, GeneralNames $names) {
		parent::__construct(self::OID_ISSUER_ALT_NAME, $critical);
		$this->_names = $names;
	}
	
	protected static function _fromDER($data, $critical) {
		return new self($critical, 
			GeneralNames::fromASN1(Sequence::fromDER($data)));
	}
	
	/**
	 * Get names.
	 *
	 * @return GeneralNames
	 */
	public function names() {
		return $this->_names;
	}
	
	protected function _valueASN1() {
		return $this->_names->toASN1();
	}
}
