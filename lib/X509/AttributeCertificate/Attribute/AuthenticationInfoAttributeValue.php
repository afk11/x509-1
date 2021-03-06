<?php

declare(strict_types = 1);

namespace X509\AttributeCertificate\Attribute;

use X509\GeneralName\GeneralName;

/**
 * Implements value for 'Service Authentication Information' attribute.
 *
 * @link https://tools.ietf.org/html/rfc5755#section-4.4.1
 */
class AuthenticationInfoAttributeValue extends SvceAuthInfo
{
    const OID = "1.3.6.1.5.5.7.10.1";
    
    /**
     * Constructor.
     *
     * @param GeneralName $service
     * @param GeneralName $ident
     * @param string|null $auth_info
     */
    public function __construct(GeneralName $service, GeneralName $ident,
        $auth_info = null)
    {
        parent::__construct($service, $ident, $auth_info);
        $this->_oid = self::OID;
    }
}
