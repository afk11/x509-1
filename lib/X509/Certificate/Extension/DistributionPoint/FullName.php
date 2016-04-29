<?php

namespace X509\Certificate\Extension\DistributionPoint;

use X509\GeneralName\GeneralNames;


/**
 * Implements 'fullName' ASN.1 CHOICE type of <i>DistributionPointName</i>
 * used by 'CRL Distribution Points' certificate extension.
 *
 * @link https://tools.ietf.org/html/rfc5280#section-4.2.1.13
 */
class FullName extends DistributionPointName
{
	/**
	 * Names.
	 *
	 * @var GeneralNames $_names
	 */
	protected $_names;
	
	/**
	 * Constructor
	 *
	 * @param GeneralNames $names
	 */
	public function __construct(GeneralNames $names) {
		$this->_tag = self::TAG_FULL_NAME;
		$this->_names = $names;
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
