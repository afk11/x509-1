<?php

declare(strict_types = 1);

namespace X509\AttributeCertificate;

use ASN1\Element;
use ASN1\Type\UnspecifiedType;
use X501\ASN1\Name;
use X509\Certificate\Certificate;
use X509\GeneralName\DirectoryName;
use X509\GeneralName\GeneralNames;

/**
 * Base class implementing <i>AttCertIssuer</i> ASN.1 CHOICE type.
 *
 * @link https://tools.ietf.org/html/rfc5755#section-4.1
 */
abstract class AttCertIssuer
{
    /**
     * Generate ASN.1 element.
     *
     * @return Element
     */
    abstract public function toASN1();
    
    /**
     * Check whether AttCertIssuer identifies given certificate.
     *
     * @param Certificate $cert
     * @return bool
     */
    abstract public function identifiesPKC(Certificate $cert): bool;
    
    /**
     * Initialize from distinguished name.
     *
     * This conforms to RFC 5755 which states that only v2Form must be used,
     * and issuerName must contain exactly one GeneralName of DirectoryName
     * type.
     *
     * @link https://tools.ietf.org/html/rfc5755#section-4.2.3
     * @param Name $name
     * @return self
     */
    public static function fromName(Name $name): self
    {
        return new V2Form(new GeneralNames(new DirectoryName($name)));
    }
    
    /**
     * Initialize from an issuer's public key certificate.
     *
     * @param Certificate $cert
     * @return self
     */
    public static function fromPKC(Certificate $cert): self
    {
        return self::fromName($cert->tbsCertificate()->subject());
    }
    
    /**
     * Initialize from ASN.1.
     *
     * @param UnspecifiedType $el CHOICE
     * @throws \UnexpectedValueException
     * @return self
     */
    public static function fromASN1(UnspecifiedType $el): self
    {
        if (!$el->isTagged()) {
            throw new \UnexpectedValueException("v1Form issuer not supported.");
        }
        $tagged = $el->asTagged();
        switch ($tagged->tag()) {
            case 0:
                return V2Form::fromV2ASN1(
                    $tagged->asImplicit(Element::TYPE_SEQUENCE)->asSequence());
        }
        throw new \UnexpectedValueException("Unsupported issuer type.");
    }
}
