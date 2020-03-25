<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneModele
 *
 * @ORM\Table(name="one_modele")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneModeleRepository")
 */
class OneModele
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="head_label", type="string", length=255, nullable=true)
     */
    private $headLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="head_color", type="string", length=255)
     */
    private $headColor;

    /**
     * @var string
     *
     * @ORM\Column(name="font_family", type="string", length=255)
     */
    private $fontFamily;

    /**
     * @var integer
     *
     * @ORM\Column(name="font_size", type="integer")
     */
    private $fontSize;

    /**
     * @var string
     *
     * @ORM\Column(name="font_color", type="string", length=255)
     */
    private $fontColor;

    /**
     * @var string
     *
     * @ORM\Column(name="logo_position", type="string", length=255, nullable=true)
     */
    private $logoPosition;

    /**
     * @var integer
     *
     * @ORM\Column(name="logo_width", type="integer", nullable=true)
     */
    private $logoWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="logo_height", type="integer", nullable=true)
     */
    private $logoHeight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_company_name", type="boolean")
     */
    private $showCompanyName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_reglement", type="boolean")
     */
    private $showReglement;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_num_client", type="boolean")
     */
    private $showNumClient;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_tel_client", type="boolean")
     */
    private $showTelClient;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_shipping_address", type="boolean")
     */
    private $showShippingAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address_label", type="string", length=255)
     */
    private $shippingAddressLabel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="billing_address_right", type="boolean")
     */
    private $billingAddressRight;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_address_label", type="string", length=255)
     */
    private $billingAddressLabel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_tva_intracom", type="boolean")
     */
    private $showTvaIntracom;

    /**
     * @var string
     *
     * @ORM\Column(name="designation_label", type="string", length=255)
     */
    private $designationLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity_label", type="string", length=255)
     */
    private $quantityLabel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_quantity", type="boolean")
     */
    private $showQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price_label", type="string", length=255)
     */
    private $priceLabel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_price", type="boolean")
     */
    private $showPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_unit", type="boolean")
     */
    private $showUnit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_product_code", type="boolean")
     */
    private $showProductCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_deadline", type="boolean")
     */
    private $showDeadline;

    /**
     * @var string
     *
     * @ORM\Column(name="global_note", type="text", nullable=true)
     */
    private $globalNote;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_payment_info", type="boolean")
     */
    private $showPaymentInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_info_label", type="string", length=255)
     */
    private $paymentInfoLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_info_default", type="text", nullable=true)
     */
    private $paymentInfoDefault;

    /**
     * @var string
     *
     * @ORM\Column(name="footer_page_content", type="text", nullable=true)
     */
    private $footerPageContent;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modele_name", type="string", length=255)
     */
    private $modeleName;

    /**
     * @var string
     *
     * @ORM\Column(name="modele_description", type="text", nullable=true)
     */
    private $modeleDescription;


    /**
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set headLabel
     *
     * @param string $headLabel
     *
     * @return OneModele
     */
    public function setHeadLabel($headLabel)
    {
        $this->headLabel = $headLabel;
    
        return $this;
    }

    /**
     * Get headLabel
     *
     * @return string
     */
    public function getHeadLabel()
    {
        return $this->headLabel;
    }

    /**
     * Set headColor
     *
     * @param string $headColor
     *
     * @return OneModele
     */
    public function setHeadColor($headColor)
    {
        $this->headColor = $headColor;
    
        return $this;
    }

    /**
     * Get headColor
     *
     * @return string
     */
    public function getHeadColor()
    {
        return $this->headColor;
    }

    /**
     * Set fontFamily
     *
     * @param string $fontFamily
     *
     * @return OneModele
     */
    public function setFontFamily($fontFamily)
    {
        $this->fontFamily = $fontFamily;
    
        return $this;
    }

    /**
     * Get fontFamily
     *
     * @return string
     */
    public function getFontFamily()
    {
        return $this->fontFamily;
    }

    /**
     * Set fontSize
     *
     * @param integer $fontSize
     *
     * @return OneModele
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
    
        return $this;
    }

    /**
     * Get fontSize
     *
     * @return integer
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * Set fontColor
     *
     * @param string $fontColor
     *
     * @return OneModele
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;
    
        return $this;
    }

    /**
     * Get fontColor
     *
     * @return string
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }

    /**
     * Set logoPosition
     *
     * @param string $logoPosition
     *
     * @return OneModele
     */
    public function setLogoPosition($logoPosition)
    {
        $this->logoPosition = $logoPosition;
    
        return $this;
    }

    /**
     * Get logoPosition
     *
     * @return string
     */
    public function getLogoPosition()
    {
        return $this->logoPosition;
    }

    /**
     * Set logoWidth
     *
     * @param integer $logoWidth
     *
     * @return OneModele
     */
    public function setLogoWidth($logoWidth)
    {
        $this->logoWidth = $logoWidth;
    
        return $this;
    }

    /**
     * Get logoWidth
     *
     * @return integer
     */
    public function getLogoWidth()
    {
        return $this->logoWidth;
    }

    /**
     * Set logoHeight
     *
     * @param integer $logoHeight
     *
     * @return OneModele
     */
    public function setLogoHeight($logoHeight)
    {
        $this->logoHeight = $logoHeight;
    
        return $this;
    }

    /**
     * Get logoHeight
     *
     * @return integer
     */
    public function getLogoHeight()
    {
        return $this->logoHeight;
    }

    /**
     * Set showCompanyName
     *
     * @param boolean $showCompanyName
     *
     * @return OneModele
     */
    public function setShowCompanyName($showCompanyName)
    {
        $this->showCompanyName = $showCompanyName;
    
        return $this;
    }

    /**
     * Get showCompanyName
     *
     * @return boolean
     */
    public function getShowCompanyName()
    {
        return $this->showCompanyName;
    }

    /**
     * Set showReglement
     *
     * @param boolean $showReglement
     *
     * @return OneModele
     */
    public function setShowReglement($showReglement)
    {
        $this->showReglement = $showReglement;
    
        return $this;
    }

    /**
     * Get showReglement
     *
     * @return boolean
     */
    public function getShowReglement()
    {
        return $this->showReglement;
    }

    /**
     * Set showNumClient
     *
     * @param boolean $showNumClient
     *
     * @return OneModele
     */
    public function setShowNumClient($showNumClient)
    {
        $this->showNumClient = $showNumClient;
    
        return $this;
    }

    /**
     * Get showNumClient
     *
     * @return boolean
     */
    public function getShowNumClient()
    {
        return $this->showNumClient;
    }

    /**
     * Set showTelClient
     *
     * @param boolean $showTelClient
     *
     * @return OneModele
     */
    public function setShowTelClient($showTelClient)
    {
        $this->showTelClient = $showTelClient;
    
        return $this;
    }

    /**
     * Get showTelClient
     *
     * @return boolean
     */
    public function getShowTelClient()
    {
        return $this->showTelClient;
    }

    /**
     * Set showShippingAddress
     *
     * @param boolean $showShippingAddress
     *
     * @return OneModele
     */
    public function setShowShippingAddress($showShippingAddress)
    {
        $this->showShippingAddress = $showShippingAddress;
    
        return $this;
    }

    /**
     * Get showShippingAddress
     *
     * @return boolean
     */
    public function getShowShippingAddress()
    {
        return $this->showShippingAddress;
    }

    /**
     * Set shippingAddressLabel
     *
     * @param string $shippingAddressLabel
     *
     * @return OneModele
     */
    public function setShippingAddressLabel($shippingAddressLabel)
    {
        $this->shippingAddressLabel = $shippingAddressLabel;
    
        return $this;
    }

    /**
     * Get shippingAddressLabel
     *
     * @return string
     */
    public function getShippingAddressLabel()
    {
        return $this->shippingAddressLabel;
    }

    /**
     * Set billingAddressRight
     *
     * @param boolean $billingAddressRight
     *
     * @return OneModele
     */
    public function setBillingAddressRight($billingAddressRight)
    {
        $this->billingAddressRight = $billingAddressRight;
    
        return $this;
    }

    /**
     * Get billingAddressRight
     *
     * @return boolean
     */
    public function getBillingAddressRight()
    {
        return $this->billingAddressRight;
    }

    /**
     * Set billingAddressLabel
     *
     * @param string $billingAddressLabel
     *
     * @return OneModele
     */
    public function setBillingAddressLabel($billingAddressLabel)
    {
        $this->billingAddressLabel = $billingAddressLabel;
    
        return $this;
    }

    /**
     * Get billingAddressLabel
     *
     * @return string
     */
    public function getBillingAddressLabel()
    {
        return $this->billingAddressLabel;
    }

    /**
     * Set showTvaIntracom
     *
     * @param boolean $showTvaIntracom
     *
     * @return OneModele
     */
    public function setShowTvaIntracom($showTvaIntracom)
    {
        $this->showTvaIntracom = $showTvaIntracom;
    
        return $this;
    }

    /**
     * Get showTvaIntracom
     *
     * @return boolean
     */
    public function getShowTvaIntracom()
    {
        return $this->showTvaIntracom;
    }

    /**
     * Set designationLabel
     *
     * @param string $designationLabel
     *
     * @return OneModele
     */
    public function setDesignationLabel($designationLabel)
    {
        $this->designationLabel = $designationLabel;
    
        return $this;
    }

    /**
     * Get designationLabel
     *
     * @return string
     */
    public function getDesignationLabel()
    {
        return $this->designationLabel;
    }

    /**
     * Set quantityLabel
     *
     * @param string $quantityLabel
     *
     * @return OneModele
     */
    public function setQuantityLabel($quantityLabel)
    {
        $this->quantityLabel = $quantityLabel;
    
        return $this;
    }

    /**
     * Get quantityLabel
     *
     * @return string
     */
    public function getQuantityLabel()
    {
        return $this->quantityLabel;
    }

    /**
     * Set showQuantity
     *
     * @param boolean $showQuantity
     *
     * @return OneModele
     */
    public function setShowQuantity($showQuantity)
    {
        $this->showQuantity = $showQuantity;
    
        return $this;
    }

    /**
     * Get showQuantity
     *
     * @return boolean
     */
    public function getShowQuantity()
    {
        return $this->showQuantity;
    }

    /**
     * Set priceLabel
     *
     * @param string $priceLabel
     *
     * @return OneModele
     */
    public function setPriceLabel($priceLabel)
    {
        $this->priceLabel = $priceLabel;
    
        return $this;
    }

    /**
     * Get priceLabel
     *
     * @return string
     */
    public function getPriceLabel()
    {
        return $this->priceLabel;
    }

    /**
     * Set showPrice
     *
     * @param boolean $showPrice
     *
     * @return OneModele
     */
    public function setShowPrice($showPrice)
    {
        $this->showPrice = $showPrice;
    
        return $this;
    }

    /**
     * Get showPrice
     *
     * @return boolean
     */
    public function getShowPrice()
    {
        return $this->showPrice;
    }

    /**
     * Set showUnit
     *
     * @param boolean $showUnit
     *
     * @return OneModele
     */
    public function setShowUnit($showUnit)
    {
        $this->showUnit = $showUnit;
    
        return $this;
    }

    /**
     * Get showUnit
     *
     * @return boolean
     */
    public function getShowUnit()
    {
        return $this->showUnit;
    }

    /**
     * Set showProductCode
     *
     * @param boolean $showProductCode
     *
     * @return OneModele
     */
    public function setShowProductCode($showProductCode)
    {
        $this->showProductCode = $showProductCode;
    
        return $this;
    }

    /**
     * Get showProductCode
     *
     * @return boolean
     */
    public function getShowProductCode()
    {
        return $this->showProductCode;
    }

    /**
     * Set showDeadline
     *
     * @param boolean $showDeadline
     *
     * @return OneModele
     */
    public function setShowDeadline($showDeadline)
    {
        $this->showDeadline = $showDeadline;
    
        return $this;
    }

    /**
     * Get showDeadline
     *
     * @return boolean
     */
    public function getShowDeadline()
    {
        return $this->showDeadline;
    }

    /**
     * Set globalNote
     *
     * @param string $globalNote
     *
     * @return OneModele
     */
    public function setGlobalNote($globalNote)
    {
        $this->globalNote = $globalNote;
    
        return $this;
    }

    /**
     * Get globalNote
     *
     * @return string
     */
    public function getGlobalNote()
    {
        return $this->globalNote;
    }

    /**
     * Set showPaymentInfo
     *
     * @param boolean $showPaymentInfo
     *
     * @return OneModele
     */
    public function setShowPaymentInfo($showPaymentInfo)
    {
        $this->showPaymentInfo = $showPaymentInfo;
    
        return $this;
    }

    /**
     * Get showPaymentInfo
     *
     * @return boolean
     */
    public function getShowPaymentInfo()
    {
        return $this->showPaymentInfo;
    }

    /**
     * Set paymentInfoLabel
     *
     * @param string $paymentInfoLabel
     *
     * @return OneModele
     */
    public function setPaymentInfoLabel($paymentInfoLabel)
    {
        $this->paymentInfoLabel = $paymentInfoLabel;
    
        return $this;
    }

    /**
     * Get paymentInfoLabel
     *
     * @return string
     */
    public function getPaymentInfoLabel()
    {
        return $this->paymentInfoLabel;
    }

    /**
     * Set paymentInfoDefault
     *
     * @param string $paymentInfoDefault
     *
     * @return OneModele
     */
    public function setPaymentInfoDefault($paymentInfoDefault)
    {
        $this->paymentInfoDefault = $paymentInfoDefault;
    
        return $this;
    }

    /**
     * Get paymentInfoDefault
     *
     * @return string
     */
    public function getPaymentInfoDefault()
    {
        return $this->paymentInfoDefault;
    }

    /**
     * Set footerPageContent
     *
     * @param string $footerPageContent
     *
     * @return OneModele
     */
    public function setFooterPageContent($footerPageContent)
    {
        $this->footerPageContent = $footerPageContent;
    
        return $this;
    }

    /**
     * Get footerPageContent
     *
     * @return string
     */
    public function getFooterPageContent()
    {
        return $this->footerPageContent;
    }

    /**
     * Set modeleName
     *
     * @param string $modeleName
     *
     * @return OneModele
     */
    public function setModeleName($modeleName)
    {
        $this->modeleName = $modeleName;
    
        return $this;
    }

    /**
     * Get modeleName
     *
     * @return string
     */
    public function getModeleName()
    {
        return $this->modeleName;
    }

    /**
     * Set modeleDescription
     *
     * @param string $modeleDescription
     *
     * @return OneModele
     */
    public function setModeleDescription($modeleDescription)
    {
        $this->modeleDescription = $modeleDescription;
    
        return $this;
    }

    /**
     * Get modeleDescription
     *
     * @return string
     */
    public function getModeleDescription()
    {
        return $this->modeleDescription;
    }

    /**
     * @return Dossier
     */
    public function getDossier(){
        return $this->dossier;
    }

    /**
     * @param Dossier $dossier
     * @return $this
     */
    public function setDossier(Dossier $dossier){
        $this->dossier = $dossier;
        return  $this;
    }
}
