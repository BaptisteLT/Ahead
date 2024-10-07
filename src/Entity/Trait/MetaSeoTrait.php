<?php
namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait MetaSeoTrait{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $meta_image_url = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $meta_description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $meta_title = null;

    public function getMetaImageUrl(): ?string
    {
        return $this->meta_image_url;
    }

    public function setMetaImageUrl(?string $meta_image_url): self
    {
        $this->meta_image_url = $meta_image_url;
        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(?string $meta_description): self
    {
        $this->meta_description = $meta_description;
        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->meta_title;
    }

    public function setMetaTitle(?string $meta_title): self
    {
        $this->meta_title = $meta_title;
        return $this;
    }
}