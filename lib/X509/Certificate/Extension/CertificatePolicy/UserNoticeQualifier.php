<?php

declare(strict_types = 1);

namespace X509\Certificate\Extension\CertificatePolicy;

use ASN1\Element;
use ASN1\Type\UnspecifiedType;
use ASN1\Type\Constructed\Sequence;

/**
 * Implements <i>UserNotice</i> ASN.1 type used by
 * 'Certificate Policies' certificate extension.
 *
 * @link https://tools.ietf.org/html/rfc5280#section-4.2.1.4
 */
class UserNoticeQualifier extends PolicyQualifierInfo
{
    /**
     * Explicit notice text.
     *
     * @var DisplayText $_text
     */
    protected $_text;
    
    /**
     * Notice reference.
     *
     * @var NoticeReference $_ref
     */
    protected $_ref;
    
    /**
     * Constructor.
     *
     * @param DisplayText|null $text
     * @param NoticeReference|null $ref
     */
    public function __construct(DisplayText $text = null, NoticeReference $ref = null)
    {
        $this->_oid = self::OID_UNOTICE;
        $this->_text = $text;
        $this->_ref = $ref;
    }
    
    /**
     *
     * @param UnspecifiedType $el
     * @return self
     */
    public static function fromQualifierASN1(UnspecifiedType $el): self
    {
        $seq = $el->asSequence();
        $ref = null;
        $text = null;
        $idx = 0;
        if ($seq->has($idx, Element::TYPE_SEQUENCE)) {
            $ref = NoticeReference::fromASN1($seq->at($idx++)->asSequence());
        }
        if ($seq->has($idx, Element::TYPE_STRING)) {
            $text = DisplayText::fromASN1($seq->at($idx)->asString());
        }
        return new self($text, $ref);
    }
    
    /**
     * Whether explicit text is present.
     *
     * @return bool
     */
    public function hasExplicitText(): bool
    {
        return isset($this->_text);
    }
    
    /**
     * Get explicit text.
     *
     * @return DisplayText
     */
    public function explicitText(): DisplayText
    {
        if (!$this->hasExplicitText()) {
            throw new \LogicException("explicitText not set.");
        }
        return $this->_text;
    }
    
    /**
     * Whether notice reference is present.
     *
     * @return bool
     */
    public function hasNoticeRef(): bool
    {
        return isset($this->_ref);
    }
    
    /**
     * Get notice reference.
     *
     * @throws \RuntimeException
     * @return NoticeReference
     */
    public function noticeRef(): NoticeReference
    {
        if (!$this->hasNoticeRef()) {
            throw new \LogicException("noticeRef not set.");
        }
        return $this->_ref;
    }
    
    /**
     *
     * {@inheritdoc}
     * @return Sequence
     */
    protected function _qualifierASN1(): Sequence
    {
        $elements = array();
        if (isset($this->_ref)) {
            $elements[] = $this->_ref->toASN1();
        }
        if (isset($this->_text)) {
            $elements[] = $this->_text->toASN1();
        }
        return new Sequence(...$elements);
    }
}
