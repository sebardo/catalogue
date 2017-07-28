<?php

namespace CatalogueBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Entity\Image;
use CoreBundle\Entity\Timestampable;
use CoreBundle\Entity\BaseActor;
use PaymentBundle\Entity\ProductTrait;
use CoreBundle\Entity\MetasableTrait;

/**
 * Product Entity class
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(columns={"created"})})
 * @ORM\Entity(repositoryClass="CatalogueBundle\Entity\Repository\ProductRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Product extends Timestampable
{
    use MetasableTrait;
    use ProductTrait;
    
    const PRICE_TYPE_FIXED = 0;
    const PRICE_TYPE_PERCENT = 1;
    
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="technical_details", type="text", nullable=true)
     */
    private $technicalDetails;
    
    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, unique=true)
     * @ORM\Column(length=255, unique=false)
     */
    private $slug;

    /**
     * Dynamic
     */
    protected $actor;
    
    /**
     * @var Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="products")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=true, onDelete="set null")
     * @Assert\NotBlank
     */
    private $brand;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true, onDelete="set null")
     * @Assert\NotBlank
     */
    private $category;

    /**
     * @var Brand
     *
     * @ORM\ManyToOne(targetEntity="BrandModel", inversedBy="products")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id", nullable=true, onDelete="set null")
     */
    private $model;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AttributeValue", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="product_attributes",
     *                      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *                      inverseJoinColumns={@ORM\JoinColumn(name="attributevalue_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    private $attributeValues;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FeatureValue", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="product_features",
     *                      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *                      inverseJoinColumns={@ORM\JoinColumn(name="featurevalue_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    private $featureValues;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="CoreBundle\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="product_images",
     *                      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *                      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")})
     */
    private $images;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="available", type="boolean")
     */
    private $available;

    /**
     * @var boolean
     *
     * @ORM\Column(name="highlighted", type="boolean")
     */
    private $highlighted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\CatalogueBundle\Entity\ProductStats", mappedBy="product", cascade={"persist", "remove"})
     */
    private $stats;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributeValues = new ArrayCollection();
        $this->featureValues = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->relatedProducts = new ArrayCollection();
        $this->stats = new ArrayCollection();
        $this->active = false;
        $this->available = false;
        $this->highlighted = false;
        $this->freeTransport = false;
    }

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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get $technicalDetails
     *
     * @return string
     */
    public function getTechnicalDetails()
    {
        return $this->technicalDetails;
    }

    /**
     * Set technicalDetails
     *
     * @param string $technicalDetails
     *
     * @return Product
     */
    public function setTechnicalDetails($technicalDetails)
    {
        $this->technicalDetails = $technicalDetails;

        return $this;
    }
            
     /**
     * Get slug
     *
     * @return string
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }
    
    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set actor
     *
     * @param BaseActor $actor
     *
     * @return Product
     */
    public function setActor(BaseActor $actor)
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * Get actor
     *
     * @return Actor
     */
    public function getActor()
    {
        return $this->actor;
    }
    
    /**
     * Get brand
     *
     * @return Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set brand
     *
     * @param Brand $brand
     *
     * @return Product
     */
    public function setBrand(Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Product
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    
    /**
     * Get model
     *
     * @return BrandModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set model
     *
     * @param BrandModel $model
     *
     * @return Product
     */
    public function setModel(BrandModel $model = null)
    {
        $this->model = $model;

        return $this;
    }
    
    /**
     * Add attribute value
     *
     * @param AttributeValue $attributeValue
     *
     * @return Product
     */
    public function addAttributeValue(AttributeValue $attributeValue)
    {
        $this->attributeValues->add($attributeValue);

        return $this;
    }

    /**
     * Remove attribute value
     *
     * @param AttributeValue $attributeValue
     */
    public function removeAttributeValue(AttributeValue $attributeValue)
    {
        $this->attributeValues->removeElement($attributeValue);
    }

    /**
     * Get attribute values
     *
     * @return ArrayCollection
     */
    public function getAttributeValues()
    {
        return $this->attributeValues;
    }

    /**
     * Add feature value
     *
     * @param FeatureValue $featureValue
     *
     * @return Product
     */
    public function addFeatureValue(FeatureValue $featureValue)
    {
        $this->featureValues->add($featureValue);

        return $this;
    }

    /**
     * Remove feature value
     *
     * @param FeatureValue $featureValue
     */
    public function removeFeatureValue(FeatureValue $featureValue)
    {
        $this->featureValues->removeElement($featureValue);
    }

    /**
     * Get feature values
     *
     * @return ArrayCollection
     */
    public function getFeatureValues()
    {
        return $this->featureValues;
    }

    /**
     * Add image
     *
     * @param Image $image
     *
     * @return Product
     */
    public function addImage(Image $image)
    {
        $this->images->add($image);

        return $this;
    }

    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Is active?
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Product
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Is available?
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * Set available
     *
     * @param boolean $available
     *
     * @return Product
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * Is highlighted?
     *
     * @return boolean
     */
    public function isHighlighted()
    {
        return $this->highlighted;
    }

    /**
     * Set highlighted
     *
     * @param boolean $highlighted
     *
     * @return Product
     */
    public function setHighlighted($highlighted)
    {
        $this->highlighted = $highlighted;

        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param \DateTime $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }
    
    
    /**
     * Add stat
     *
     * @param ProductStats $stat
     *
     * @return Product
     */
    public function addStat(ProductStats $stat)
    {
        $this->stats->add($stat);

        return $this;
    }

    
    /**
     * Remove stat
     *
     * @param ProductStats $stat
     */
    public function removeStat(ProductStats $stat)
    {
        $this->stats->removeElement($stat);
    }

    /**
     * Get stats
     *
     * @return ArrayCollection
     */
    public function getStats()
    {
        return $this->stats;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}